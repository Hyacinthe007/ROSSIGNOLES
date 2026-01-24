<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Inscription;
use App\Models\Eleve;
use App\Models\AnneeScolaire;
use App\Models\BaseModel;
use App\Models\Classe;
use PDOException;

/**
 * Contrôleur Parcours Scolaires
 * Gère l'affichage des parcours scolaires des élèves
 */

class ParcoursController extends BaseController {
    
    private $inscriptionModel;
    private $eleveModel;
    private $anneeModel;
    
    public function __construct() {
        $this->requireAuth();
        $this->inscriptionModel = new Inscription();
        $this->eleveModel = new Eleve();
        $this->anneeModel = new AnneeScolaire();
    }
    
    /**
     * Liste des parcours scolaires
     */
    public function list() {
        // Récupérer les filtres
        $eleveId = $_GET['eleve_id'] ?? null;
        $classeId = $_GET['classe_id'] ?? null;
        $anneeId = $_GET['annee_id'] ?? null;
        
        // Récupérer l'année active par défaut
        if (!$anneeId) {
            $anneeActive = $this->anneeModel->getActive();
            $anneeId = $anneeActive ? $anneeActive['id'] : null;
        }
        
        // Récupérer les données pour les filtres
        try {
            $baseModel = new BaseModel();
            
            $eleves = $baseModel->query(
                "SELECT id, matricule, nom, prenom FROM eleves WHERE statut = 'actif' ORDER BY nom ASC, prenom ASC"
            );
            
            $annees = $baseModel->query(
                "SELECT id, libelle, date_debut, date_fin, actif FROM annees_scolaires ORDER BY date_debut DESC"
            );
            
            // Récupérer les classes
            $classes = $baseModel->query(
                "SELECT id, nom, code FROM classes WHERE statut = 'actif' ORDER BY nom ASC"
            );
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des filtres: " . $e->getMessage());
            $eleves = [];
            $annees = [];
            $classes = [];
        }
        
        // Construire la requête pour récupérer les parcours
        $parcours = [];
        
        try {
            $sql = "
                SELECT 
                    i.id,
                    i.eleve_id,
                    i.classe_id,
                    i.annee_scolaire_id,
                    i.date_inscription,
                    i.type_inscription,
                    i.statut_dossier,
                    i.statut,
                    e.matricule,
                    e.nom as eleve_nom,
                    e.prenom as eleve_prenom,
                    e.photo as eleve_photo,
                    c.nom as classe_nom,
                    c.code as classe_code,
                    n.libelle as niveau_nom,
                    s.libelle as serie_nom,
                    a.libelle as annee_libelle,
                    a.date_debut,
                    a.date_fin
                FROM inscriptions i
                INNER JOIN eleves e ON i.eleve_id = e.id
                INNER JOIN classes c ON i.classe_id = c.id
                INNER JOIN niveaux n ON c.niveau_id = n.id
                LEFT JOIN series s ON c.serie_id = s.id
                INNER JOIN annees_scolaires a ON i.annee_scolaire_id = a.id
                WHERE i.statut = 'validee'
            ";
            
            $params = [];
            
            if ($eleveId) {
                $sql .= " AND i.eleve_id = ?";
                $params[] = $eleveId;
            }
            
            if ($classeId) {
                $sql .= " AND i.classe_id = ?";
                $params[] = $classeId;
            }
            
            if ($anneeId) {
                $sql .= " AND i.annee_scolaire_id = ?";
                $params[] = $anneeId;
            }
            
            $sql .= " ORDER BY a.date_debut DESC, e.nom ASC, e.prenom ASC";
            
            $model = new BaseModel();
            $parcours = $model->query($sql, $params);
            
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des parcours: " . $e->getMessage());
            $parcours = [];
        }
        
        $this->view('parcours/list', [
            'parcours' => $parcours,
            'eleves' => $eleves,
            'classes' => $classes,
            'annees' => $annees,
            'eleveId' => $eleveId,
            'classeId' => $classeId,
            'anneeId' => $anneeId
        ]);
    }
    
    /**
     * Détails du parcours d'un élève
     */
    public function details($eleveId) {
        // Récupérer l'élève
        $eleve = $this->eleveModel->find($eleveId);
        
        if (!$eleve) {
            http_response_code(404);
            die("Élève non trouvé");
        }
        
        // Récupérer tout le parcours de l'élève
        try {
            $sql = "
                SELECT 
                    i.id,
                    i.classe_id,
                    i.annee_scolaire_id,
                    i.date_inscription,
                    i.type_inscription,
                    i.statut_dossier,
                    i.statut,
                    c.nom as classe_nom,
                    c.code as classe_code,
                    n.libelle as niveau_nom,
                    n.ordre as niveau_ordre,
                    s.libelle as serie_nom,
                    a.libelle as annee_libelle,
                    a.date_debut,
                    a.date_fin,
                    a.actif as annee_active,
                    pe.resultat,
                    pe.mention,
                    pe.rang_classe,
                    pe.effectif_classe,
                    cs.nom as classe_suivante_nom
                FROM inscriptions i
                INNER JOIN classes c ON i.classe_id = c.id
                INNER JOIN niveaux n ON c.niveau_id = n.id
                LEFT JOIN series s ON c.serie_id = s.id
                INNER JOIN annees_scolaires a ON i.annee_scolaire_id = a.id
                LEFT JOIN parcours_eleves pe ON i.eleve_id = pe.eleve_id AND i.annee_scolaire_id = pe.annee_scolaire_id
                LEFT JOIN classes cs ON pe.classe_suivante_id = cs.id
                WHERE i.eleve_id = ? AND i.statut = 'validee'
                ORDER BY a.date_debut DESC
            ";
            
            $model = new BaseModel();
            $parcours = $model->query($sql, [$eleveId]);
            
            // Récupérer les statistiques si disponibles (bulletins)
            $statistiques = [];
            foreach ($parcours as &$p) {
                $statsSql = "
                    SELECT 
                        AVG(moyenne_generale) as moyenne_annuelle,
                        COUNT(*) as nb_periodes
                    FROM bulletins
                    WHERE eleve_id = ? 
                    AND annee_scolaire_id = ?
                    AND statut IN ('valide', 'imprime', 'envoye')
                ";
                
                $stats = $model->queryOne($statsSql, [$eleveId, $p['annee_scolaire_id']]);
                $p['moyenne_annuelle'] = $stats['moyenne_annuelle'] ?? null;
                $p['nb_periodes'] = $stats['nb_periodes'] ?? 0;
            }
            
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération du parcours: " . $e->getMessage());
            $parcours = [];
        }
        
        $this->view('parcours/details', [
            'eleve' => $eleve,
            'parcours' => $parcours
        ]);
    }
}
