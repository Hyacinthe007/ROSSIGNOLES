-- =====================================================
-- Migration: Tables pour Articles Scolaires Optionnels
-- Date: 2026-01-22
-- Description: Création des tables pour gérer les articles optionnels
-- =====================================================

-- Créer une table de liaison pour les articles optionnels par niveau (optionnel)
-- Cela permet de définir quels articles sont disponibles pour chaque niveau
CREATE TABLE IF NOT EXISTS `articles_niveaux` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `article_id` bigint(20) NOT NULL,
  `niveau_id` bigint(20) DEFAULT NULL COMMENT 'NULL = tous les niveaux',
  `annee_scolaire_id` bigint(20) NOT NULL,
  `actif` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_article_niveau_annee` (`article_id`, `niveau_id`, `annee_scolaire_id`),
  KEY `fk_an_article` (`article_id`),
  KEY `fk_an_niveau` (`niveau_id`),
  KEY `fk_an_annee` (`annee_scolaire_id`),
  CONSTRAINT `fk_an_article` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`),
  CONSTRAINT `fk_an_niveau` FOREIGN KEY (`niveau_id`) REFERENCES `niveaux` (`id`),
  CONSTRAINT `fk_an_annee` FOREIGN KEY (`annee_scolaire_id`) REFERENCES `annees_scolaires` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Créer une table pour stocker les articles sélectionnés lors de l'inscription
CREATE TABLE IF NOT EXISTS `inscriptions_articles` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `inscription_id` bigint(20) NOT NULL,
  `article_id` bigint(20) NOT NULL,
  `quantite` int(11) NOT NULL DEFAULT 1,
  `prix_unitaire` decimal(10,2) NOT NULL,
  `montant_total` decimal(10,2) NOT NULL,
  `paye` tinyint(1) DEFAULT 0 COMMENT '1 si payé au moment de l\'inscription',
  `facture_id` bigint(20) DEFAULT NULL COMMENT 'Facture si payé séparément',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_ia_inscription` (`inscription_id`),
  KEY `fk_ia_article` (`article_id`),
  KEY `fk_ia_facture` (`facture_id`),
  CONSTRAINT `fk_ia_inscription` FOREIGN KEY (`inscription_id`) REFERENCES `inscriptions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ia_article` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`),
  CONSTRAINT `fk_ia_facture` FOREIGN KEY (`facture_id`) REFERENCES `factures` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SELECT 'Tables créées avec succès!' as Message;
