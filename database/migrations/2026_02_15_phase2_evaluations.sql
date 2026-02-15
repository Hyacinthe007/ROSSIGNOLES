-- =============================================================================
-- ROSSIGNOLES V2 — Phase 2 : Évaluations Unifiées
-- Date : 2026-02-15
-- Description : Création des tables unifiées evaluations/notes et migration
-- =============================================================================

SET FOREIGN_KEY_CHECKS=0;

-- ─────────────────────────────────────────────────────────────────────────────
-- 1. TABLE EVALUATIONS — Unification interrogations + examens_finaux
-- ─────────────────────────────────────────────────────────────────────────────

CREATE TABLE IF NOT EXISTS `evaluations` (
  `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `type` ENUM('interrogation', 'devoir', 'examen', 'tp', 'oral') NOT NULL,
  `classe_id` BIGINT(20) NOT NULL,
  `matiere_id` BIGINT(20) NOT NULL,
  `personnel_id` BIGINT(20) DEFAULT NULL COMMENT 'Enseignant responsable',
  `periode_id` BIGINT(20) NOT NULL,
  `annee_scolaire_id` BIGINT(20) NOT NULL,
  `nom` VARCHAR(255) NOT NULL,
  `date_evaluation` DATE NOT NULL,
  `heure_debut` TIME DEFAULT NULL,
  `heure_fin` TIME DEFAULT NULL,
  `duree_minutes` INT DEFAULT NULL,
  `note_sur` DECIMAL(5,2) DEFAULT 20.00,
  `poids` DECIMAL(3,2) DEFAULT 1.00 COMMENT 'Coefficient de l evaluation dans la moyenne',
  `description` TEXT DEFAULT NULL,
  `consignes` TEXT DEFAULT NULL,
  `sujet_url` VARCHAR(255) DEFAULT NULL,
  `statut` ENUM('planifiee', 'en_cours', 'terminee', 'annulee') DEFAULT 'terminee',
  `source_migration` ENUM('interrogations', 'examens_finaux') DEFAULT NULL,
  `source_id` BIGINT(20) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_classe_matiere_periode` (`classe_id`, `matiere_id`, `periode_id`),
  KEY `idx_annee_type` (`annee_scolaire_id`, `type`),
  KEY `idx_date` (`date_evaluation`),
  CONSTRAINT `fk_eval_classe` FOREIGN KEY (`classe_id`) REFERENCES `classes` (`id`),
  CONSTRAINT `fk_eval_matiere` FOREIGN KEY (`matiere_id`) REFERENCES `matieres` (`id`),
  CONSTRAINT `fk_eval_personnel` FOREIGN KEY (`personnel_id`) REFERENCES `personnels` (`id`),
  CONSTRAINT `fk_eval_periode` FOREIGN KEY (`periode_id`) REFERENCES `periodes` (`id`),
  CONSTRAINT `fk_eval_annee` FOREIGN KEY (`annee_scolaire_id`) REFERENCES `annees_scolaires` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COMMENT='Table d evaluations unifiée (V2)';

-- ─────────────────────────────────────────────────────────────────────────────
-- 2. TABLE NOTES — Unification notes_interrogations + notes_examens
-- ─────────────────────────────────────────────────────────────────────────────

CREATE TABLE IF NOT EXISTS `notes` (
  `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `evaluation_id` BIGINT(20) NOT NULL,
  `eleve_id` BIGINT(20) NOT NULL,
  `note` DECIMAL(5,2) DEFAULT NULL,
  `absent` TINYINT(1) DEFAULT 0,
  `dispense` TINYINT(1) DEFAULT 0,
  `appreciation` TEXT DEFAULT NULL,
  `saisi_par` BIGINT(20) DEFAULT NULL,
  `date_saisie` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_eleve_eval` (`eleve_id`, `evaluation_id`),
  KEY `idx_evaluation` (`evaluation_id`),
  KEY `idx_eleve` (`eleve_id`),
  CONSTRAINT `fk_note_eval` FOREIGN KEY (`evaluation_id`) REFERENCES `evaluations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_note_eleve` FOREIGN KEY (`eleve_id`) REFERENCES `eleves` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COMMENT='Table de notes unifiée (V2)';

-- ─────────────────────────────────────────────────────────────────────────────
-- 3. MIGRATION DES DONNÉES
-- ─────────────────────────────────────────────────────────────────────────────

-- A. Interrogations → Evaluations
INSERT IGNORE INTO `evaluations` 
  (`type`, `classe_id`, `matiere_id`, `personnel_id`, `periode_id`, `annee_scolaire_id`, 
   `nom`, `date_evaluation`, `statut`, `source_migration`, `source_id`, `created_at`)
SELECT 
  'interrogation', i.classe_id, i.matiere_id, i.personnel_id, i.periode_id, i.annee_scolaire_id,
  i.nom, i.date_interrogation, 'terminee', 'interrogations', i.id, i.created_at
FROM `interrogations` i;

-- B. Examens → Evaluations
INSERT IGNORE INTO `evaluations` 
  (`type`, `classe_id`, `matiere_id`, `personnel_id`, `periode_id`, `annee_scolaire_id`, 
   `nom`, `date_evaluation`, `statut`, `source_migration`, `source_id`, `created_at`)
SELECT 
  'examen', ex.classe_id, ex.matiere_id, ex.personnel_id, ex.periode_id, ex.annee_scolaire_id,
  CONCAT('Examen ', ex.nom), ex.date_examen, 'terminee', 'examens_finaux', ex.id, ex.created_at
FROM `examens_finaux` ex;

-- C. Notes Interrogations → Notes
INSERT IGNORE INTO `notes` 
  (`evaluation_id`, `eleve_id`, `note`, `absent`, `appreciation`, `created_at`)
SELECT 
  ev.id, ni.eleve_id, ni.note, ni.absent, ni.appreciation, ni.created_at
FROM `notes_interrogations` ni
JOIN `evaluations` ev ON ev.source_id = ni.interrogation_id AND ev.source_migration = 'interrogations';

-- D. Notes Examens → Notes
INSERT IGNORE INTO `notes` 
  (`evaluation_id`, `eleve_id`, `note`, `absent`, `appreciation`, `created_at`)
SELECT 
  ev.id, ne.eleve_id, ne.note, ne.absent, ne.appreciation, ne.created_at
FROM `notes_examens` ne
JOIN `evaluations` ev ON ev.source_id = ne.examen_id AND ev.source_migration = 'examens_finaux';

-- ─────────────────────────────────────────────────────────────────────────────
-- 4. VUES COMPATIBILITÉ (Pour ne pas casser le code existant immédiatement)
-- ─────────────────────────────────────────────────────────────────────────────

-- Ces vues permettent au code V1 de continuer à lire les données
-- mais elles seront supprimées en Phase 5.

CREATE OR REPLACE VIEW `vue_interrogations_legacy` AS
SELECT id, nom, date_evaluation as date_interrogation, classe_id, matiere_id, personnel_id, periode_id, annee_scolaire_id, created_at
FROM evaluations WHERE type = 'interrogation';

CREATE OR REPLACE VIEW `vue_examens_legacy` AS
SELECT id, nom, date_evaluation as date_examen, classe_id, matiere_id, personnel_id, periode_id, annee_scolaire_id, created_at
FROM evaluations WHERE type = 'examen';

SET FOREIGN_KEY_CHECKS=1;
