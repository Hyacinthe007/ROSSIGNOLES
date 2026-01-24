<?php
declare(strict_types=1);

namespace App\Models;

/**
 * Modèle Annonce
 * Gestion des annonces scolaires (générales, urgentes, administratives, pédagogiques)
 */

class Annonce extends BaseModel {
    protected $table = 'annonces';
    protected $fillable = [
        'titre', 'contenu', 'type', 'cible', 'classe_id', 
        'annee_scolaire_id', 'date_debut', 'date_fin', 'publie_par', 'actif'
    ];
    
    /**
     * Récupère les annonces actives avec filtres optionnels
     * @param string|null $cible Cible de l'annonce (tous, enseignants, parents, eleves, classe)
     * @param int|null $classeId ID de la classe (si cible = classe)
     * @return array Liste des annonces actives
     */
    public function getActives($cible = null, $classeId = null) {
        $today = date('Y-m-d');
        $where = "a.actif = 1 AND a.date_debut <= ? AND a.date_fin >= ?";
        $params = [$today, $today];
        
        if ($cible) {
            $where .= " AND (a.cible = 'tous' OR a.cible = ?)";
            $params[] = $cible;
        }
        
        if ($classeId) {
            $where .= " AND (a.classe_id IS NULL OR a.classe_id = ?)";
            $params[] = $classeId;
        }
        
        return $this->query(
            "SELECT a.*, 
                    u.username as publie_par_username, 
                    u.nom as publie_par_nom,
                    u.prenom as publie_par_prenom,
                    c.nom as classe_nom
             FROM {$this->table} a
             LEFT JOIN users u ON a.publie_par = u.id
             LEFT JOIN classes c ON a.classe_id = c.id
             WHERE {$where}
             ORDER BY 
                CASE a.type 
                    WHEN 'urgente' THEN 1
                    WHEN 'administrative' THEN 2
                    WHEN 'pedagogique' THEN 3
                    ELSE 4
                END,
                a.created_at DESC",
            $params
        );
    }
    
    /**
     * Récupère les annonces pour une classe spécifique
     * @param int $classeId ID de la classe
     * @return array Liste des annonces
     */
    public function getByClasse($classeId) {
        $today = date('Y-m-d');
        
        return $this->query(
            "SELECT a.*, 
                    u.username as publie_par_username,
                    u.nom as publie_par_nom,
                    u.prenom as publie_par_prenom
             FROM {$this->table} a
             LEFT JOIN users u ON a.publie_par = u.id
             WHERE a.actif = 1 
             AND a.date_debut <= ? 
             AND a.date_fin >= ?
             AND (a.cible = 'tous' OR (a.cible = 'classe' AND a.classe_id = ?))
             ORDER BY a.created_at DESC",
            [$today, $today, $classeId]
        );
    }
    
    /**
     * Récupère les annonces urgentes actives
     * @return array Liste des annonces urgentes
     */
    public function getUrgentes() {
        $today = date('Y-m-d');
        
        return $this->query(
            "SELECT a.*, 
                    u.username as publie_par_username,
                    u.nom as publie_par_nom,
                    u.prenom as publie_par_prenom
             FROM {$this->table} a
             LEFT JOIN users u ON a.publie_par = u.id
             WHERE a.actif = 1 
             AND a.type = 'urgente'
             AND a.date_debut <= ? 
             AND a.date_fin >= ?
             ORDER BY a.created_at DESC",
            [$today, $today]
        );
    }
    
    /**
     * Récupère les annonces pour un utilisateur selon son rôle
     * @param string $userRole Rôle de l'utilisateur
     * @param int|null $classeId ID de la classe (pour les élèves/enseignants)
     * @return array Liste des annonces
     */
    public function getForUser($userRole, $classeId = null) {
        $today = date('Y-m-d');
        $sql = "SELECT a.*, 
                       u.username as publie_par_username,
                       u.nom as publie_par_nom,
                       u.prenom as publie_par_prenom,
                       c.nom as classe_nom
                FROM {$this->table} a
                LEFT JOIN users u ON a.publie_par = u.id
                LEFT JOIN classes c ON a.classe_id = c.id
                WHERE a.actif = 1 
                AND (a.date_debut <= ? OR a.date_debut IS NULL)
                AND (a.date_fin >= ? OR a.date_fin IS NULL)
                AND (a.cible = 'tous' OR a.cible = ?)";
        
        $params = [$today, $today, $userRole];
        
        if ($classeId) {
            $sql .= " OR (a.cible = 'classe' AND a.classe_id = ?)";
            $params[] = $classeId;
        }
        
        $sql .= " ORDER BY 
                    CASE a.type 
                        WHEN 'urgente' THEN 1
                        WHEN 'administrative' THEN 2
                        WHEN 'pedagogique' THEN 3
                        ELSE 4
                    END,
                    a.created_at DESC";
        
        return $this->query($sql, $params);
    }
    
    /**
     * Récupère toutes les annonces avec détails (pour l'administration)
     * @param array $filters Filtres optionnels (type, cible, actif)
     * @return array Liste des annonces
     */
    public function getAllWithDetails($filters = []) {
        $where = "1=1";
        $params = [];
        
        if (isset($filters['type'])) {
            $where .= " AND a.type = ?";
            $params[] = $filters['type'];
        }
        
        if (isset($filters['cible'])) {
            $where .= " AND a.cible = ?";
            $params[] = $filters['cible'];
        }
        
        if (isset($filters['actif'])) {
            $where .= " AND a.actif = ?";
            $params[] = $filters['actif'];
        }
        
        return $this->query(
            "SELECT a.*, 
                    u.username as publie_par_username,
                    u.nom as publie_par_nom,
                    u.prenom as publie_par_prenom,
                    c.nom as classe_nom,
                    ans.libelle as annee_scolaire_libelle
             FROM {$this->table} a
             LEFT JOIN users u ON a.publie_par = u.id
             LEFT JOIN classes c ON a.classe_id = c.id
             LEFT JOIN annees_scolaires ans ON a.annee_scolaire_id = ans.id
             WHERE {$where}
             ORDER BY a.created_at DESC",
            $params
        );
    }
    
    /**
     * Désactive une annonce
     * @param int $id ID de l'annonce
     * @return bool Succès de l'opération
     */
    public function desactiver($id) {
        return $this->update($id, ['actif' => 0]);
    }
    
    /**
     * Active une annonce
     * @param int $id ID de l'annonce
     * @return bool Succès de l'opération
     */
    public function activer($id) {
        return $this->update($id, ['actif' => 1]);
    }
    
    /**
     * Vérifie si une annonce est encore valide (dans sa période de validité)
     * @param int $id ID de l'annonce
     * @return bool True si l'annonce est valide
     */
    public function isValid($id) {
        $annonce = $this->find($id);
        if (!$annonce) return false;
        
        $today = date('Y-m-d');
        return $annonce['actif'] == 1 
            && $annonce['date_debut'] <= $today 
            && $annonce['date_fin'] >= $today;
    }
}
