-- ============================================================================
-- Migration : Création de la table documents_personnel
-- Date : 2026-02-14
-- Description : Stockage des pièces justificatives du personnel
--               (diplômes, acte de naissance enfants, acte de mariage, CIN, etc.)
-- ============================================================================

CREATE TABLE IF NOT EXISTS `documents_personnel` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `personnel_id` bigint(20) NOT NULL COMMENT 'Référence vers la table personnels',
  `type_document` enum(
    'diplome',
    'acte_naissance_enfant',
    'acte_mariage',
    'copie_cin',
    'cv',
    'certificat_medical',
    'certificat_travail',
    'lettre_motivation',
    'contrat_travail',
    'fiche_renseignement',
    'photo_identite',
    'rib',
    'attestation_cnaps',
    'autre'
  ) NOT NULL,
  `libelle` varchar(255) NOT NULL COMMENT 'Ex: Diplôme CAPEN, Acte naissance - Fils Jean, etc.',
  `nom_fichier` varchar(255) DEFAULT NULL COMMENT 'Nom original du fichier uploadé',
  `chemin_fichier` varchar(500) DEFAULT NULL COMMENT 'Chemin relatif vers le fichier stocké',
  `taille_fichier` int(11) DEFAULT NULL COMMENT 'Taille en octets',
  `type_mime` varchar(100) DEFAULT NULL COMMENT 'Ex: application/pdf, image/jpeg',
  `numero_document` varchar(100) DEFAULT NULL COMMENT 'Numéro officiel du document (ex: n° CIN)',
  `date_emission` date DEFAULT NULL COMMENT 'Date de délivrance du document',
  `date_expiration` date DEFAULT NULL COMMENT 'Date d''expiration si applicable',
  `lieu_emission` varchar(255) DEFAULT NULL COMMENT 'Lieu de délivrance',
  `nom_enfant` varchar(200) DEFAULT NULL COMMENT 'Nom de l''enfant (pour acte_naissance_enfant)',
  `remarques` text DEFAULT NULL,
  `statut` enum('en_attente','valide','refuse','expire') DEFAULT 'en_attente',
  `valide_par` bigint(20) DEFAULT NULL COMMENT 'Utilisateur qui a validé le document',
  `date_validation` datetime DEFAULT NULL,
  `motif_refus` text DEFAULT NULL,
  `telecharge_par` bigint(20) DEFAULT NULL COMMENT 'Utilisateur qui a uploadé le document',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_personnel` (`personnel_id`),
  KEY `idx_type` (`type_document`),
  KEY `idx_statut` (`statut`),
  KEY `idx_personnel_type` (`personnel_id`, `type_document`),
  CONSTRAINT `fk_docpers_personnel` FOREIGN KEY (`personnel_id`) REFERENCES `personnels` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_docpers_valideur` FOREIGN KEY (`valide_par`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_docpers_uploadeur` FOREIGN KEY (`telecharge_par`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Documents et pièces justificatives du personnel';
