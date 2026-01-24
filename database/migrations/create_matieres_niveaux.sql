-- Création de la table matieres_niveaux
-- Cette table associe les matières aux niveaux avec leurs coefficients

CREATE TABLE IF NOT EXISTS `matieres_niveaux` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `niveau_id` int(11) NOT NULL,
  `matiere_id` int(11) NOT NULL,
  `coefficient` decimal(4,2) NOT NULL DEFAULT 1.00,
  `obligatoire` tinyint(1) NOT NULL DEFAULT 1,
  `heures_semaine` decimal(4,2) DEFAULT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_niveau_matiere` (`niveau_id`, `matiere_id`),
  KEY `idx_niveau_id` (`niveau_id`),
  KEY `idx_matiere_id` (`matiere_id`),
  KEY `idx_actif` (`actif`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Association des matières avec les niveaux scolaires et leurs coefficients';
