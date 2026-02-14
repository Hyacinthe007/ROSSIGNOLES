<?php
namespace App\Helpers;

/**
 * Helper pour les dates
 */

class DateHelper {
    
    /**
     * Obtient l'année scolaire active
     */
    public static function getAnneeScolaireActive() {
        require_once APP_PATH . '/Models/BaseModel.php';
        $db = BaseModel::getDBConnection();
        
        $stmt = $db->query("SELECT * FROM annees_scolaires WHERE actif = 1 LIMIT 1");
        return $stmt->fetch();
    }
    
    /**
     * Calcule l'âge à partir d'une date de naissance
     */
    public static function calculateAge($dateNaissance) {
        if (empty($dateNaissance)) {
            return null;
        }
        
        $birth = new DateTime($dateNaissance);
        $today = new DateTime();
        return $today->diff($birth)->y;
    }
    
    /**
     * Formate une date en français
     */
    public static function formatFrench($date, $format = 'd/m/Y') {
        if (empty($date)) {
            return '';
        }
        
        $dateObj = new DateTime($date);
        return $dateObj->format($format);
    }
}

if (!class_exists('DateHelper')) {
    class_alias(\App\Helpers\DateHelper::class, 'DateHelper');
}

