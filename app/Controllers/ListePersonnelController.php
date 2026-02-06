<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Personnel;

class ListePersonnelController extends BaseController {
    
    private $personnelModel;
    
    public function __construct() {
        $this->personnelModel = new Personnel();
    }
    
    private function getCombinedList() {
        // Obtenir tous les personnels actifs
        $allPersonnel = $this->personnelModel->all(['statut' => 'actif']);
        
        $combinedList = [];
        
        foreach ($allPersonnel as $pers) {
            $type = $pers['type_personnel'] ?? 'autre';
            $matricule = $pers['matricule'] ?? '';
            $telephone = $pers['telephone'] ?? '';
            
            // 1°/ Formater la colonne fonction : ENS => Enseignant, sinon => Administration
            $fonction = (strpos(strtoupper($matricule), 'ENS') !== false) ? 'Enseignant' : 'Administration';

            // 2°/ Formater le téléphone en 03X XX XXX XX
            $formattedPhone = $telephone;
            $digits = preg_replace('/\D/', '', $telephone);
            if (strlen($digits) === 10) {
                $formattedPhone = substr($digits, 0, 3) . ' ' . substr($digits, 3, 2) . ' ' . substr($digits, 5, 3) . ' ' . substr($digits, 8, 2);
            }

            $item = [
                'id' => $pers['id'],
                'matricule' => $matricule,
                'nom' => $pers['nom'],
                'prenom' => $pers['prenom'],
                'sexe' => $pers['sexe'],
                'photo' => $pers['photo'] ?? '',
                'telephone' => $formattedPhone,
                'email' => $pers['email'] ?? '',
                'statut' => ucfirst($pers['statut'] ?? 'Inactif'),
                'type_raw' => $type,
                'type' => ($type === 'enseignant') ? 'enseignants' : 'personnel',
                'fonction' => $fonction
            ];
            
            $combinedList[] = $item;
        }
        
        return $combinedList;
    }

    public function index() {
        $combinedList = $this->getCombinedList();
        
        $this->view('liste_personnel/index', [
            'list' => $combinedList,
            'title' => 'Liste du Personnel'
        ]);
    }
    
    public function search() {
        $query = $_GET['q'] ?? '';
        
        // Recherche dans la table unifiée personnels
        $resultsRaw = $this->personnelModel->query(
            "SELECT * FROM personnels 
             WHERE (nom LIKE ? OR prenom LIKE ? OR matricule LIKE ?) 
             AND statut = 'actif' 
             LIMIT 20", 
            ["%$query%", "%$query%", "%$query%"]
        );
        
        $results = [];
        
        foreach ($resultsRaw as $p) {
            $labelType = ($p['type_personnel'] === 'enseignant') ? '(Enseignant)' : '(Admin)';
            $results[] = [
                'label' => $p['nom'] . ' ' . $p['prenom'] . ' ' . $labelType,
                'value' => $p['id'],
                'type' => $p['type_personnel'] // 'enseignant' ou 'administratif'
            ];
        }
        
        header('Content-Type: application/json');
        echo json_encode($results);
    }
    
    public function exportExcel() {
        $filename = "liste_personnel_" . date('Y-m-d') . ".csv";
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);
        
        $output = fopen('php://output', 'w');
        
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM for Excel
        
        fputcsv($output, ['Matricule', 'Nom', 'Prénom', 'Sexe', 'Fonction', 'Téléphone', 'Email', 'Statut'], ';');
        
        $list = $this->getCombinedList();
        
        foreach ($list as $p) {
            fputcsv($output, [
                $p['matricule'],
                $p['nom'],
                $p['prenom'],
                $p['sexe'],
                $p['fonction'],
                $p['telephone'],
                $p['email'],
                $p['statut']
            ], ';');
        }
        
        fclose($output);
    }

    public function exportPdf() {
        $list = $this->getCombinedList();
          
        $html = '
        <html>
        <head>
           <link rel="preconnect" href="https://fonts.googleapis.com">
           <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
           <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
           <style>
              body { font-family: \'Outfit\', sans-serif; }
              table { width: 100%; border-collapse: collapse; margin-top: 20px; }
              th, td { border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 12px; }
              th { background-color: #f2f2f2; }
              h1 { text-align: center; color: #333; }
              .date { text-align: right; font-size: 11px; margin-bottom: 20px; }
           </style>
        </head>
        <body>
           <div class="date">Date: ' . date('d/m/Y H:i') . '</div>
           <h1>Liste du Personnel</h1>
           <table>
               <thead>
                   <tr>
                       <th>Matricule</th>
                       <th>Nom & Prénom</th>
                       <th>Sexe</th>
                       <th>Fonction</th>
                       <th>Téléphone</th>
                   </tr>
               </thead>
               <tbody>';
        
        foreach ($list as $pers) {
            $html .= '<tr>
                <td>' . htmlspecialchars($pers['matricule']) . '</td>
                <td>' . htmlspecialchars($pers['nom'] . ' ' . $pers['prenom']) . '</td>
                <td>' . htmlspecialchars($pers['sexe']) . '</td>
                <td>' . htmlspecialchars($pers['fonction']) . '</td>
                <td>' . htmlspecialchars($pers['telephone']) . '</td>
            </tr>';
        }
        
        $html .= '</tbody></table>
        <script>window.print();</script>
        </body></html>';
        
        echo $html;
    }
}
