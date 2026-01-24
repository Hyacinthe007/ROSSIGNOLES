<?php
declare(strict_types=1);

namespace App\Services;

class ImportExportService {
    
    /**
     * Exporte les données en CSV
     */
    public function exportCsv($data, $filename) {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // En-têtes
        if (!empty($data)) {
            fputcsv($output, array_keys($data[0]));
        }
        
        // Données
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
        
        fclose($output);
        exit;
    }
    
    /**
     * Importe des données depuis un CSV
     */
    public function importCsv($filePath) {
        $data = [];
        
        if (($handle = fopen($filePath, 'r')) !== false) {
            $headers = fgetcsv($handle);
            
            while (($row = fgetcsv($handle)) !== false) {
                $data[] = array_combine($headers, $row);
            }
            
            fclose($handle);
        }
        
        return $data;
    }
}

