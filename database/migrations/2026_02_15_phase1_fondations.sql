-- =============================================================================
-- ROSSIGNOLES V2 — Phase 1 : Fondations
-- Date : 2026-02-15
-- Description : audit_trail, fusion calendrier, index FULLTEXT, nettoyage
-- =============================================================================

SET FOREIGN_KEY_CHECKS=0;

-- ─────────────────────────────────────────────────────────────────────────────
-- 1. TABLE AUDIT_TRAIL — Traçabilité centralisée
-- ─────────────────────────────────────────────────────────────────────────────

CREATE TABLE IF NOT EXISTS `audit_trail` (
  `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT(20) DEFAULT NULL,
  `username` VARCHAR(100) DEFAULT NULL COMMENT 'Copie du username au moment de l action',
  `action` ENUM('create', 'update', 'delete', 'validate', 'reject', 'export', 'login', 'logout') NOT NULL,
  `table_name` VARCHAR(100) NOT NULL,
  `record_id` BIGINT(20) DEFAULT NULL,
  `old_values` JSON DEFAULT NULL COMMENT 'Anciennes valeurs (pour update/delete)',
  `new_values` JSON DEFAULT NULL COMMENT 'Nouvelles valeurs (pour create/update)',
  `description` VARCHAR(500) DEFAULT NULL COMMENT 'Description lisible de l action',
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `user_agent` VARCHAR(500) DEFAULT NULL,
  `request_uri` VARCHAR(500) DEFAULT NULL,
  `request_method` VARCHAR(10) DEFAULT NULL,
  `duration_ms` INT DEFAULT NULL COMMENT 'Durée de la requête en ms',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_table_record` (`table_name`, `record_id`),
  KEY `idx_user_date` (`user_id`, `created_at`),
  KEY `idx_action_date` (`action`, `created_at`),
  KEY `idx_date` (`created_at`),
  KEY `idx_table_action` (`table_name`, `action`, `created_at`),
  CONSTRAINT `fk_audit_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COMMENT='Audit centralisé — traçabilité complète de toutes les actions';

-- ─────────────────────────────────────────────────────────────────────────────
-- 2. TABLE EVENEMENTS_CALENDRIER — Fusion calendrier_scolaire + jours_feries
-- ─────────────────────────────────────────────────────────────────────────────

CREATE TABLE IF NOT EXISTS `evenements_calendrier` (
  `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `annee_scolaire_id` BIGINT(20) NOT NULL,
  `type` ENUM('vacances', 'ferie', 'pont', 'examen', 'conseil',
              'rentree', 'sortie', 'pedagogique', 'autre') NOT NULL,
  `libelle` VARCHAR(255) NOT NULL,
  `date_debut` DATE NOT NULL,
  `date_fin` DATE NOT NULL,
  `description` TEXT DEFAULT NULL,
  `concerne` ENUM('tous', 'eleves', 'enseignants', 'administratifs') DEFAULT 'tous',
  `bloque_cours` TINYINT(1) DEFAULT 1 COMMENT 'Pas de cours ces jours',
  `couleur` VARCHAR(7) DEFAULT NULL COMMENT 'Couleur pour l affichage calendrier',
  `actif` TINYINT(1) DEFAULT 1,
  `source_migration` VARCHAR(50) DEFAULT NULL COMMENT 'calendrier_scolaire ou jours_feries (migration)',
  `source_id` BIGINT(20) DEFAULT NULL COMMENT 'ID original avant migration',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_annee_dates` (`annee_scolaire_id`, `date_debut`, `date_fin`),
  KEY `idx_type_actif` (`type`, `actif`),
  KEY `idx_concerne` (`concerne`),
  KEY `idx_dates` (`date_debut`, `date_fin`),
  CONSTRAINT `fk_evt_annee` FOREIGN KEY (`annee_scolaire_id`) REFERENCES `annees_scolaires` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COMMENT='Calendrier unifié — remplace calendrier_scolaire + jours_feries';

-- Migration des données de calendrier_scolaire → evenements_calendrier
INSERT IGNORE INTO `evenements_calendrier` 
  (`annee_scolaire_id`, `type`, `libelle`, `date_debut`, `date_fin`, 
   `description`, `concerne`, `bloque_cours`, `actif`, `source_migration`, `source_id`, `created_at`)
SELECT 
  cs.annee_scolaire_id,
  cs.type,
  cs.libelle,
  cs.date_debut,
  cs.date_fin,
  cs.description,
  'tous',
  cs.bloque_cours,
  1,
  'calendrier_scolaire',
  cs.id,
  cs.created_at
FROM `calendrier_scolaire` cs;

-- Migration des données de jours_feries → evenements_calendrier (sans doublons)
INSERT IGNORE INTO `evenements_calendrier` 
  (`annee_scolaire_id`, `type`, `libelle`, `date_debut`, `date_fin`, 
   `description`, `concerne`, `bloque_cours`, `actif`, `source_migration`, `source_id`, `created_at`)
SELECT 
  jf.annee_scolaire_id,
  CASE jf.type
    WHEN 'ferie_national' THEN 'ferie'
    WHEN 'vacances_scolaires' THEN 'vacances'
    WHEN 'pont' THEN 'pont'
    WHEN 'journee_pedagogique' THEN 'pedagogique'
    WHEN 'examen' THEN 'examen'
    ELSE 'autre'
  END,
  jf.libelle,
  jf.date_debut,
  jf.date_fin,
  jf.description,
  jf.concerne,
  1,
  jf.actif,
  'jours_feries',
  jf.id,
  jf.created_at
FROM `jours_feries` jf
WHERE NOT EXISTS (
  SELECT 1 FROM `evenements_calendrier` ec 
  WHERE ec.annee_scolaire_id = jf.annee_scolaire_id 
    AND ec.date_debut = jf.date_debut 
    AND ec.date_fin = jf.date_fin
    AND ec.libelle COLLATE utf8mb4_unicode_ci = jf.libelle COLLATE utf8mb4_unicode_ci
);

-- ─────────────────────────────────────────────────────────────────────────────
-- 3. INDEX FULLTEXT — Recherche rapide sur noms
-- ─────────────────────────────────────────────────────────────────────────────

-- Vérifier et ajouter l'index fulltext sur eleves
-- (On utilise ALTER TABLE avec IF NOT EXISTS via une procédure)
DROP PROCEDURE IF EXISTS `add_fulltext_indexes`;

DELIMITER //
CREATE PROCEDURE `add_fulltext_indexes`()
BEGIN
  -- Index fulltext sur eleves (nom, prenom, matricule)
  IF NOT EXISTS (
    SELECT 1 FROM information_schema.STATISTICS 
    WHERE table_schema = DATABASE() 
    AND table_name = 'eleves' 
    AND index_name = 'ft_eleves_recherche'
  ) THEN
    ALTER TABLE `eleves` ADD FULLTEXT INDEX `ft_eleves_recherche` (`nom`, `prenom`, `matricule`);
  END IF;

  -- Index composite classique sur eleves nom+prenom
  IF NOT EXISTS (
    SELECT 1 FROM information_schema.STATISTICS 
    WHERE table_schema = DATABASE() 
    AND table_name = 'eleves' 
    AND index_name = 'idx_eleves_nom_prenom'
  ) THEN
    ALTER TABLE `eleves` ADD INDEX `idx_eleves_nom_prenom` (`nom`, `prenom`);
  END IF;

  -- Index fulltext sur personnels (nom, prenom, matricule)
  IF NOT EXISTS (
    SELECT 1 FROM information_schema.STATISTICS 
    WHERE table_schema = DATABASE() 
    AND table_name = 'personnels' 
    AND index_name = 'ft_personnels_recherche'
  ) THEN
    ALTER TABLE `personnels` ADD FULLTEXT INDEX `ft_personnels_recherche` (`nom`, `prenom`, `matricule`);
  END IF;

  -- Index composite classique sur personnels nom+prenom
  IF NOT EXISTS (
    SELECT 1 FROM information_schema.STATISTICS 
    WHERE table_schema = DATABASE() 
    AND table_name = 'personnels' 
    AND index_name = 'idx_personnels_nom_prenom'
  ) THEN
    ALTER TABLE `personnels` ADD INDEX `idx_personnels_nom_prenom` (`nom`, `prenom`);
  END IF;

  -- Index fulltext sur parents (nom, prenom)
  IF NOT EXISTS (
    SELECT 1 FROM information_schema.STATISTICS 
    WHERE table_schema = DATABASE() 
    AND table_name = 'parents' 
    AND index_name = 'ft_parents_recherche'
  ) THEN
    ALTER TABLE `parents` ADD FULLTEXT INDEX `ft_parents_recherche` (`nom`, `prenom`);
  END IF;
END //
DELIMITER ;

CALL `add_fulltext_indexes`();
DROP PROCEDURE IF EXISTS `add_fulltext_indexes`;

-- ─────────────────────────────────────────────────────────────────────────────
-- 4. NETTOYAGE : Retirer effectif_actuel de classes (sera calculé dynamiquement)
-- ─────────────────────────────────────────────────────────────────────────────

-- Créer une vue pour calculer l'effectif dynamiquement (remplace la colonne)
CREATE OR REPLACE VIEW `vue_classes_effectifs` AS
SELECT 
  c.id AS classe_id,
  c.nom,
  c.code,
  c.capacite,
  COUNT(DISTINCT CASE WHEN i.statut = 'validee' THEN i.eleve_id END) AS effectif_actuel,
  c.capacite - COUNT(DISTINCT CASE WHEN i.statut = 'validee' THEN i.eleve_id END) AS places_disponibles,
  ROUND(
    COUNT(DISTINCT CASE WHEN i.statut = 'validee' THEN i.eleve_id END) / NULLIF(c.capacite, 0) * 100, 1
  ) AS taux_remplissage
FROM classes c
LEFT JOIN inscriptions i ON c.id = i.classe_id 
  AND c.annee_scolaire_id = i.annee_scolaire_id
GROUP BY c.id, c.nom, c.code, c.capacite;

-- NOTE: On ne supprime PAS encore la colonne effectif_actuel pour compatibilité
-- Elle sera retirée en Phase 5 (nettoyage final) après mise à jour des vues

-- ─────────────────────────────────────────────────────────────────────────────
-- 5. TABLE DE FILE D'ATTENTE — Pour traitement asynchrone (SMS, emails)
-- ─────────────────────────────────────────────────────────────────────────────

CREATE TABLE IF NOT EXISTS `job_queue` (
  `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `queue` VARCHAR(50) NOT NULL DEFAULT 'default' COMMENT 'Nom de la file (sms, email, notification)',
  `handler` VARCHAR(255) NOT NULL COMMENT 'Classe::methode à appeler',
  `payload` JSON NOT NULL COMMENT 'Données sérialisées pour le job',
  `attempts` INT NOT NULL DEFAULT 0,
  `max_attempts` INT NOT NULL DEFAULT 3,
  `priority` INT NOT NULL DEFAULT 0 COMMENT 'Plus élevé = plus prioritaire',
  `status` ENUM('pending', 'processing', 'completed', 'failed') DEFAULT 'pending',
  `error_message` TEXT DEFAULT NULL,
  `scheduled_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'Exécuter après cette date',
  `started_at` TIMESTAMP NULL DEFAULT NULL,
  `completed_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_queue_status` (`queue`, `status`, `priority` DESC, `scheduled_at`),
  KEY `idx_status_scheduled` (`status`, `scheduled_at`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COMMENT='File d attente pour traitements asynchrones (SMS, emails, notifications)';

SET FOREIGN_KEY_CHECKS=1;

-- =============================================================================
-- FIN Migration Phase 1
-- =============================================================================
