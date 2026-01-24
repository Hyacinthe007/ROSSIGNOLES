<?php
declare(strict_types=1);

namespace App\Services;

/**
 * Service de génération PDF
 */

class PdfService {
    
    /**
     * Génère un bulletin en PDF
     */
    public function generateBulletin($html, $filename = "bulletin.pdf") {
        return $this->generatePdf($html, $filename);
    }
    
    /**
     * Génère un reçu de paiement en PDF
     */
    public function generateRecu($html, $filename = "recu_paiement.pdf") {
        return $this->generatePdf($html, $filename);
    }

    /**
     * Génère un certificat scolaire en PDF
     */
    public function generateCertificatScolaire($html, $filename = "certificat_scolaire.pdf") {
        return $this->generatePdf($html, $filename);
    }

    /**
     * Génère un certificat de travail en PDF
     */
    public function generateCertificatTravail($html, $filename = "certificat_travail.pdf") {
        return $this->generatePdf($html, $filename);
    }
    /**
     * Génère un document PDF générique à partir de HTML (via Dompdf)
     */
    public function generatePdf($html, $filename) {
        try {
            // Options Dompdf
            $options = new \Dompdf\Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true); // Pour les images/CSS externes
            $options->set('defaultFont', 'Arial');

            // Instanciation de Dompdf
            $dompdf = new \Dompdf\Dompdf($options);
            
            // Charger le HTML
            $dompdf->loadHtml($html);

            // (Optionnel) Taille du papier et orientation
            $dompdf->setPaper('A4', 'portrait');

            // Rendu du PDF
            $dompdf->render();

            // Envoi au navigateur pour téléchargement
            $dompdf->stream($filename, ["Attachment" => true]);
            exit;
        } catch (\Exception $e) {
            // Fallback en cas d'erreur de Dompdf : rendu HTML pour impression
            header('Content-Type: text/html; charset=UTF-8');
            header('Content-Disposition: inline; filename="' . $filename . '"');
            echo "<!-- Erreur PDF : " . $e->getMessage() . " -->";
            echo $html;
            exit;
        }
    }
    
    /**
     * Alias pour generatePdf
     */
    public function generate($html, $filename) {
        return $this->generatePdf($html, $filename);
    }
}

