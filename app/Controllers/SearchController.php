<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\BaseModel;

/**
 * Contrôleur pour la recherche globale intelligente
 */
class SearchController extends BaseController {

    public function __construct() {
        $this->requireAuth();
    }

    /**
     * Recherche globale (AJAX)
     */
    public function global() {
        $query = $_GET['q'] ?? '';
        
        if (strlen($query) < 2) {
            return $this->json([]);
        }

        $baseModel = new BaseModel();
        $results = [
            'eleves' => [],
            'parents' => [],
            'personnel' => []
        ];

        // 1. Recherche des ÉLÈVES
        $eleves = $baseModel->query(
            "SELECT id, matricule, nom, prenom, photo, 'eleve' as type 
             FROM eleves 
             WHERE (nom LIKE ? OR prenom LIKE ? OR matricule LIKE ?) 
             AND statut != 'supprime' 
             LIMIT 5",
            ["%$query%", "%$query%", "%$query%"]
        );
        foreach ($eleves as $e) {
            $results['eleves'][] = [
                'id' => $e['id'],
                'title' => $e['nom'] . ' ' . $e['prenom'],
                'subtitle' => 'Élève - ' . $e['matricule'],
                'url' => url('eleves/details/' . $e['id']),
                'type' => 'élève',
                'icon' => 'fas fa-user-graduate',
                'photo' => $e['photo']
            ];
        }

        // 2. Recherche des PARENTS
        $parents = $baseModel->query(
            "SELECT id, nom, prenom, telephone, 'parent' as type 
             FROM parents 
             WHERE (nom LIKE ? OR prenom LIKE ? OR telephone LIKE ?) 
             LIMIT 5",
            ["%$query%", "%$query%", "%$query%"]
        );
        foreach ($parents as $p) {
            $results['parents'][] = [
                'id' => $p['id'],
                'title' => $p['nom'] . ' ' . $p['prenom'],
                'subtitle' => 'Parent - ' . ($p['telephone'] ?? 'N/A'),
                'url' => url('parents/details/' . $p['id']),
                'type' => 'parent',
                'icon' => 'fas fa-user-friends',
                'photo' => null
            ];
        }

        // 3. Recherche du PERSONNEL (Enseignants et Administratifs)
        $personnels = $baseModel->query(
            "SELECT id, nom, prenom, type_personnel, photo, 'personnel' as type 
             FROM personnels 
             WHERE (nom LIKE ? OR prenom LIKE ?) 
             AND deleted_at IS NULL 
             LIMIT 5",
            ["%$query%", "%$query%"]
        );
        foreach ($personnels as $pers) {
            $isEnseignant = $pers['type_personnel'] === 'enseignant';
            $results['personnel'][] = [
                'id' => $pers['id'],
                'title' => $pers['nom'] . ' ' . $pers['prenom'],
                'subtitle' => $isEnseignant ? 'Enseignant' : 'Personnel Administratif',
                'url' => $isEnseignant ? url('enseignants/details/' . $pers['id']) : url('personnel/details/' . $pers['id']),
                'type' => $isEnseignant ? 'enseignant' : 'personnel',
                'icon' => $isEnseignant ? 'fas fa-chalkboard-teacher' : 'fas fa-user-tie',
                'photo' => $pers['photo']
            ];
        }

        return $this->json($results);
    }
}
