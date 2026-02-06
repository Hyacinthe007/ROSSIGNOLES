-- ============================================================================
-- Migration : Correction et consolidation du système de paie
-- Date : 2026-02-03
-- Description : Supprime les doublons et corrige la structure des tables paie
-- ============================================================================

-- 1. Vérifier si les tables doublons contiennent des données importantes
-- (À exécuter manuellement pour vérifier avant suppression)
-- SELECT COUNT(*) FROM fiches_paie;
-- SELECT COUNT(*) FROM salaires_personnels;

-- 2. Supprimer les tables en doublon (après vérification)
DROP TABLE IF EXISTS `fiches_paie`;
DROP TABLE IF EXISTS `salaires_personnels`;

-- 3. Ajouter la colonne 'nom' manquante dans paie_parametres_cotisations
ALTER TABLE `paie_parametres_cotisations`
ADD COLUMN `nom` VARCHAR(50) NOT NULL UNIQUE AFTER `id`,
ADD COLUMN `description` TEXT DEFAULT NULL AFTER `taux_patronal`;

-- 4. Ajouter la colonne 'annee_validite' dans paie_tranches_irsa
ALTER TABLE `paie_tranches_irsa`
ADD COLUMN `annee_validite` INT(4) DEFAULT 2026 COMMENT 'Année de validité de cette grille' AFTER `taux`;

-- 5. Créer un index pour améliorer les performances
ALTER TABLE `paie_bulletins`
ADD INDEX `idx_periode_statut` (`periode`, `statut`);

ALTER TABLE `paie_contrats`
ADD INDEX `idx_actif` (`actif`);

-- 6. Ajouter une contrainte pour éviter les doublons de contrats actifs
-- Un personnel ne peut avoir qu'un seul contrat actif à la fois
ALTER TABLE `paie_contrats`
ADD UNIQUE KEY `unique_personnel_actif` (`personnel_id`, `actif`);

-- Note: Cette contrainte peut échouer si vous avez déjà plusieurs contrats actifs
-- Dans ce cas, désactivez d'abord les anciens contrats avant de lancer cette migration

-- ============================================================================
-- Fin de la migration
-- ============================================================================
