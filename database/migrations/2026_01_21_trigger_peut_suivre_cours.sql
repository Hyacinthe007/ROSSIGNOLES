-- ============================================================================
-- Migration : Activation du système peut_suivre_cours
-- Date : 2026-01-21
-- Description : Crée un trigger pour mettre à jour automatiquement le champ
--               peut_suivre_cours dans statuts_eleves_ecolage
-- ============================================================================

-- Sélectionner la base de données
USE abonnements_transport;

-- Suppression du trigger s'il existe déjà
DROP TRIGGER IF EXISTS update_statut_eleve_after_echeance_update;

DELIMITER $$

-- ============================================================================
-- Trigger : update_statut_eleve_after_echeance_update
-- Description : Met à jour automatiquement peut_suivre_cours quand une 
--               échéance est modifiée
-- ============================================================================
CREATE TRIGGER update_statut_eleve_after_echeance_update
AFTER UPDATE ON echeanciers_ecolages
FOR EACH ROW
BEGIN
    DECLARE v_peut_suivre INT DEFAULT 1;
    DECLARE v_date_exclusion DATE DEFAULT NULL;
    
    -- Déterminer si l'élève peut suivre les cours
    -- Règle : Si statut = 'exclusion' ou 'impaye_exclu', alors peut_suivre_cours = 0
    IF NEW.statut IN ('exclusion', 'impaye_exclu') THEN
        SET v_peut_suivre = 0;
        SET v_date_exclusion = NEW.date_exclusion;
    ELSE
        SET v_peut_suivre = 1;
        SET v_date_exclusion = NULL;
    END IF;
    
    -- Mettre à jour ou insérer dans statuts_eleves_ecolage
    INSERT INTO statuts_eleves_ecolage 
    (
        eleve_id, 
        annee_scolaire_id, 
        mois, 
        annee, 
        statut, 
        peut_suivre_cours,
        date_exclusion,
        updated_at
    )
    VALUES 
    (
        NEW.eleve_id, 
        NEW.annee_scolaire_id, 
        NEW.mois, 
        NEW.annee, 
        NEW.statut, 
        v_peut_suivre,
        v_date_exclusion,
        NOW()
    )
    ON DUPLICATE KEY UPDATE
        statut = NEW.statut,
        peut_suivre_cours = v_peut_suivre,
        date_exclusion = v_date_exclusion,
        updated_at = NOW();
END$$

DELIMITER ;

-- ============================================================================
-- Initialisation : Synchroniser les données existantes
-- Description : Met à jour statuts_eleves_ecolage pour tous les échéanciers
--               existants
-- ============================================================================

-- Vider la table statuts_eleves_ecolage pour repartir sur une base saine
TRUNCATE TABLE statuts_eleves_ecolage;

-- Insérer les statuts pour tous les échéanciers existants
INSERT INTO statuts_eleves_ecolage 
(
    eleve_id, 
    annee_scolaire_id, 
    mois, 
    annee, 
    statut, 
    peut_suivre_cours,
    date_exclusion,
    alerte_envoyee,
    created_at,
    updated_at
)
SELECT 
    e.eleve_id,
    e.annee_scolaire_id,
    e.mois,
    e.annee,
    e.statut,
    CASE 
        WHEN e.statut IN ('exclusion', 'impaye_exclu') THEN 0
        ELSE 1
    END as peut_suivre_cours,
    CASE 
        WHEN e.statut IN ('exclusion', 'impaye_exclu') THEN e.date_exclusion
        ELSE NULL
    END as date_exclusion,
    0 as alerte_envoyee,
    NOW() as created_at,
    NOW() as updated_at
FROM echeanciers_ecolages e
ON DUPLICATE KEY UPDATE
    statut = e.statut,
    peut_suivre_cours = CASE 
        WHEN e.statut IN ('exclusion', 'impaye_exclu') THEN 0
        ELSE 1
    END,
    date_exclusion = CASE 
        WHEN e.statut IN ('exclusion', 'impaye_exclu') THEN e.date_exclusion
        ELSE NULL
    END,
    updated_at = NOW();

-- ============================================================================
-- Vérification : Afficher les statistiques
-- ============================================================================
SELECT 
    'Total échéanciers' as type,
    COUNT(*) as nombre
FROM echeanciers_ecolages

UNION ALL

SELECT 
    'Total statuts créés' as type,
    COUNT(*) as nombre
FROM statuts_eleves_ecolage

UNION ALL

SELECT 
    'Élèves bloqués (peut_suivre_cours = 0)' as type,
    COUNT(DISTINCT eleve_id) as nombre
FROM statuts_eleves_ecolage
WHERE peut_suivre_cours = 0

UNION ALL

SELECT 
    'Élèves autorisés (peut_suivre_cours = 1)' as type,
    COUNT(DISTINCT eleve_id) as nombre
FROM statuts_eleves_ecolage
WHERE peut_suivre_cours = 1;

-- ============================================================================
-- Fin de la migration
-- ============================================================================
