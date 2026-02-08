<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Inscription;
use App\Models\DocumentsInscription;
use Exception;

/**
 * Contrôleur InscriptionDocumentController
 * Gère les documents liés aux inscriptions
 */
class InscriptionDocumentController extends BaseController {
    
    private $inscriptionModel;
    private $docModel;
    
    public function __construct() {
        $this->requireAuth();
        $this->inscriptionModel = new Inscription();
        $this->docModel = new DocumentsInscription();
    }

    /**
     * Gestion des documents d'inscription
     */
    public function index($id) {
        $inscription = $this->inscriptionModel->details($id);
        
        if (!$inscription) {
            $_SESSION['error'] = "Inscription introuvable";
            $this->redirect('inscriptions/liste');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->uploadDocument($id, $inscription);
                $_SESSION['success'] = "Document ajouté avec succès";
                $this->redirect('inscriptions/documents/' . $id);
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }
        }
        
        $documents = $this->docModel->getByInscription($id);
        
        $this->view('inscriptions/documents', [
            'inscription' => $inscription,
            'documents' => $documents
        ]);
    }

    /**
     * Upload d'un document
     */
    private function uploadDocument($inscriptionId, $inscription) {
        if (!isset($_FILES['document']) || $_FILES['document']['error'] === UPLOAD_ERR_NO_FILE) {
            throw new Exception("Veuillez sélectionner un fichier.");
        }

        $file = $_FILES['document'];
        $typeDocument = $_POST['type_document'] ?? 'Autre';

        // Validation du type de fichier
        $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'];
        if (!in_array($file['type'], $allowedTypes)) {
            throw new Exception("Type de fichier non autorisé. PDF, JPG et PNG uniquement.");
        }

        // Création du dossier si inexistant
        $uploadDir = ROOT_PATH . '/public/uploads/documents/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Nom unique
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'doc_' . $inscriptionId . '_' . time() . '_' . uniqid() . '.' . $extension;
        $destination = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            $data = [
                'inscription_id' => $inscriptionId,
                'eleve_id' => $inscription['eleve_id'],
                'nom_document' => $typeDocument,
                'chemin_fichier' => 'uploads/documents/' . $filename,
                'type_mime' => $file['type'],
                'taille' => $file['size'],
                'date_upload' => date('Y-m-d H:i:s')
            ];
            
            return $this->docModel->create($data);
        } else {
            throw new Exception("Erreur lors de l'enregistrement du fichier.");
        }
    }

    /**
     * Supprimer un document
     */
    public function delete($id) {
        $doc = $this->docModel->findById($id);
        if (!$doc) {
            $_SESSION['error'] = "Document introuvable";
            $this->redirect('inscriptions/liste');
        }

        $inscriptionId = $doc['inscription_id'];
        
        // Supprimer le fichier physiquement
        $filePath = ROOT_PATH . '/public/' . $doc['chemin_fichier'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        if ($this->docModel->delete($id)) {
            $_SESSION['success'] = "Document supprimé";
        } else {
            $_SESSION['error'] = "Erreur lors de la suppression";
        }

        $this->redirect('inscriptions/documents/' . $inscriptionId);
    }
}
