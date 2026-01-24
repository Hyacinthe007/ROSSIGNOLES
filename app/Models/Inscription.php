<?php
/**
 * Modèle Inscription
 * Gère les inscriptions et réinscriptions avec journalisation automatique
 */

require_once __DIR__ . '/BaseModel.php';
require_once __DIR__ . '/../Helpers/Loggable.php';

class Inscription extends BaseModel {
    use Loggable;
    
    protected $table = 'inscriptions';
    protected $fillable = [
        'eleve_id', 'classe_id', 'annee_scolaire_id', 'date_inscription', 
        'type_inscription', 'facture_inscription_id', 'statut_dossier', 
        'statut', 'valide_par', 'date_validation', 'motif_rejet', 'commentaire_interne',
        'frais_inscription_paye', 'premier_mois_ecolage_paye', 'bloquee', 'date_deblocage'
    ];
    
    /**
     * Met à jour une inscription avec journalisation des changements de statut
     * @param int $id ID de l'inscription
     * @param array $newData Nouvelles données
     * @return bool Succès de l'opération
     */
    public function update($id, $newData) {
        // Récupérer l'ancien statut
        $oldData = $this->find($id);
        
        if (!$oldData) {
            return false;
        }
        
        $success = parent::update($id, $newData);
        
        // Logger les changements de statut
        if ($success && isset($newData['statut_dossier']) && $oldData['statut_dossier'] != $newData['statut_dossier']) {
            $this->logInscriptionStatusChange(
                $id,
                $oldData['eleve_id'],
                $oldData['statut_dossier'],
                $newData['statut_dossier']
            );
        }
        
        return $success;
    }
    
    /**
     * Obtient les détails d'une inscription avec les informations liées
     */
    public function getDetails($id) {
        return $this->queryOne(
            "SELECT i.*, 
                    e.matricule as eleve_matricule, e.nom as eleve_nom, e.prenom as eleve_prenom,
                    e.sexe as eleve_sexe, e.date_naissance as eleve_date_naissance,
                    c.nom as classe_nom, c.code as classe_code,
                    a.libelle as annee_scolaire,
                    f.numero_facture, f.montant_total as facture_montant, f.montant_paye as facture_paye, f.statut as facture_statut,
                    mp.libelle as mode_paiement
             FROM {$this->table} i
             INNER JOIN eleves e ON i.eleve_id = e.id
             INNER JOIN classes c ON i.classe_id = c.id
             INNER JOIN annees_scolaires a ON i.annee_scolaire_id = a.id
             LEFT JOIN factures f ON i.facture_inscription_id = f.id
             LEFT JOIN paiements p ON f.id = p.facture_id
             LEFT JOIN modes_paiement mp ON p.mode_paiement_id = mp.id
             WHERE i.id = ?
             LIMIT 1",
            [$id]
        );
    }
    
    /**
     * Obtient toutes les inscriptions avec filtres
     */
    public function getAllWithDetails($filters = [], $orderBy = 'i.date_inscription DESC') {
        $sql = "SELECT i.*,
                       e.matricule as eleve_matricule, e.nom as eleve_nom, e.prenom as eleve_prenom,
                       c.nom as classe_nom, c.code as classe_code,
                       a.libelle as annee_scolaire,
                       f.statut as facture_statut,
                       f.montant_total, f.montant_paye
                FROM {$this->table} i
                INNER JOIN eleves e ON i.eleve_id = e.id
                INNER JOIN classes c ON i.classe_id = c.id
                INNER JOIN annees_scolaires a ON i.annee_scolaire_id = a.id
                LEFT JOIN factures f ON i.facture_inscription_id = f.id
                WHERE 1=1";
        
        $params = [];
        
        if (isset($filters['type_inscription'])) {
            $sql .= " AND i.type_inscription = ?";
            $params[] = $filters['type_inscription'];
        }
        
        if (isset($filters['eleve_id'])) {
            $sql .= " AND i.eleve_id = ?";
            $params[] = $filters['eleve_id'];
        }
        
        if (isset($filters['classe_id'])) {
            $sql .= " AND i.classe_id = ?";
            $params[] = $filters['classe_id'];
        }
        
        if (isset($filters['annee_scolaire_id'])) {
            $sql .= " AND i.annee_scolaire_id = ?";
            $params[] = $filters['annee_scolaire_id'];
        }
        
        if (isset($filters['statut'])) {
            $sql .= " AND i.statut = ?";
            $params[] = $filters['statut'];
        }
        
        $sql .= " ORDER BY " . $orderBy;
        
        return $this->query($sql, $params);
    }

