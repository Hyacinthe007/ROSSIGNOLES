-- Phase 3 : Unification des coefficients des matières
-- Cette migration remplace matieres_classes, matieres_series et matieres_niveaux par une table unique

SET FOREIGN_KEY_CHECKS=0;

-- 1. Création de la table unifiée
CREATE TABLE IF NOT EXISTS `coefficients_matieres` (
    `id` bigint(20) NOT NULL AUTO_INCREMENT,
    `matiere_id` bigint(20) NOT NULL,
    `cible_type` enum('classe', 'serie', 'niveau') NOT NULL COMMENT 'Le type d''entité auquel s''applique le coefficient',
    `cible_id` bigint(20) NOT NULL COMMENT 'L''ID de la classe, de la série ou du niveau',
    `coefficient` decimal(4,2) NOT NULL DEFAULT 1.00,
    `heures_semaine` decimal(4,2) DEFAULT NULL,
    `obligatoire` tinyint(1) DEFAULT 1,
    `actif` tinyint(1) DEFAULT 1,
    `annee_scolaire_id` bigint(20) DEFAULT NULL COMMENT 'NULL si permanent (niveau/série), défini si spécifique (classe)',
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_coeff_cible` (`matiere_id`, `cible_type`, `cible_id`, `annee_scolaire_id`),
    KEY `idx_cible` (`cible_type`, `cible_id`),
    KEY `idx_annee` (`annee_scolaire_id`),
    CONSTRAINT `fk_cm_matiere` FOREIGN KEY (`matiere_id`) REFERENCES `matieres` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Migration des données de matieres_niveaux
INSERT IGNORE INTO `coefficients_matieres` (matiere_id, cible_type, cible_id, coefficient, heures_semaine, obligatoire, actif)
SELECT matiere_id, 'niveau', niveau_id, coefficient, heures_semaine, obligatoire, actif
FROM matieres_niveaux;

-- 3. Migration des données de matieres_series
INSERT IGNORE INTO `coefficients_matieres` (matiere_id, cible_type, cible_id, coefficient, heures_semaine, obligatoire, actif)
SELECT matiere_id, 'serie', serie_id, coefficient, heures_semaine, obligatoire, actif
FROM matieres_series;

-- 4. Migration des données de matieres_classes
INSERT IGNORE INTO `coefficients_matieres` (matiere_id, cible_type, cible_id, annee_scolaire_id, coefficient, heures_semaine, obligatoire)
SELECT matiere_id, 'classe', classe_id, annee_scolaire_id, coefficient, heures_semaine, obligatoire
FROM matieres_classes;

-- 5. Création d'une vue de compatibilité pour éviter de tout casser immédiatement
CREATE OR REPLACE VIEW `vue_coefficients_legacy` AS
SELECT 
    cm.id, cm.matiere_id, m.nom as matiere_nom, m.code as matiere_code,
    cm.cible_type, cm.cible_id, cm.coefficient, cm.annee_scolaire_id
FROM coefficients_matieres cm
JOIN matieres m ON cm.matiere_id = m.id;

SET FOREIGN_KEY_CHECKS=1;
