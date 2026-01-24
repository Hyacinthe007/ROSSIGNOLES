<?php
declare(strict_types=1);

namespace App\Models;

use App\Helpers\Loggable;

/**
 * Modèle Facture
 * Gestion des factures avec journalisation automatique
 */

class Facture extends BaseModel {
    use Loggable;

    protected $table = 'factures';
    protected $fillable = [
        'numero_facture', 'eleve_id', 'annee_scolaire_id', 'type_facture_id',
        'date_facture', 'date_echeance', 'montant_total', 'montant_paye',
        'montant_restant', 'statut', 'description'
    ];

    /**
     * Crée une facture avec journalisation
     */
    public function create($data) {
        $factureId = parent::create($data);
        if ($factureId) {
            $this->logCreate('finance', 'facture', $factureId, [
                'numero' => $data['numero_facture'],
                'eleve_id' => $data['eleve_id'],
                'montant' => $data['montant_total']
            ]);
        }
        return $factureId;
    }

    /**
     * Met à jour une facture avec journalisation des changements de statut
     */
    public function update($id, $newData) {
        $oldData = $this->find($id);
        if (!$oldData) return false;

        $success = parent::update($id, $newData);
        if ($success) {
            if (isset($newData['statut']) && $newData['statut'] != $oldData['statut']) {
                $this->logActivity('update_statut', 'finance', 
                    "Changement de statut facture #{$oldData['numero_facture']}: {$oldData['statut']} → {$newData['statut']}",
                    'facture', $id
                );
            } else {
                $this->logUpdate('finance', 'facture', $id, $oldData, $newData);
            }
        }
        return $success;
    }

    /**
     * Supprime une facture (OPÉRATION CRITIQUE)
     */
    public function delete($id) {
        $facture = $this->find($id);
        if (!$facture) return false;

        $success = parent::delete($id);
        if ($success) {
            $this->logDelete('finance', 'facture', $id, [
                'numero' => $facture['numero_facture'],
                'eleve_id' => $facture['eleve_id'],
                'montant' => $facture['montant_total']
            ]);
        }
        return $success;
    }
    
    /**
     * Récupère toutes les factures avec les détails des élèves, types et années
     * @return array Liste des factures avec détails
     */
    public function getAllWithDetails() {
        return $this->query(
            "SELECT f.*, 
                    e.nom as eleve_nom, e.prenom as eleve_prenom, e.matricule,
                    tf.libelle as type_facture_libelle,
                    an.libelle as annee_scolaire_libelle
             FROM {$this->table} f
             LEFT JOIN eleves e ON f.eleve_id = e.id
             LEFT JOIN types_facture tf ON f.type_facture_id = tf.id
             LEFT JOIN annees_scolaires an ON f.annee_scolaire_id = an.id
             ORDER BY f.date_facture DESC"
        );
    }
    
    /**
     * Récupère les détails d'une facture avec toutes les relations
     * @param int $id ID de la facture
     * @return array|null Détails de la facture
     */
    public function getDetailsWithRelations($id) {
        return $this->queryOne(
            "SELECT f.*, 
                    e.nom as eleve_nom, e.prenom as eleve_prenom, e.matricule,
                    tf.libelle as type_facture_libelle,
                    an.libelle as annee_scolaire_libelle
             FROM {$this->table} f
             LEFT JOIN eleves e ON f.eleve_id = e.id
             LEFT JOIN types_facture tf ON f.type_facture_id = tf.id
             LEFT JOIN annees_scolaires an ON f.annee_scolaire_id = an.id
             WHERE f.id = ?",
            [$id]
        );
    }
    
    /**
     * Obtient les lignes de la facture
     */
    public function getLignes($factureId) {
        return $this->query(
            "SELECT lf.*, tf.libelle as type_frais
             FROM lignes_facture lf
             INNER JOIN types_frais tf ON lf.type_frais_id = tf.id
             WHERE lf.facture_id = ?",
            [$factureId]
        );
    }
    
    /**
     * Obtient les paiements de la facture
     */
    public function getPaiements($factureId) {
        return $this->query(
            "SELECT p.*, mp.libelle as mode_paiement
             FROM paiements p
             INNER JOIN modes_paiement mp ON p.mode_paiement_id = mp.id
             WHERE p.facture_id = ?
             ORDER BY p.date_paiement DESC",
            [$factureId]
        );
    }
    /**
     * Enregistre un paiement sur la facture et met à jour le montant payé et le statut
     * @param float $montant Montant du paiement
     * @return bool Succès de la mise à jour
     */
    public function registerPayment($id, $montant) {
        $facture = $this->find($id);
        if (!$facture) return false;

        $nouveauPaye = (float)$facture['montant_paye'] + (float)$montant;
        $nouveauRestant = max(0, (float)$facture['montant_total'] - $nouveauPaye);
        
        $statut = 'impayee';
        if ($nouveauPaye >= (float)$facture['montant_total']) {
            $statut = 'payee';
        } elseif ($nouveauPaye > 0) {
            $statut = 'partiellement_payee';
        }

        return $this->update($id, [
            'montant_paye' => $nouveauPaye,
            'montant_restant' => $nouveauRestant,
            'statut' => $statut
        ]);
    }
}