    /**
     * Crée une nouvelle inscription avec génération de facture et paiement initial
     */
    public function creerInscription($data, $paiementInitial = null, $userId = null) {
        try {
            // Démarrer une transaction
            $this->db->beginTransaction();
            
            // 1. Créer la Facture d'Inscription (comprend Droit d'inscription + Écolage 1er mois si applicable)
            require_once __DIR__ . '/Facture.php';
            require_once __DIR__ . '/LigneFacture.php'; // Important: model for invoice lines
            require_once __DIR__ . '/TypeFacture.php';
            require_once __DIR__ . '/TypeFrais.php';

            $factureModel = new Facture();
            $ligneFactureModel = new LigneFacture();
            $typeFactureModel = new TypeFacture();
            $typeFraisModel = new TypeFrais();

            // Trouver le type de facture "INSCRIPTION" ou le créer si inexistant
            $typeFacture = $typeFactureModel->queryOne("SELECT id FROM types_facture WHERE code = 'INSCRIPTION' LIMIT 1");
            if ($typeFacture) {
                $typeFactureId = $typeFacture['id'];
            } else {
                // Création automatique du type de facture INSCRIPTION
                $typeFactureId = $typeFactureModel->create([
                    'code'           => 'INSCRIPTION',
                    'libelle'        => 'Inscription',
                    'description'    => 'Facturation des droits d\'inscription et premier mois d\'écolage',
                    'prefixe_numero' => 'INS',
                    'actif'          => 1,
                ]);
            }

            // Calcul du montant total de la facture
            $montantDroit = $data['frais_inscription_montant'] ?? 0;
            $montantEcolage = $data['premier_mois_ecolage_montant'] ?? 0;
            $totalFacture = $montantDroit + $montantEcolage;
            
            $paiementMontant = ($paiementInitial['montant'] ?? 0);

            // Création de la facture
            $factureData = [
                'numero_facture' => 'INS-' . date('Ymd') . '-' . uniqid(), // A améliorer avec compteur
                'eleve_id' => $data['eleve_id'],
                'annee_scolaire_id' => $data['annee_scolaire_id'],
                'type_facture_id' => $typeFactureId,
                'date_facture' => date('Y-m-d'),
                'montant_total' => $totalFacture,
                'montant_paye' => $paiementMontant,
                'montant_restant' => $totalFacture - $paiementMontant,
                'statut' => ($paiementMontant >= $totalFacture) ? 'payee' : (($paiementMontant > 0) ? 'partiellement_payee' : 'impayee'),
                'description' => "Frais d'inscription"
            ];
            
            $factureId = $factureModel->create($factureData);

            // Création des lignes de facture
            // 1) Obtenir ou créer les types de frais nécessaires
            // Type frais pour le droit d'inscription (categorie = 'inscription')
            $typeFraisInscription = $typeFraisModel->queryOne(
                "SELECT id FROM types_frais WHERE categorie = 'inscription' LIMIT 1"
            );
            if ($typeFraisInscription) {
                $typeFraisInscriptionId = $typeFraisInscription['id'];
            } else {
                $typeFraisInscriptionId = $typeFraisModel->create([
                    'libelle'   => "Droit d'inscription",
                    'categorie' => 'inscription',
                    'actif'     => 1,
                ]);
            }

            // Type frais pour l'écolage (categorie = 'scolarite')
            $typeFraisEcolage = $typeFraisModel->queryOne(
                "SELECT id FROM types_frais WHERE categorie = 'scolarite' LIMIT 1"
            );
            if ($typeFraisEcolage) {
                $typeFraisEcolageId = $typeFraisEcolage['id'];
            } else {
                $typeFraisEcolageId = $typeFraisModel->create([
                    'libelle'   => "Écolage mensuel",
                    'categorie' => 'scolarite',
                    'actif'     => 1,
                ]);
            }

            // 2) Lignes de facture
            // Ligne Droit d'inscription
            if ($montantDroit > 0) {
                $ligneFactureModel->create([
                    'facture_id'    => $factureId,
                    'type_frais_id' => $typeFraisInscriptionId,
                    'designation'   => "Droit d'inscription",
                    'quantite'      => 1,
                    'prix_unitaire' => $montantDroit,
                    'montant'       => $montantDroit,
                ]);
            }
            
            // Lignes Écolage - Une ligne par mois
            if ($montantEcolage > 0) {
                // Récupérer le nombre de mois et le mois de début
                $nombreMois = $data['nombre_mois'] ?? 1;
                
                // Récupérer le mois de début depuis la classe/tarif
                require_once __DIR__ . '/Classe.php';
                require_once __DIR__ . '/TarifInscription.php';
                $classeModel = new Classe();
                $tarifModel = new TarifInscription();
                
                $classe = $classeModel->find($data['classe_id']);
                $tarif = $tarifModel->queryOne(
                    "SELECT mois_debut_annee FROM tarifs_inscription WHERE niveau_id = ? AND annee_scolaire_id = ? AND actif = 1 LIMIT 1",
                    [$classe['niveau_id'], $data['annee_scolaire_id']]
                );
                
                $moisDebut = $tarif['mois_debut_annee'] ?? 9; // Par défaut Septembre (9)
                $montantParMois = $montantEcolage / $nombreMois;
                
                // Noms des mois en français
                $nomsMois = [
                    1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril', 
                    5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
                    9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
                ];
                
                $anneeActuelle = date('Y');
                
                // Créer une ligne pour chaque mois
                for ($i = 0; $i < $nombreMois; $i++) {
                    $moisCourant = $moisDebut + $i;
                    $anneeMois = $anneeActuelle;
                    
                    // Gérer le passage à l'année suivante
                    if ($moisCourant > 12) {
                        $moisCourant = $moisCourant - 12;
                        $anneeMois = $anneeActuelle + 1;
                    }
                    
                    $nomMois = $nomsMois[$moisCourant] ?? 'Mois ' . $moisCourant;
                    $designation = "Écolage " . $nomMois . " " . $anneeMois;
                    
                    $ligneFactureModel->create([
                        'facture_id'    => $factureId,
                        'type_frais_id' => $typeFraisEcolageId,
                        'designation'   => $designation,
                        'quantite'      => 1,
                        'prix_unitaire' => $montantParMois,
                        'montant'       => $montantParMois,
                    ]);
                }
            }

            // 2. Créer l'Inscription
            // Nettoyer $data pour ne garder que les champs de 'inscriptions'
            $inscriptionData = [
                'eleve_id' => $data['eleve_id'],
                'classe_id' => $data['classe_id'],
                'annee_scolaire_id' => $data['annee_scolaire_id'],
                'date_inscription' => $data['date_inscription'] ?? date('Y-m-d'),
                'type_inscription' => $data['type_inscription'],
                'facture_inscription_id' => $factureId,
                'statut_dossier' => ($paiementInitial !== null && $paiementMontant >= $totalFacture) ? 'validee' : 'en_attente',
                'statut' => ($paiementInitial !== null && $paiementMontant >= $totalFacture) ? 'validee' : 'brouillon', // Statut initial en brouillon
                'frais_inscription_paye' => ($paiementMontant >= $montantDroit) ? 1 : 0,
                'premier_mois_ecolage_paye' => ($paiementMontant >= ($montantDroit + $montantEcolage)) ? 1 : 0,
                'bloquee' => ($paiementMontant >= ($montantDroit + $montantEcolage)) ? 0 : 1,
                'commentaire_interne' => $data['commentaire'] ?? null
            ];

            $inscriptionId = $this->create($inscriptionData);

            // 3. Enregistrer le Paiement
            if ($paiementMontant > 0) {
                require_once __DIR__ . '/Paiement.php';
                $paiementModel = new Paiement();
                
                $paiementModel->create([
                    'numero_paiement' => 'PAY-' . date('Ymd') . '-' . uniqid(),
                    'facture_id' => $factureId,
                    'date_paiement' => $paiementInitial['date_paiement'] ?? date('Y-m-d'),
                    'montant' => $paiementMontant,
                    'mode_paiement_id' => $paiementInitial['mode_paiement'] ?? 1,
                    'reference_paiement' => $paiementInitial['reference_externe'] ?? null,
                    'remarque' => $paiementInitial['commentaire'] ?? "Paiement inscription"
                ]);
            }

            if ($inscriptionData['statut'] === 'validee') {
                require_once __DIR__ . '/../Services/EcheancierService.php';
                $echeancierService = new EcheancierService();
                
                try {
                    $resultatEcheancier = $echeancierService->genererEcheancierInscription($inscriptionId, $userId);
                    
                    if (!$resultatEcheancier['success']) {
                        // Log l'erreur mais ne bloque pas l'inscription
                        error_log("Erreur génération échéancier pour inscription #{$inscriptionId}: " . $resultatEcheancier['message']);
                    } else {
                        // Si l'écolage du premier mois a été payé, il faut l'imputer sur l'échéancier
                        if ($montantEcolage > 0 && $paiementMontant >= ($montantDroit + $montantEcolage)) {
                            // On impute uniquement la partie écolage sur l'échéancier
                            // Le reste (droit inscription) ne concerne pas l'échéancier d'écolage
                            $echeancierService->enregistrerPaiement(
                                $data['eleve_id'], 
                                $data['annee_scolaire_id'], 
                                $montantEcolage, 
                                $factureId
                            );
                        }
                    }
                } catch (Exception $e) {
                    // Log l'erreur mais ne bloque pas l'inscription
                    error_log("Exception génération échéancier: " . $e->getMessage());
                }
            }

            $this->db->commit();
            return $inscriptionId;

        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Valide la réinscription (Règles métier)
     */
    public function validateReinscription($eleveId, $anneeScolaireActuelleId, $inscriptionPrecedenteId = null) {
        // Vérifier si l'élève a déjà une inscription pour l'année scolaire actuelle
        $inscriptionMemeAnnee = $this->queryOne(
            "SELECT id FROM {$this->table} WHERE eleve_id = ? AND annee_scolaire_id = ? LIMIT 1",
            [$eleveId, $anneeScolaireActuelleId]
        );
        
        if ($inscriptionMemeAnnee) {
            return ['valid' => false, 'message' => 'Cet élève est déjà inscrit pour cette année scolaire.'];
        }
        
        return ['valid' => true];
    }

    /**
     * Vérifie la cohérence année classe/inscription
     */
    public function validateCoherenceAnnee($classeId, $anneeScolaireId) {
        $classe = $this->queryOne("SELECT annee_scolaire_id FROM classes WHERE id = ?", [$classeId]);
        if (!$classe) return ['valid' => false, 'message' => 'Classe introuvable'];
        
        if ($classe['annee_scolaire_id'] != $anneeScolaireId) {
            return ['valid' => false, 'message' => 'La classe n\'appartient pas à l\'année scolaire sélectionnée'];
        }
        return ['valid' => true];
    }
    
    /**
     * Vérifie si inscription active existe
     */
    public function hasActiveInscription($eleveId, $anneeScolaireId) {
        $res = $this->queryOne(
            "SELECT id FROM {$this->table} WHERE eleve_id = ? AND annee_scolaire_id = ? AND statut = 'validee'", 
            [$eleveId, $anneeScolaireId]
        );
        return $res ? true : false;
    }

    /**
     * Finalise une inscription existante avec un paiement
     */
    public function finaliserInscription($inscriptionId, $data, $paiementInitial = null, $userId = null) {
        $inscription = $this->find($inscriptionId);
        if (!$inscription) throw new Exception("Inscription introuvable");
        
        $this->db->beginTransaction();
        try {
            if ($paiementInitial && $paiementInitial['montant'] > 0) {
                require_once __DIR__ . '/Paiement.php';
                require_once __DIR__ . '/Facture.php';
                $paiementModel = new Paiement();
                $factureModel = new Facture();
                
                $factureId = $inscription['facture_inscription_id'];
                $facture = $factureModel->find($factureId);

                // Synchronisation du nombre de mois d'écolage (si modifié à l'étape finale)
                $nombreMois = $data['nombre_mois'] ?? 1;
                require_once __DIR__ . '/LigneFacture.php';
                require_once __DIR__ . '/TypeFrais.php';
                $ligneFactureModel = new LigneFacture();
                $typeFraisModel = new TypeFrais();
                $typeFraisEcolage = $typeFraisModel->queryOne("SELECT id FROM types_frais WHERE categorie = 'scolarite' LIMIT 1");
                
                if ($typeFraisEcolage) {
                    $typeFraisEcolageId = $typeFraisEcolage['id'];
                    $existingEcolageLines = $ligneFactureModel->query("SELECT id FROM lignes_facture WHERE facture_id = ? AND type_frais_id = ?", [$factureId, $typeFraisEcolageId]);
                    
                    if (count($existingEcolageLines) != $nombreMois) {
                        $ligneFactureModel->execute("DELETE FROM lignes_facture WHERE facture_id = ? AND type_frais_id = ?", [$factureId, $typeFraisEcolageId]);
                        
                        require_once __DIR__ . '/Classe.php';
                        require_once __DIR__ . '/TarifInscription.php';
                        $classeModel = new Classe();
                        $tarifModel = new TarifInscription();
                        $classe = $classeModel->find($inscription['classe_id']);
                        $tarif = $tarifModel->queryOne("SELECT mois_debut_annee, ecolage_mensuel FROM tarifs_inscription WHERE niveau_id = ? AND annee_scolaire_id = ? AND actif = 1 LIMIT 1", [$classe['niveau_id'], $inscription['annee_scolaire_id']]);
                        
                        $moisDebut = $tarif['mois_debut_annee'] ?? 9;
                        $montantParMois = $tarif['ecolage_mensuel'] ?? 0;
                        $nomsMois = [1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril', 5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août', 9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'];
                        $anneeActuelle = date('Y', strtotime($inscription['date_inscription']));
                        
                        for ($i = 0; $i < $nombreMois; $i++) {
                            $moisCourant = $moisDebut + $i;
                            $anneeMois = $anneeActuelle;
                            if ($moisCourant > 12) { $moisCourant -= 12; $anneeMois++; }
                            $nomMois = $nomsMois[$moisCourant] ?? 'Mois ' . $moisCourant;
                            $ligneFactureModel->create([
                                'facture_id' => $factureId,
                                'type_frais_id' => $typeFraisEcolageId,
                                'designation' => "Écolage $nomMois $anneeMois",
                                'quantite' => 1,
                                'prix_unitaire' => $montantParMois,
                                'montant' => $montantParMois
                            ]);
                        }
                        // Mettre à jour le total de base de la facture
                        $facture['montant_total'] = ($data['frais_inscription_montant'] ?? 0) + ($montantParMois * $nombreMois);
                        $factureModel->update($factureId, ['montant_total' => $facture['montant_total']]);
                    }
                }
                
                // Gérer les articles optionnels
                $articlesOptionnels = $data['articles_optionnels'] ?? [];
                $montantTotalArticles = 0;
                if (!empty($articlesOptionnels)) {
                    require_once __DIR__ . '/Article.php';
                    require_once __DIR__ . '/InscriptionArticle.php';
                    require_once __DIR__ . '/LigneFacture.php';
                    require_once __DIR__ . '/TypeFrais.php';
                    
                    $articleModel = new Article();
                    $insArticleModel = new InscriptionArticle();
                    $ligneFactureModel = new LigneFacture();
                    $typeFraisModel = new TypeFrais();
                    
                    // Trouver ou créer le type de frais "ARTICLES"
                    $typeFraisArticle = $typeFraisModel->queryOne("SELECT id FROM types_frais WHERE categorie = 'article' LIMIT 1");
                    if (!$typeFraisArticle) {
                        $typeFraisArticleId = $typeFraisModel->create([
                            'libelle' => 'Articles scolaires',
                            'categorie' => 'article',
                            'actif' => 1
                        ]);
                    } else {
                        $typeFraisArticleId = $typeFraisArticle['id'];
                    }
                    
                    $anneeId = $inscription['annee_scolaire_id'];
                    
                    foreach ($articlesOptionnels as $articleId) {
                        $articleTarif = $articleModel->getWithTarif($articleId, $anneeId);
                        if ($articleTarif && !empty($articleTarif['prix_unitaire'])) {
                            $prix = $articleTarif['prix_unitaire'];
                            $montantTotalArticles += $prix;
                            
                            // 1. Ajouter à lignes_facture
                            $ligneFactureModel->create([
                                'facture_id' => $factureId,
                                'type_frais_id' => $typeFraisArticleId,
                                'designation' => $articleTarif['libelle'],
                                'quantite' => 1,
                                'prix_unitaire' => $prix,
                                'montant' => $prix
                            ]);
                            
                            // 2. Ajouter à inscriptions_articles
                            $insArticleModel->addToInscription($inscriptionId, $articleId, $prix, 1, ($paiementInitial['montant'] >= ($facture['montant_total'] + $montantTotalArticles)));
                        }
                    }
                    
                    // Mettre à jour le montant total de la facture avec les articles
                    $nouveauTotalFacture = $facture['montant_total'] + $montantTotalArticles;
                    $factureModel->update($factureId, [
                        'montant_total' => $nouveauTotalFacture,
                        'montant_restant' => $nouveauTotalFacture - ($facture['montant_paye'] + $paiementInitial['montant'])
                    ]);
                    
                    // Re-récupérer la facture pour la suite de la logique
                    $facture = $factureModel->find($factureId);
                }

                // Séparer le paiement en records si nécessaire
                $montantDroit = $paiementInitial['paiement_droit_inscription'] ?? 0;
                $montantEcolage = $paiementInitial['paiement_premier_mois'] ?? 0;
                
                if ($montantDroit > 0) {
                    $paiementModel->create([
                        'numero_paiement' => 'PAY-' . date('Ymd') . '-' . uniqid() . '-D',
                        'facture_id' => $factureId,
                        'date_paiement' => date('Y-m-d'),
                        'montant' => $montantDroit,
                        'mode_paiement_id' => $paiementInitial['mode_paiement'],
                        'reference_paiement' => $paiementInitial['reference_externe'],
                        'remarque' => "Droit d'inscription"
                    ]);
                }
                
                if ($montantEcolage > 0) {
                    $paiementModel->create([
                        'numero_paiement' => 'PAY-' . date('Ymd') . '-' . uniqid() . '-E',
                        'facture_id' => $factureId,
                        'date_paiement' => date('Y-m-d'),
                        'montant' => $montantEcolage,
                        'mode_paiement_id' => $paiementInitial['mode_paiement'],
                        'reference_paiement' => $paiementInitial['reference_externe'],
                        'remarque' => "Écolage 1er mois"
                    ]);
                }

                // Paiement des articles si le montant payé le permet
                // On considère que si le montant total payé couvre les articles, on crée un record
                if ($montantTotalArticles > 0 && ($paiementInitial['montant'] > ($montantDroit + $montantEcolage))) {
                    $restePortionArticles = $paiementInitial['montant'] - ($montantDroit + $montantEcolage);
                    $paiementModel->create([
                        'numero_paiement' => 'PAY-' . date('Ymd') . '-' . uniqid() . '-A',
                        'facture_id' => $factureId,
                        'date_paiement' => date('Y-m-d'),
                        'montant' => min($restePortionArticles, $montantTotalArticles),
                        'mode_paiement_id' => $paiementInitial['mode_paiement'],
                        'reference_paiement' => $paiementInitial['reference_externe'],
                        'remarque' => "Articles scolaires"
                    ]);
                }
                
                // Si le montant total est fourni par erreur sans détail (fallback)
                if ($montantDroit <= 0 && $montantEcolage <= 0 && $paiementInitial['montant'] > 0) {
                    $paiementModel->create([
                        'numero_paiement' => 'PAY-' . date('Ymd') . '-' . uniqid(),
                        'facture_id' => $factureId,
                        'date_paiement' => date('Y-m-d'),
                        'montant' => $paiementInitial['montant'],
                        'mode_paiement_id' => $paiementInitial['mode_paiement'],
                        'reference_paiement' => $paiementInitial['reference_externe'],
                        'remarque' => $paiementInitial['commentaire'] ?? "Paiement inscription finalisé"
                    ]);
                }

                // Mettre à jour la facture (paiement déjà effectué)
                $nouveauPaye = $facture['montant_paye'] + $paiementInitial['montant'];
                $statutFacture = ($nouveauPaye >= $facture['montant_total']) ? 'payee' : 'partiellement_payee';
                
                $factureModel->update($factureId, [
                    'montant_paye' => $nouveauPaye,
                    'montant_restant' => $facture['montant_total'] - $nouveauPaye,
                    'statut' => $statutFacture
                ]);
                
                // Mettre à jour l'inscription : règle stricte (Droit + 1er mois payé)
                // Note: Les articles optionnels ne bloquent pas l'inscription en théorie, 
                // mais ici on suit la logique de la facture totale.
                $isPayeTotalement = ($nouveauPaye >= $facture['montant_total']);
                $this->update($inscriptionId, [
                    'statut_dossier' => $isPayeTotalement ? 'validee' : 'paiement_ecolage_attente',
                    'statut' => $isPayeTotalement ? 'validee' : 'en_attente',
                    'bloquee' => $isPayeTotalement ? 0 : 1
                ]);

                // Si les articles ont été payés, on les marque dans inscriptions_articles
                if ($montantTotalArticles > 0 && $nouveauPaye >= ($montantDroit + $montantEcolage + $montantTotalArticles)) {
                    $insArticleModel = new InscriptionArticle();
                    $insArticleModel->markAsPaid($inscriptionId, $factureId);
                }

                // Générer l'échéancier si nécessaire
                if (!isset($inscription['echeancier_genere']) || !$inscription['echeancier_genere']) {
                    require_once __DIR__ . '/../Services/EcheancierService.php';
                    $echeancierService = new EcheancierService();
                    $echeancierService->genererEcheancierInscription($inscriptionId, $userId);
                    
                    // Imputer le paiement écolage sur l'échéancier
                    $montantEcolage = $data['premier_mois_ecolage_montant'] ?? 0;
                    if (isset($paiementInitial['paiement_premier_mois']) && $paiementInitial['paiement_premier_mois'] > 0) {
                        $echeancierService->enregistrerPaiement(
                            $inscription['eleve_id'], 
                            $inscription['annee_scolaire_id'], 
                            $paiementInitial['paiement_premier_mois'], 
                            $factureId
                        );
                    }
                }
            }
            
            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
