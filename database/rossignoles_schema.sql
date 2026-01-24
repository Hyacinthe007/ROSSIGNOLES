SET FOREIGN_KEY_CHECKS=0;

-- Table: cycles
CREATE TABLE IF NOT EXISTS `cycles` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `libelle` varchar(100) NOT NULL COMMENT 'Maternelle, Primaire, Collège, Lycée',
  `ordre` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `actif` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: annees_scolaires
CREATE TABLE IF NOT EXISTS `annees_scolaires` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(100) NOT NULL,
  `date_debut` date NOT NULL,
  `date_rentree` date DEFAULT NULL COMMENT 'Date de rentrée scolaire',
  `date_fin_cours` date DEFAULT NULL COMMENT 'Date fin des cours',
  `date_fin` date NOT NULL,
  `actif` tinyint(1) DEFAULT 0,
  `cloturee` tinyint(1) DEFAULT 0 COMMENT 'Année clôturée définitivement',
  `date_cloture` datetime DEFAULT NULL,
  `nb_jours_classe` int(11) DEFAULT NULL,
  `nb_semaines` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_actif_cloture` (`actif`, `cloturee`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: niveaux
CREATE TABLE IF NOT EXISTS `niveaux` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `libelle` varchar(100) NOT NULL,
  `ordre` int(11) NOT NULL,
  `cycle_id` bigint(20) NOT NULL,
  `actif` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `idx_cycle` (`cycle_id`),
  CONSTRAINT `fk_niveaux_cycle` FOREIGN KEY (`cycle_id`) REFERENCES `cycles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: series
CREATE TABLE IF NOT EXISTS `series` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `libelle` varchar(100) NOT NULL,
  `niveau_id` bigint(20) NOT NULL,
  `description` text DEFAULT NULL,
  `actif` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `code` (`code`),
  KEY `idx_niveau` (`niveau_id`),
  KEY `idx_actif` (`actif`),
  CONSTRAINT `fk_series_niveau` FOREIGN KEY (`niveau_id`) REFERENCES `niveaux` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: classes
CREATE TABLE IF NOT EXISTS `classes` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `code` varchar(50) NOT NULL,
  `niveau_id` bigint(20) NOT NULL,
  `serie_id` bigint(20) DEFAULT NULL,
  `professeur_principal_id` bigint(20) DEFAULT NULL COMMENT 'Personnel enseignant responsable de la classe',
  `annee_scolaire_id` bigint(20) NOT NULL,
  `capacite` int(11) DEFAULT NULL,
  `seuil_admission` decimal(4,2) DEFAULT 10.00 COMMENT 'Moyenne minimale pour admission',
  `effectif_actuel` int(11) DEFAULT 0 COMMENT 'Nombre d''élèves inscrits actuellement',
  `salle` varchar(50) DEFAULT NULL COMMENT 'Salle de classe principale',
  `statut` enum('actif', 'inactif') DEFAULT 'actif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Soft delete',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_code_annee` (`code`, `annee_scolaire_id`),
  KEY `fk_classes_niveau` (`niveau_id`),
  KEY `fk_classes_annee_scolaire` (`annee_scolaire_id`),
  KEY `idx_serie` (`serie_id`),
  KEY `idx_professeur_principal` (`professeur_principal_id`),
  KEY `idx_effectif` (`effectif_actuel`, `capacite`),
  KEY `idx_deleted` (`deleted_at`),
  CONSTRAINT `fk_classes_niveau` FOREIGN KEY (`niveau_id`) REFERENCES `niveaux` (`id`),
  CONSTRAINT `fk_classes_serie` FOREIGN KEY (`serie_id`) REFERENCES `series` (`id`),
  CONSTRAINT `fk_classes_annee` FOREIGN KEY (`annee_scolaire_id`) REFERENCES `annees_scolaires` (`id`),
  CONSTRAINT `fk_classes_pp` FOREIGN KEY (`professeur_principal_id`) REFERENCES `personnels` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: matieres
CREATE TABLE IF NOT EXISTS `matieres` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `couleur` varchar(7) DEFAULT NULL,
  `actif` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: matieres_series
CREATE TABLE IF NOT EXISTS `matieres_series` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `matiere_id` bigint(20) NOT NULL,
  `serie_id` bigint(20) NOT NULL,
  `coefficient` decimal(3,2) NOT NULL DEFAULT 1.00,
  `obligatoire` tinyint(1) DEFAULT 1,
  `heures_semaine` decimal(3,1) DEFAULT NULL,
  `actif` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_matiere_serie` (`matiere_id`, `serie_id`),
  KEY `idx_serie` (`serie_id`),
  KEY `idx_actif` (`actif`),
  CONSTRAINT `fk_ms_matiere` FOREIGN KEY (`matiere_id`) REFERENCES `matieres` (`id`),
  CONSTRAINT `fk_ms_serie` FOREIGN KEY (`serie_id`) REFERENCES `series` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL COMMENT 'Hash bcrypt',
  `user_type` enum('admin', 'enseignant', 'parent', 'eleve') NOT NULL,
  `reference_id` bigint(20) DEFAULT NULL COMMENT 'ID dans la table enseignants/parents/eleves',
  `avatar` varchar(255) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `last_login_ip` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_user_type` (`user_type`),
  KEY `idx_reference` (`reference_id`),
  KEY `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: roles
CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `niveau` int(11) DEFAULT 0 COMMENT 'Hiérarchie: admin=100, directeur=90, enseignant=50, parent=20, eleve=10',
  `actif` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `idx_niveau` (`niveau`),
  KEY `idx_actif` (`actif`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: permissions
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `code` varchar(100) NOT NULL,
  `module` varchar(50) NOT NULL,
  `action` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `idx_module` (`module`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: roles_permissions
CREATE TABLE IF NOT EXISTS `roles_permissions` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `role_id` bigint(20) NOT NULL,
  `permission_id` bigint(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_role_permission` (`role_id`, `permission_id`),
  KEY `permission_id` (`permission_id`),
  CONSTRAINT `fk_rp_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`),
  CONSTRAINT `fk_rp_permission` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: users_roles
CREATE TABLE IF NOT EXISTS `users_roles` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `role_id` bigint(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_role` (`user_id`, `role_id`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `fk_ur_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `fk_ur_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: eleves
CREATE TABLE IF NOT EXISTS `eleves` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `matricule` varchar(50) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `date_naissance` date NOT NULL,
  `lieu_naissance` varchar(100) NOT NULL,
  `sexe` enum('M', 'F') NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `statut` enum('actif', 'inactif', 'diplome', 'exclus', 'transfere') DEFAULT 'actif',
  `date_inscription` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `matricule` (`matricule`),
  KEY `idx_eleves_statut_date` (`statut`, `date_inscription`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: parents
CREATE TABLE IF NOT EXISTS `parents` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `adresse` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: eleves_parents
CREATE TABLE IF NOT EXISTS `eleves_parents` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `eleve_id` bigint(20) NOT NULL,
  `parent_id` bigint(20) NOT NULL,
  `lien_parente` enum('pere', 'mere', 'tuteur', 'grand_parent', 'oncle', 'tante', 'autre') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_eleve_parent` (`eleve_id`, `parent_id`),
  KEY `parent_id` (`parent_id`),
  CONSTRAINT `fk_ep_eleve` FOREIGN KEY (`eleve_id`) REFERENCES `eleves` (`id`),
  CONSTRAINT `fk_ep_parent` FOREIGN KEY (`parent_id`) REFERENCES `parents` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: personnels
CREATE TABLE IF NOT EXISTS `personnels` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `matricule` varchar(50) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `date_naissance` date DEFAULT NULL,
  `lieu_naissance` varchar(100) DEFAULT NULL,
  `situation_matrimoniale` enum('celibataire', 'marie', 'divorce', 'veuf') DEFAULT NULL,
  `nb_enfants` int(11) DEFAULT 0,
  `sexe` enum('M','F') NOT NULL,
  `cin` varchar(12) DEFAULT NULL,
  `numero_cnaps` varchar(50) DEFAULT NULL COMMENT 'Numéro sécurité sociale',
  `iban` varchar(34) DEFAULT NULL COMMENT 'Compte bancaire pour salaire',
  `telephone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `adresse` text DEFAULT NULL,
  `urgence_nom` varchar(200) DEFAULT NULL COMMENT 'Contact d''urgence',
  `urgence_telephone` varchar(20) DEFAULT NULL,
  `urgence_lien` varchar(100) DEFAULT NULL COMMENT 'Lien avec personnel',
  `photo` varchar(255) DEFAULT NULL,
  `diplome` varchar(255) DEFAULT NULL,
  `annee_obtention_diplome` year(4) DEFAULT NULL,
  `type_personnel` enum('enseignant','administratif','direction','autre') NOT NULL,
  `date_embauche` date DEFAULT NULL,
  `date_fin_contrat` date DEFAULT NULL,
  `type_contrat` enum('cdi','cdd','vacataire','stage') DEFAULT 'cdi',
  `statut` enum('actif','inactif','conge','demission','retraite','suspendu') DEFAULT 'actif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Soft delete',
  PRIMARY KEY (`id`),
  UNIQUE KEY `matricule` (`matricule`),
  UNIQUE KEY `cin` (`cin`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_matricule` (`matricule`),
  KEY `idx_type_statut` (`type_personnel`, `statut`),
  KEY `idx_statut` (`statut`),
  KEY `idx_email` (`email`),
  KEY `idx_cin` (`cin`),
  KEY `idx_numero_cnaps` (`numero_cnaps`),
  KEY `idx_deleted` (`deleted_at`),
  KEY `idx_date_embauche` (`date_embauche`),
  KEY `idx_date_fin_contrat` (`date_fin_contrat`, `statut`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: personnels_enseignants
CREATE TABLE IF NOT EXISTS `personnels_enseignants` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `personnel_id` bigint(20) NOT NULL,
  `diplome` varchar(255) DEFAULT NULL,
  `specialite` varchar(100) DEFAULT NULL,
  `matieres_enseignees` text DEFAULT NULL COMMENT 'Liste des matières (JSON ou séparé par virgules)',
  `grade` enum('vacataire','contractuel','titulaire','certifie','agrege') DEFAULT NULL,
  `anciennete_annees` int(11) DEFAULT NULL,
  `charge_horaire_hebdo` decimal(5,2) DEFAULT NULL COMMENT 'Heures de cours par semaine',
  `charge_horaire_max` decimal(5,2) DEFAULT 24.00 COMMENT 'Maximum heures hebdomadaires',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_personnel` (`personnel_id`),
  KEY `idx_specialite` (`specialite`),
  KEY `idx_grade` (`grade`),
  KEY `idx_charge_horaire` (`charge_horaire_hebdo`, `charge_horaire_max`),
  CONSTRAINT `fk_pe_personnel` FOREIGN KEY (`personnel_id`) REFERENCES `personnels` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: postes_administratifs
CREATE TABLE IF NOT EXISTS `postes_administratifs` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `libelle` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `departement` varchar(100) DEFAULT NULL,
  `niveau_hierarchique` int(11) DEFAULT 1,
  `actif` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `idx_code` (`code`),
  KEY `idx_departement` (`departement`),
  KEY `idx_actif` (`actif`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: personnels_administratifs
CREATE TABLE IF NOT EXISTS `personnels_administratifs` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `personnel_id` bigint(20) NOT NULL,
  `poste_id` bigint(20) NOT NULL,
  `departement` varchar(100) DEFAULT NULL,
  `niveau_acces` int(11) DEFAULT 1,
  `responsable_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_personnel` (`personnel_id`),
  KEY `idx_poste` (`poste_id`),
  KEY `idx_departement` (`departement`),
  KEY `idx_responsable` (`responsable_id`),
  CONSTRAINT `fk_pa_personnel` FOREIGN KEY (`personnel_id`) REFERENCES `personnels` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pa_poste` FOREIGN KEY (`poste_id`) REFERENCES `postes_administratifs` (`id`),
  CONSTRAINT `fk_pa_responsable` FOREIGN KEY (`responsable_id`) REFERENCES `personnels` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: enseignants_classes (Refactored to reference personnels)
CREATE TABLE IF NOT EXISTS `enseignants_classes` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `personnel_id` bigint(20) NOT NULL COMMENT 'Référence vers personnels (enseignants uniquement)',
  `classe_id` bigint(20) NOT NULL,
  `matiere_id` bigint(20) NOT NULL,
  `annee_scolaire_id` bigint(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_personnel_classe_matiere_annee` (`personnel_id`, `classe_id`, `matiere_id`, `annee_scolaire_id`),
  KEY `matiere_id` (`matiere_id`),
  KEY `annee_scolaire_id` (`annee_scolaire_id`),
  KEY `idx_classe_annee` (`classe_id`, `annee_scolaire_id`),
  KEY `idx_personnel_ec` (`personnel_id`),
  CONSTRAINT `fk_ec_personnel` FOREIGN KEY (`personnel_id`) REFERENCES `personnels` (`id`),
  CONSTRAINT `fk_ec_classe` FOREIGN KEY (`classe_id`) REFERENCES `classes` (`id`),
  CONSTRAINT `fk_ec_matiere` FOREIGN KEY (`matiere_id`) REFERENCES `matieres` (`id`),
  CONSTRAINT `fk_ec_annee` FOREIGN KEY (`annee_scolaire_id`) REFERENCES `annees_scolaires` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: absences
-- Commentaires de table : Absences et retards élèves par année scolaire
CREATE TABLE IF NOT EXISTS `absences` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `eleve_id` bigint(20) NOT NULL,
  `classe_id` bigint(20) NOT NULL,
  `annee_scolaire_id` bigint(20) NOT NULL,
  `date_absence` date NOT NULL,
  `type` enum('absence', 'retard') NOT NULL,
  `periode` enum('matin', 'apres_midi', 'journee') DEFAULT 'journee',
  `heure_debut` time DEFAULT NULL,
  `heure_fin` time DEFAULT NULL,
  `justifiee` tinyint(1) DEFAULT 0,
  `motif` text DEFAULT NULL,
  `piece_justificative` varchar(255) DEFAULT NULL,
  `saisi_par` bigint(20) DEFAULT NULL,
  `valide_par` bigint(20) DEFAULT NULL,
  `date_validation` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_classe_date` (`classe_id`, `date_absence`),
  KEY `idx_justifiee` (`justifiee`),
  KEY `idx_absences_date_type` (`date_absence`, `type`, `justifiee`),
  KEY `fk_abs_annee` (`annee_scolaire_id`),
  KEY `idx_eleve_annee_date` (`eleve_id`, `annee_scolaire_id`, `date_absence`),
  CONSTRAINT `fk_abs_eleve` FOREIGN KEY (`eleve_id`) REFERENCES `eleves` (`id`),
  CONSTRAINT `fk_abs_classe` FOREIGN KEY (`classe_id`) REFERENCES `classes` (`id`),
  CONSTRAINT `fk_abs_annee` FOREIGN KEY (`annee_scolaire_id`) REFERENCES `annees_scolaires` (`id`),
  CONSTRAINT `fk_abs_saisi` FOREIGN KEY (`saisi_par`) REFERENCES `users` (`id`),
  CONSTRAINT `fk_abs_valide` FOREIGN KEY (`valide_par`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: absences_personnels
-- Commentaires de table : Absences et congés du personnel par année scolaire
CREATE TABLE IF NOT EXISTS `absences_personnels` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `personnel_id` bigint(20) NOT NULL,
  `annee_scolaire_id` bigint(20) DEFAULT NULL,
  `type_absence` enum('conge_annuel', 'conge_maladie', 'conge_maternite', 'conge_paternite', 'conge_sans_solde', 'absence_autorisee', 'absence_non_justifiee', 'formation', 'mission', 'autre') NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `nb_jours` decimal(4,1) NOT NULL COMMENT 'Nombre de jours ouvrés',
  `nb_heures` decimal(6,2) DEFAULT NULL,
  `motif` text DEFAULT NULL,
  `piece_justificative` varchar(255) DEFAULT NULL,
  `statut` enum('demande', 'validee', 'refusee', 'annulee') DEFAULT 'demande',
  `demande_par` bigint(20) DEFAULT NULL COMMENT 'Personnel qui fait la demande',
  `date_demande` datetime DEFAULT NULL,
  `valide_par` bigint(20) DEFAULT NULL COMMENT 'Responsable qui valide',
  `date_validation` datetime DEFAULT NULL,
  `motif_refus` text DEFAULT NULL,
  `remplace_par` bigint(20) DEFAULT NULL COMMENT 'personnels -> id Personnel remplaçant',
  `commentaire_remplacement` text DEFAULT NULL,
  `deduit_salaire` tinyint(1) DEFAULT 0,
  `montant_deduction` decimal(10,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_personnel_dates` (`personnel_id`, `date_debut`, `date_fin`),
  KEY `idx_statut` (`statut`, `type_absence`),
  KEY `idx_dates` (`date_debut`, `date_fin`),
  KEY `idx_remplacant` (`remplace_par`),
  KEY `fk_ap_annee` (`annee_scolaire_id`),
  KEY `idx_personnel_annee` (`personnel_id`, `annee_scolaire_id`),
  CONSTRAINT `fk_ap_personnel` FOREIGN KEY (`personnel_id`) REFERENCES `personnels` (`id`),
  CONSTRAINT `fk_ap_remplace` FOREIGN KEY (`remplace_par`) REFERENCES `personnels` (`id`),
  CONSTRAINT `fk_ap_annee` FOREIGN KEY (`annee_scolaire_id`) REFERENCES `annees_scolaires` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: baremes_notation
-- Commentaires de table : Barèmes et seuils de notation par année
CREATE TABLE IF NOT EXISTS `baremes_notation` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `annee_scolaire_id` bigint(20) NOT NULL,
  `niveau_id` bigint(20) DEFAULT NULL COMMENT 'NULL = tous niveaux',
  `note_min` decimal(5,2) NOT NULL DEFAULT 0.00,
  `note_max` decimal(5,2) NOT NULL DEFAULT 20.00,
  `seuil_passage` decimal(5,2) NOT NULL DEFAULT 10.00 COMMENT 'Note minimale pour passer',
  `seuil_mention_passable` decimal(5,2) DEFAULT 10.00,
  `seuil_mention_assez_bien` decimal(5,2) DEFAULT 12.00,
  `seuil_mention_bien` decimal(5,2) DEFAULT 14.00,
  `seuil_mention_tres_bien` decimal(5,2) DEFAULT 16.00,
  `seuil_mention_excellence` decimal(5,2) DEFAULT 18.00,
  `actif` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_annee_niveau` (`annee_scolaire_id`, `niveau_id`),
  KEY `fk_bareme_niveau` (`niveau_id`),
  CONSTRAINT `fk_bareme_annee` FOREIGN KEY (`annee_scolaire_id`) REFERENCES `annees_scolaires` (`id`),
  CONSTRAINT `fk_bareme_niveau` FOREIGN KEY (`niveau_id`) REFERENCES `niveaux` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: calendrier_scolaire
-- Commentaires de table : Calendrier des vacances et jours fériés par année
CREATE TABLE IF NOT EXISTS `calendrier_scolaire` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `annee_scolaire_id` bigint(20) NOT NULL,
  `type` enum('vacances', 'ferie', 'pont', 'examen', 'conseil', 'rentree', 'sortie', 'autre') NOT NULL,
  `libelle` varchar(255) NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `description` text DEFAULT NULL,
  `bloque_cours` tinyint(1) DEFAULT 1 COMMENT 'Pas de cours ces jours',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_annee_dates` (`annee_scolaire_id`, `date_debut`, `date_fin`),
  CONSTRAINT `fk_cal_annee` FOREIGN KEY (`annee_scolaire_id`) REFERENCES `annees_scolaires` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: jours_feries
-- Commentaires de table : Jours fériés et calendrier scolaire
CREATE TABLE IF NOT EXISTS `jours_feries` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `annee_scolaire_id` bigint(20) NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `type` enum('ferie_national', 'vacances_scolaires', 'pont', 'journee_pedagogique', 'examen', 'autre') NOT NULL,
  `libelle` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `concerne` enum('tous', 'eleves', 'enseignants', 'administratifs') DEFAULT 'tous',
  `actif` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_annee_dates` (`annee_scolaire_id`, `date_debut`, `date_fin`),
  KEY `idx_type` (`type`, `actif`),
  CONSTRAINT `fk_jf_annee` FOREIGN KEY (`annee_scolaire_id`) REFERENCES `annees_scolaires` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: matieres_classes
-- Commentaires de table : Association matières-classes avec coefficients par année
CREATE TABLE IF NOT EXISTS `matieres_classes` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `matiere_id` bigint(20) NOT NULL,
  `classe_id` bigint(20) NOT NULL,
  `annee_scolaire_id` bigint(20) NOT NULL,
  `coefficient` decimal(3,2) NOT NULL DEFAULT 1.00,
  `heures_semaine` decimal(3,1) DEFAULT NULL,
  `obligatoire` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_matiere_classe_annee` (`matiere_id`, `classe_id`, `annee_scolaire_id`),
  KEY `fk_mc_classe` (`classe_id`),
  KEY `fk_mc_annee` (`annee_scolaire_id`),
  CONSTRAINT `fk_mc_matiere` FOREIGN KEY (`matiere_id`) REFERENCES `matieres` (`id`),
  CONSTRAINT `fk_mc_classe` FOREIGN KEY (`classe_id`) REFERENCES `classes` (`id`),
  CONSTRAINT `fk_mc_annee` FOREIGN KEY (`annee_scolaire_id`) REFERENCES `annees_scolaires` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: parametres_annee_scolaire
-- Commentaires de table : Paramètres configurables par année scolaire
CREATE TABLE IF NOT EXISTS `parametres_annee_scolaire` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `annee_scolaire_id` bigint(20) NOT NULL,
  `cle` varchar(100) NOT NULL,
  `valeur` text DEFAULT NULL,
  `type` enum('string', 'integer', 'boolean', 'json', 'decimal') DEFAULT 'string',
  `groupe` varchar(50) DEFAULT NULL COMMENT 'finances, pedagogie, discipline, etc.',
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_annee_cle` (`annee_scolaire_id`, `cle`),
  CONSTRAINT `fk_param_annee` FOREIGN KEY (`annee_scolaire_id`) REFERENCES `annees_scolaires` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: periodes
CREATE TABLE IF NOT EXISTS `periodes` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `annee_scolaire_id` bigint(20) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `type_periode` enum('trimestre', 'semestre', 'bimestre') DEFAULT 'trimestre',
  `numero` int(11) NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `date_conseil` date DEFAULT NULL COMMENT 'Date prévue du conseil de classe',
  `date_limite_saisie_notes` date DEFAULT NULL COMMENT 'Date limite pour saisir les notes',
  `date_edition_bulletins` date DEFAULT NULL COMMENT 'Date prévue impression bulletins',
  `actif` tinyint(1) DEFAULT 1,
  `conseil_tenu` tinyint(1) DEFAULT 0 COMMENT 'Conseil de classe effectué',
  `bulletins_edites` tinyint(1) DEFAULT 0 COMMENT 'Bulletins imprimés',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_annee_numero` (`annee_scolaire_id`, `numero`),
  KEY `idx_dates_cles` (`date_limite_saisie_notes`, `date_conseil`, `date_edition_bulletins`),
  CONSTRAINT `fk_period_annee` FOREIGN KEY (`annee_scolaire_id`) REFERENCES `annees_scolaires` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: bulletins
CREATE TABLE IF NOT EXISTS `bulletins` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `eleve_id` bigint(20) NOT NULL,
  `classe_id` bigint(20) NOT NULL,
  `periode_id` bigint(20) NOT NULL,
  `annee_scolaire_id` bigint(20) NOT NULL,
  `moyenne_generale` decimal(5,2) DEFAULT NULL,
  `total_points` decimal(7,2) DEFAULT NULL,
  `total_coefficients` decimal(5,2) DEFAULT NULL,
  `rang` int(11) DEFAULT NULL,
  `appreciation_generale` text DEFAULT NULL,
  `decision_conseil` enum('passe', 'redouble', 'exclu', 'en_attente') DEFAULT NULL,
  `statut` enum('brouillon', 'valide', 'imprime', 'envoye') DEFAULT 'brouillon',
  `date_validation` datetime DEFAULT NULL,
  `valide_par` bigint(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_eleve_periode` (`eleve_id`, `periode_id`, `annee_scolaire_id`),
  KEY `periode_id` (`periode_id`),
  KEY `idx_statut` (`statut`),
  KEY `idx_classe_periode` (`classe_id`, `periode_id`),
  KEY `idx_bulletins_classe_periode` (`classe_id`, `periode_id`, `statut`),
  KEY `idx_bulletins_classe_periode_statut` (`classe_id`, `periode_id`, `statut`),
  KEY `idx_bulletins_annee_statut` (`annee_scolaire_id`, `statut`, `classe_id`),
  CONSTRAINT `fk_bull_eleve` FOREIGN KEY (`eleve_id`) REFERENCES `eleves` (`id`),
  CONSTRAINT `fk_bull_classe` FOREIGN KEY (`classe_id`) REFERENCES `classes` (`id`),
  CONSTRAINT `fk_bull_periode` FOREIGN KEY (`periode_id`) REFERENCES `periodes` (`id`),
  CONSTRAINT `fk_bull_annee` FOREIGN KEY (`annee_scolaire_id`) REFERENCES `annees_scolaires` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: bulletins_notes
CREATE TABLE IF NOT EXISTS `bulletins_notes` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `bulletin_id` bigint(20) NOT NULL,
  `matiere_id` bigint(20) NOT NULL,
  `moyenne_interrogations` decimal(5,2) DEFAULT NULL,
  `note_examen` decimal(5,2) DEFAULT NULL,
  `note_bulletin` decimal(5,2) NOT NULL,
  `coefficient` decimal(3,2) DEFAULT NULL,
  `note_ponderee` decimal(7,2) DEFAULT NULL,
  `rang_matiere` int(11) DEFAULT NULL,
  `appreciation_matiere` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_bulletin_matiere` (`bulletin_id`, `matiere_id`),
  KEY `idx_bulletin` (`bulletin_id`),
  KEY `idx_matiere` (`matiere_id`),
  CONSTRAINT `fk_bn_bulletin` FOREIGN KEY (`bulletin_id`) REFERENCES `bulletins` (`id`),
  CONSTRAINT `fk_bn_matiere` FOREIGN KEY (`matiere_id`) REFERENCES `matieres` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: emplois_temps
CREATE TABLE IF NOT EXISTS `emplois_temps` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `classe_id` bigint(20) NOT NULL,
  `matiere_id` bigint(20) NOT NULL,
  `personnel_id` bigint(20) DEFAULT NULL,
  `annee_scolaire_id` bigint(20) NOT NULL,
  `jour_semaine` enum('lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi') NOT NULL,
  `heure_debut` time NOT NULL,
  `heure_fin` time NOT NULL,
  `remarque` text DEFAULT NULL COMMENT 'Notes particulières sur le créneau',
  `actif` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_classe_jour_heure` (`classe_id`, `jour_semaine`, `heure_debut`, `annee_scolaire_id`),
  KEY `matiere_id` (`matiere_id`),
  KEY `idx_classe_jour` (`classe_id`, `jour_semaine`),
  KEY `idx_enseignant_jour` (`jour_semaine`),
  KEY `idx_classe_annee` (`classe_id`, `annee_scolaire_id`),
  KEY `idx_personnel` (`personnel_id`),
  KEY `idx_emplois_temps_annee` (`annee_scolaire_id`, `classe_id`, `jour_semaine`),
  CONSTRAINT `fk_et_classe` FOREIGN KEY (`classe_id`) REFERENCES `classes` (`id`),
  CONSTRAINT `fk_et_matiere` FOREIGN KEY (`matiere_id`) REFERENCES `matieres` (`id`),
  CONSTRAINT `fk_et_personnel` FOREIGN KEY (`personnel_id`) REFERENCES `personnels` (`id`),
  CONSTRAINT `fk_et_annee` FOREIGN KEY (`annee_scolaire_id`) REFERENCES `annees_scolaires` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: examens_finaux
CREATE TABLE IF NOT EXISTS `examens_finaux` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `classe_id` bigint(20) NOT NULL,
  `matiere_id` bigint(20) NOT NULL,
  `personnel_id` bigint(20) DEFAULT NULL,
  `periode_id` bigint(20) NOT NULL,
  `annee_scolaire_id` bigint(20) NOT NULL,
  `nom` varchar(255) NOT NULL COMMENT 'Ex: Examen Trimestre 1',
  `date_examen` date NOT NULL,
  `heure_debut` time DEFAULT NULL,
  `heure_fin` time DEFAULT NULL,
  `duree` int(11) DEFAULT NULL COMMENT 'Durée en minutes',
  `note_sur` decimal(5,2) DEFAULT 20.00,
  `sujet_url` varchar(255) DEFAULT NULL COMMENT 'Fichier du sujet',
  `bareme_url` varchar(255) DEFAULT NULL COMMENT 'Fichier du barème',
  `description` text DEFAULT NULL,
  `consignes` text DEFAULT NULL,
  `statut` enum('planifie', 'en_cours', 'termine', 'annule') DEFAULT 'planifie',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_examen_matiere_periode` (`classe_id`, `matiere_id`, `periode_id`, `annee_scolaire_id`),
  KEY `matiere_id` (`matiere_id`),
  KEY `idx_date` (`date_examen`),
  KEY `idx_statut` (`statut`),
  KEY `idx_examens_periode_statut_date` (`periode_id`, `statut`, `date_examen`),
  KEY `idx_personnel_ef` (`personnel_id`),
  KEY `idx_examens_annee_periode` (`annee_scolaire_id`, `periode_id`, `statut`),
  CONSTRAINT `fk_ex_classe` FOREIGN KEY (`classe_id`) REFERENCES `classes` (`id`),
  CONSTRAINT `fk_ex_matiere` FOREIGN KEY (`matiere_id`) REFERENCES `matieres` (`id`),
  CONSTRAINT `fk_ex_personnel` FOREIGN KEY (`personnel_id`) REFERENCES `personnels` (`id`),
  CONSTRAINT `fk_ex_periode` FOREIGN KEY (`periode_id`) REFERENCES `periodes` (`id`),
  CONSTRAINT `fk_ex_annee` FOREIGN KEY (`annee_scolaire_id`) REFERENCES `annees_scolaires` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: notes_examens
CREATE TABLE IF NOT EXISTS `notes_examens` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `examen_id` bigint(20) NOT NULL,
  `eleve_id` bigint(20) NOT NULL,
  `note` decimal(5,2) NOT NULL,
  `absent` tinyint(1) DEFAULT 0,
  `appreciation` text DEFAULT NULL,
  `saisi_par` bigint(20) DEFAULT NULL COMMENT 'ID enseignant qui saisit',
  `date_saisie` timestamp NOT NULL DEFAULT current_timestamp(),
  `modifie_par` bigint(20) DEFAULT NULL,
  `date_modification` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_eleve_examen` (`eleve_id`, `examen_id`),
  KEY `examen_id` (`examen_id`),
  KEY `idx_eleve` (`eleve_id`),
  KEY `idx_note` (`note`),
  KEY `idx_absent` (`absent`),
  CONSTRAINT `fk_ne_examen` FOREIGN KEY (`examen_id`) REFERENCES `examens_finaux` (`id`),
  CONSTRAINT `fk_ne_eleve` FOREIGN KEY (`eleve_id`) REFERENCES `eleves` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: interrogations
CREATE TABLE IF NOT EXISTS `interrogations` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `classe_id` bigint(20) NOT NULL,
  `matiere_id` bigint(20) NOT NULL,
  `personnel_id` bigint(20) DEFAULT NULL,
  `periode_id` bigint(20) NOT NULL,
  `annee_scolaire_id` bigint(20) NOT NULL,
  `nom` varchar(255) NOT NULL COMMENT 'Ex: Interrogation 1, Interro Fractions',
  `date_interrogation` date NOT NULL,
  `duree` int(11) DEFAULT NULL COMMENT 'Durée en minutes',
  `note_sur` decimal(5,2) DEFAULT 20.00,
  `description` text DEFAULT NULL,
  `statut` enum('planifiee', 'en_cours', 'terminee', 'annulee') DEFAULT 'planifiee',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `matiere_id` (`matiere_id`),
  KEY `idx_classe_matiere_periode` (`classe_id`, `matiere_id`, `periode_id`),
  KEY `idx_date` (`date_interrogation`),
  KEY `idx_statut` (`statut`),
  KEY `idx_interrogations_periode_statut_date` (`periode_id`, `statut`, `date_interrogation`),
  KEY `idx_personnel_int` (`personnel_id`),
  KEY `idx_interrogations_annee` (`annee_scolaire_id`, `classe_id`, `periode_id`),
  CONSTRAINT `fk_int_classe` FOREIGN KEY (`classe_id`) REFERENCES `classes` (`id`),
  CONSTRAINT `fk_int_matiere` FOREIGN KEY (`matiere_id`) REFERENCES `matieres` (`id`),
  CONSTRAINT `fk_int_personnel` FOREIGN KEY (`personnel_id`) REFERENCES `personnels` (`id`),
  CONSTRAINT `fk_int_periode` FOREIGN KEY (`periode_id`) REFERENCES `periodes` (`id`),
  CONSTRAINT `fk_int_annee` FOREIGN KEY (`annee_scolaire_id`) REFERENCES `annees_scolaires` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: notes_interrogations
CREATE TABLE IF NOT EXISTS `notes_interrogations` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `interrogation_id` bigint(20) NOT NULL,
  `eleve_id` bigint(20) NOT NULL,
  `note` decimal(5,2) NOT NULL,
  `absent` tinyint(1) DEFAULT 0,
  `appreciation` text DEFAULT NULL,
  `saisi_par` bigint(20) DEFAULT NULL COMMENT 'ID enseignant qui saisit',
  `date_saisie` timestamp NOT NULL DEFAULT current_timestamp(),
  `modifie_par` bigint(20) DEFAULT NULL,
  `date_modification` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_eleve_interrogation` (`eleve_id`, `interrogation_id`),
  KEY `idx_eleve` (`eleve_id`),
  KEY `idx_note` (`note`),
  KEY `idx_absent` (`absent`),
  KEY `idx_notes_periode` (`interrogation_id`, `absent`),
  CONSTRAINT `fk_ni_interrogation` FOREIGN KEY (`interrogation_id`) REFERENCES `interrogations` (`id`),
  CONSTRAINT `fk_ni_eleve` FOREIGN KEY (`eleve_id`) REFERENCES `eleves` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: types_facture
CREATE TABLE IF NOT EXISTS `types_facture` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `libelle` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `prefixe_numero` varchar(10) NOT NULL,
  `actif` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: factures
CREATE TABLE IF NOT EXISTS `factures` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `numero_facture` varchar(50) NOT NULL,
  `eleve_id` bigint(20) NOT NULL,
  `annee_scolaire_id` bigint(20) NOT NULL,
  `type_facture_id` bigint(20) NOT NULL,
  `date_facture` date NOT NULL,
  `date_echeance` date DEFAULT NULL,
  `montant_total` decimal(10,2) NOT NULL,
  `montant_paye` decimal(10,2) DEFAULT 0.00,
  `montant_restant` decimal(10,2) NOT NULL,
  `statut` enum('impayee', 'partiellement_payee', 'payee', 'annulee') DEFAULT 'impayee',
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `numero_facture` (`numero_facture`),
  KEY `idx_eleve` (`eleve_id`),
  KEY `idx_statut` (`statut`),
  KEY `fk_factures_type_facture` (`type_facture_id`),
  KEY `idx_factures_eleve_annee` (`eleve_id`, `annee_scolaire_id`, `statut`),
  KEY `idx_factures_annee_statut` (`annee_scolaire_id`, `statut`, `date_echeance`),
  CONSTRAINT `fk_fact_eleve` FOREIGN KEY (`eleve_id`) REFERENCES `eleves` (`id`),
  CONSTRAINT `fk_fact_annee` FOREIGN KEY (`annee_scolaire_id`) REFERENCES `annees_scolaires` (`id`),
  CONSTRAINT `fk_fact_type` FOREIGN KEY (`type_facture_id`) REFERENCES `types_facture` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: types_frais
CREATE TABLE IF NOT EXISTS `types_frais` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(100) NOT NULL,
  `categorie` enum('inscription', 'scolarite', 'transport', 'cantine', 'autre') NOT NULL,
  `actif` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: types_frais_niveaux
CREATE TABLE IF NOT EXISTS `types_frais_niveaux` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `type_frais_id` bigint(20) NOT NULL,
  `niveau_id` bigint(20) NOT NULL,
  `montant` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_frais_niveau` (`type_frais_id`, `niveau_id`),
  KEY `niveau_id` (`niveau_id`),
  CONSTRAINT `fk_tfn_type` FOREIGN KEY (`type_frais_id`) REFERENCES `types_frais` (`id`),
  CONSTRAINT `fk_tfn_niveau` FOREIGN KEY (`niveau_id`) REFERENCES `niveaux` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: lignes_facture
CREATE TABLE IF NOT EXISTS `lignes_facture` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `facture_id` bigint(20) NOT NULL,
  `type_frais_id` bigint(20) NOT NULL,
  `designation` varchar(255) NOT NULL,
  `quantite` int(11) DEFAULT 1,
  `prix_unitaire` decimal(10,2) NOT NULL,
  `montant` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `facture_id` (`facture_id`),
  KEY `type_frais_id` (`type_frais_id`),
  CONSTRAINT `fk_lf_facture` FOREIGN KEY (`facture_id`) REFERENCES `factures` (`id`),
  CONSTRAINT `fk_lf_type` FOREIGN KEY (`type_frais_id`) REFERENCES `types_frais` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: echeanciers_ecolages
CREATE TABLE IF NOT EXISTS `echeanciers_ecolages` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `eleve_id` bigint(20) NOT NULL COMMENT 'Référence vers l''élève',
  `annee_scolaire_id` bigint(20) NOT NULL COMMENT 'Année scolaire concernée',
  `mois` tinyint(4) NOT NULL COMMENT 'Mois (1-12)',
  `annee` year(4) NOT NULL COMMENT 'Année de l''échéance',
  `mois_libelle` varchar(20) NOT NULL COMMENT 'Ex: Septembre, Octobre...',
  `date_limite` date NOT NULL COMMENT 'Date limite de paiement',
  `date_limite_normale` date NOT NULL COMMENT '30 du mois M',
  `date_limite_grace` date NOT NULL COMMENT '10 du mois M+1',
  `date_exclusion` date NOT NULL COMMENT '11 du mois M+1',
  `montant_du` decimal(10,2) NOT NULL COMMENT 'Montant de l''écolage du mois',
  `montant_paye` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Montant déjà payé',
  `montant_restant` decimal(10,2) DEFAULT NULL COMMENT 'Calculé automatiquement',
  `statut` enum('en_attente', 'normal', 'retard', 'partiellement_paye', 'paye', 'impaye_exclu', 'annule') NOT NULL DEFAULT 'en_attente',
  `date_paiement_complet` date DEFAULT NULL COMMENT 'Date du paiement complet',
  `nombre_paiements` int(11) DEFAULT 0 COMMENT 'Nombre de paiements partiels',
  `derniere_facture_id` bigint(20) DEFAULT NULL COMMENT 'Dernière facture liée',
  `jours_retard` int(11) DEFAULT NULL COMMENT 'Nombre de jours de retard',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` bigint(20) DEFAULT NULL COMMENT 'Utilisateur créateur',
  `updated_by` bigint(20) DEFAULT NULL COMMENT 'Dernier modificateur',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_eleve_mois_annee` (`eleve_id`, `annee_scolaire_id`, `mois`, `annee`),
  KEY `fk_echeancier_annee` (`annee_scolaire_id`),
  KEY `fk_echeancier_facture` (`derniere_facture_id`),
  KEY `idx_echeanciers_statut_date` (`statut`, `date_limite`),
  KEY `idx_ecolage_statut_dates` (`statut`, `date_exclusion`, `annee_scolaire_id`),
  CONSTRAINT `fk_ee_eleve` FOREIGN KEY (`eleve_id`) REFERENCES `eleves` (`id`),
  CONSTRAINT `fk_ee_annee` FOREIGN KEY (`annee_scolaire_id`) REFERENCES `annees_scolaires` (`id`),
  CONSTRAINT `fk_ee_facture` FOREIGN KEY (`derniere_facture_id`) REFERENCES `factures` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: modes_paiement
CREATE TABLE IF NOT EXISTS `modes_paiement` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `libelle` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `necessite_reference` tinyint(1) DEFAULT 0,
  `actif` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: paiements
CREATE TABLE IF NOT EXISTS `paiements` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `numero_paiement` varchar(50) NOT NULL,
  `facture_id` bigint(20) NOT NULL,
  `date_paiement` date NOT NULL,
  `montant` decimal(10,2) NOT NULL,
  `mode_paiement_id` bigint(20) NOT NULL,
  `reference_paiement` varchar(100) DEFAULT NULL,
  `remarque` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `numero_paiement` (`numero_paiement`),
  KEY `mode_paiement_id` (`mode_paiement_id`),
  KEY `idx_facture` (`facture_id`),
  KEY `idx_date` (`date_paiement`),
  KEY `idx_paiements_date_mode` (`date_paiement`, `mode_paiement_id`),
  CONSTRAINT `fk_pay_facture` FOREIGN KEY (`facture_id`) REFERENCES `factures` (`id`),
  CONSTRAINT `fk_pay_mode` FOREIGN KEY (`mode_paiement_id`) REFERENCES `modes_paiement` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: ecolages_payes
CREATE TABLE IF NOT EXISTS `ecolages_payes` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `eleve_id` bigint(20) NOT NULL,
  `annee_scolaire_id` bigint(20) NOT NULL,
  `mois` int(11) NOT NULL,
  `annee` int(11) NOT NULL,
  `ligne_facture_id` bigint(20) NOT NULL,
  `paiement_id` bigint(20) NOT NULL,
  `montant` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_eleve_mois` (`eleve_id`, `annee_scolaire_id`, `mois`),
  KEY `annee_scolaire_id` (`annee_scolaire_id`),
  KEY `ligne_facture_id` (`ligne_facture_id`),
  KEY `paiement_id` (`paiement_id`),
  CONSTRAINT `fk_epay_eleve` FOREIGN KEY (`eleve_id`) REFERENCES `eleves` (`id`),
  CONSTRAINT `fk_epay_annee` FOREIGN KEY (`annee_scolaire_id`) REFERENCES `annees_scolaires` (`id`),
  CONSTRAINT `fk_epay_ligne` FOREIGN KEY (`ligne_facture_id`) REFERENCES `lignes_facture` (`id`),
  CONSTRAINT `fk_epay_paiement` FOREIGN KEY (`paiement_id`) REFERENCES `paiements` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: inscriptions
CREATE TABLE IF NOT EXISTS `inscriptions` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `eleve_id` bigint(20) NOT NULL,
  `classe_id` bigint(20) NOT NULL,
  `annee_scolaire_id` bigint(20) NOT NULL,
  `date_inscription` date NOT NULL,
  `type_inscription` enum('nouvelle', 'reinscription') NOT NULL,
  `statut_dossier` enum('pre_inscription', 'documents_en_cours', 'documents_valides', 'paiement_inscription_attente', 'paiement_ecolage_attente', 'validee', 'bloquee', 'rejetee') DEFAULT 'pre_inscription',
  `facture_inscription_id` bigint(20) DEFAULT NULL COMMENT 'Facture générée après validation documents',
  `statut` enum('validee', 'annulee') DEFAULT 'validee',
  `valide_par` bigint(20) DEFAULT NULL COMMENT 'User qui a validé l''inscription',
  `date_validation` datetime DEFAULT NULL COMMENT 'Date validation inscription',
  `motif_rejet` text DEFAULT NULL,
  `commentaire_interne` text DEFAULT NULL,
  `frais_inscription_paye` tinyint(1) DEFAULT 0,
  `premier_mois_ecolage_paye` tinyint(1) DEFAULT 0,
  `bloquee` tinyint(1) DEFAULT 1 COMMENT 'Bloquée tant que frais+1er mois non payés',
  `date_deblocage` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_eleve_annee` (`eleve_id`, `annee_scolaire_id`),
  KEY `classe_id` (`classe_id`),
  KEY `facture_inscription_id` (`facture_inscription_id`),
  KEY `idx_statut_dossier` (`statut_dossier`, `annee_scolaire_id`),
  KEY `idx_inscriptions_annee_statut` (`annee_scolaire_id`, `statut_dossier`, `statut`),
  KEY `idx_inscr_blocage` (`bloquee`, `frais_inscription_paye`, `premier_mois_ecolage_paye`),
  CONSTRAINT `fk_insc_eleve` FOREIGN KEY (`eleve_id`) REFERENCES `eleves` (`id`),
  CONSTRAINT `fk_insc_classe` FOREIGN KEY (`classe_id`) REFERENCES `classes` (`id`),
  CONSTRAINT `fk_insc_annee` FOREIGN KEY (`annee_scolaire_id`) REFERENCES `annees_scolaires` (`id`),
  CONSTRAINT `fk_insc_facture` FOREIGN KEY (`facture_inscription_id`) REFERENCES `factures` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: inscriptions_historique
-- Commentaires de table : Historique des changements de statut d'inscription
CREATE TABLE IF NOT EXISTS `inscriptions_historique` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `inscription_id` bigint(20) NOT NULL,
  `ancien_statut` enum('pre_inscription', 'documents_en_cours', 'documents_valides', 'paiement_en_attente', 'validee', 'rejetee') DEFAULT NULL,
  `nouveau_statut` enum('pre_inscription', 'documents_en_cours', 'documents_valides', 'paiement_en_attente', 'validee', 'rejetee') NOT NULL,
  `commentaire` text DEFAULT NULL,
  `modifie_par` bigint(20) DEFAULT NULL COMMENT 'User qui a effectué le changement',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_inscription` (`inscription_id`),
  KEY `idx_date` (`created_at`),
  CONSTRAINT `fk_insc_hist_inscription` FOREIGN KEY (`inscription_id`) REFERENCES `inscriptions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: types_sanctions
CREATE TABLE IF NOT EXISTS `types_sanctions` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `libelle` varchar(100) NOT NULL,
  `gravite` int(11) DEFAULT 1,
  `description` text DEFAULT NULL,
  `actif` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: sanctions
-- Commentaires de table : Sanctions disciplinaires par année scolaire
CREATE TABLE IF NOT EXISTS `sanctions` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `eleve_id` bigint(20) NOT NULL,
  `classe_id` bigint(20) NOT NULL,
  `annee_scolaire_id` bigint(20) NOT NULL,
  `type_sanction_id` bigint(20) NOT NULL,
  `date_sanction` date NOT NULL,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `duree_jours` int(11) DEFAULT NULL,
  `motif` text NOT NULL,
  `description_incident` text DEFAULT NULL,
  `mesures_educatives` text DEFAULT NULL,
  `emis_par` bigint(20) DEFAULT NULL COMMENT 'Enseignant/Surveillant qui émet',
  `valide_par` bigint(20) DEFAULT NULL COMMENT 'Direction qui valide',
  `date_validation` datetime DEFAULT NULL,
  `statut` enum('en_attente', 'validee', 'executee', 'annulee') DEFAULT 'en_attente',
  `parent_notifie` tinyint(1) DEFAULT 0,
  `date_notification` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `classe_id` (`classe_id`),
  KEY `type_sanction_id` (`type_sanction_id`),
  KEY `idx_eleve` (`eleve_id`),
  KEY `idx_date` (`date_sanction`),
  KEY `idx_statut` (`statut`),
  KEY `idx_sanctions_statut_date` (`statut`, `date_sanction`, `eleve_id`),
  KEY `idx_sanc_annee` (`annee_scolaire_id`, `date_sanction`),
  CONSTRAINT `fk_sanc_eleve` FOREIGN KEY (`eleve_id`) REFERENCES `eleves` (`id`),
  CONSTRAINT `fk_sanc_classe` FOREIGN KEY (`classe_id`) REFERENCES `classes` (`id`),
  CONSTRAINT `fk_sanc_type` FOREIGN KEY (`type_sanction_id`) REFERENCES `types_sanctions` (`id`),
  CONSTRAINT `fk_sanc_annee` FOREIGN KEY (`annee_scolaire_id`) REFERENCES `annees_scolaires` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: types_documents
CREATE TABLE IF NOT EXISTS `types_documents` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `libelle` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `template_url` varchar(255) DEFAULT NULL,
  `actif` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: documents
CREATE TABLE IF NOT EXISTS `documents` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `type_document_id` bigint(20) NOT NULL,
  `entite_type` enum('eleve', 'enseignant', 'classe', 'autre') NOT NULL,
  `entite_id` bigint(20) DEFAULT NULL,
  `annee_scolaire_id` bigint(20) DEFAULT NULL,
  `titre` varchar(255) NOT NULL,
  `numero_document` varchar(100) DEFAULT NULL,
  `fichier_url` varchar(255) NOT NULL,
  `date_emission` date DEFAULT NULL,
  `date_validite` date DEFAULT NULL,
  `delivre_par` bigint(20) DEFAULT NULL COMMENT 'User qui a généré le document',
  `valide_par` bigint(20) DEFAULT NULL,
  `date_validation` datetime DEFAULT NULL,
  `statut` enum('brouillon', 'valide', 'annule') DEFAULT 'brouillon',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `numero_document` (`numero_document`),
  KEY `type_document_id` (`type_document_id`),
  KEY `idx_entite` (`entite_type`, `entite_id`),
  KEY `idx_numero` (`numero_document`),
  KEY `idx_doc_annee` (`annee_scolaire_id`, `entite_type`),
  CONSTRAINT `fk_doc_type` FOREIGN KEY (`type_document_id`) REFERENCES `types_documents` (`id`),
  CONSTRAINT `fk_doc_annee` FOREIGN KEY (`annee_scolaire_id`) REFERENCES `annees_scolaires` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: annonces
CREATE TABLE IF NOT EXISTS `annonces` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) NOT NULL,
  `contenu` text NOT NULL,
  `type` enum('generale', 'urgente', 'administrative', 'pedagogique') DEFAULT 'generale',
  `cible` enum('tous', 'enseignants', 'parents', 'eleves', 'classe') DEFAULT 'tous',
  `classe_id` bigint(20) DEFAULT NULL COMMENT 'Si cible = classe',
  `annee_scolaire_id` bigint(20) DEFAULT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `publie_par` bigint(20) NOT NULL,
  `actif` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `classe_id` (`classe_id`),
  KEY `idx_actif_dates` (`actif`, `date_debut`, `date_fin`),
  KEY `idx_annon_annee` (`annee_scolaire_id`, `actif`),
  CONSTRAINT `fk_annon_classe` FOREIGN KEY (`classe_id`) REFERENCES `classes` (`id`),
  CONSTRAINT `fk_annon_annee` FOREIGN KEY (`annee_scolaire_id`) REFERENCES `annees_scolaires` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: messages
CREATE TABLE IF NOT EXISTS `messages` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `expediteur_id` bigint(20) NOT NULL,
  `destinataire_id` bigint(20) NOT NULL,
  `sujet` varchar(255) NOT NULL,
  `contenu` text NOT NULL,
  `lu` tinyint(1) DEFAULT 0,
  `date_lecture` datetime DEFAULT NULL,
  `parent_message_id` bigint(20) DEFAULT NULL COMMENT 'Pour les réponses',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `parent_message_id` (`parent_message_id`),
  KEY `idx_destinataire_lu` (`destinataire_id`, `lu`),
  KEY `idx_expediteur` (`expediteur_id`),
  KEY `idx_date` (`created_at`),
  KEY `idx_messages_dest_lu_date` (`destinataire_id`, `lu`, `created_at`),
  CONSTRAINT `fk_msg_exp` FOREIGN KEY (`expediteur_id`) REFERENCES `users` (`id`),
  CONSTRAINT `fk_msg_dest` FOREIGN KEY (`destinataire_id`) REFERENCES `users` (`id`),
  CONSTRAINT `fk_msg_parent` FOREIGN KEY (`parent_message_id`) REFERENCES `messages` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: notifications
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `type` enum('info', 'warning', 'success', 'error', 'urgent') DEFAULT 'info',
  `titre` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `url_action` varchar(255) DEFAULT NULL COMMENT 'Lien vers l''élément concerné',
  `icone` varchar(50) DEFAULT NULL,
  `lu` tinyint(1) DEFAULT 0,
  `date_lecture` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_user_lu` (`user_id`, `lu`),
  KEY `idx_date` (`created_at`),
  KEY `idx_notifications_user_lu_date` (`user_id`, `lu`, `created_at`),
  CONSTRAINT `fk_notif_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: parametres_ecole
CREATE TABLE IF NOT EXISTS `parametres_ecole` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `cle` varchar(100) NOT NULL,
  `valeur` text DEFAULT NULL,
  `type` enum('string', 'integer', 'boolean', 'json') DEFAULT 'string',
  `groupe` varchar(50) DEFAULT NULL COMMENT 'generale, academique, financiere, etc.',
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `cle` (`cle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: sessions
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext DEFAULT NULL,
  `last_activity` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_last_activity` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: relances
CREATE TABLE IF NOT EXISTS `relances` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `facture_id` bigint(20) NOT NULL,
  `type` enum('email', 'sms', 'courrier', 'appel') NOT NULL,
  `date_relance` date NOT NULL,
  `message` text DEFAULT NULL,
  `statut` enum('programmee', 'envoyee', 'echouee') DEFAULT 'programmee',
  `envoye_par` bigint(20) DEFAULT NULL,
  `envoye_le` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `facture_id` (`facture_id`),
  CONSTRAINT `fk_rel_facture` FOREIGN KEY (`facture_id`) REFERENCES `factures` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: logs_activites
CREATE TABLE IF NOT EXISTS `logs_activites` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `module` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `entite_type` varchar(50) DEFAULT NULL COMMENT 'eleve, facture, bulletin, etc.',
  `entite_id` bigint(20) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_action` (`action`),
  KEY `idx_module` (`module`),
  KEY `idx_entite` (`entite_type`, `entite_id`),
  KEY `idx_date` (`created_at`),
  CONSTRAINT `fk_log_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: tarifs_inscription
-- Commentaires de table : Tarifs inscription = Frais inscription + Premier mois écolage OBLIGATOIRES
CREATE TABLE IF NOT EXISTS `tarifs_inscription` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `annee_scolaire_id` bigint(20) NOT NULL,
  `niveau_id` bigint(20) NOT NULL,
  `frais_inscription` decimal(10,2) NOT NULL COMMENT 'Frais inscription unique',
  `ecolage_mensuel` decimal(10,2) NOT NULL COMMENT 'Montant écolage par mois',
  `mois_debut_annee` tinyint(4) NOT NULL DEFAULT 9 COMMENT '9 = Septembre',
  `frais_supplementaires` decimal(10,2) DEFAULT 0.00 COMMENT 'Autres frais obligatoires',
  `description_frais_supp` varchar(255) DEFAULT NULL,
  `date_debut_inscription` date DEFAULT NULL,
  `date_fin_inscription` date DEFAULT NULL,
  `actif` tinyint(1) DEFAULT 1,
  `type_inscription` enum('nouvelle', 'reinscription') DEFAULT NULL,
  `montant_premier_mois_obligatoire` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_annee_niveau` (`annee_scolaire_id`, `niveau_id`),
  KEY `idx_niveau` (`niveau_id`),
  KEY `idx_actif_annee` (`actif`, `annee_scolaire_id`),
  CONSTRAINT `fk_tarif_annee` FOREIGN KEY (`annee_scolaire_id`) REFERENCES `annees_scolaires` (`id`),
  CONSTRAINT `fk_tarif_niveau` FOREIGN KEY (`niveau_id`) REFERENCES `niveaux` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: exigences_documents_inscription
-- Commentaires de table : Configuration des documents requis selon type inscription
CREATE TABLE IF NOT EXISTS `exigences_documents_inscription` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `annee_scolaire_id` bigint(20) NOT NULL,
  `type_inscription` enum('nouvelle', 'reinscription') NOT NULL,
  `type_document` enum('acte_naissance', 'certificat_scolarite', 'bulletin_notes', 'photo_identite', 'certificat_medical', 'fiche_renseignement', 'autre') NOT NULL,
  `obligatoire` tinyint(1) DEFAULT 1 COMMENT '1=Requis, 0=Optionnel',
  `bloquant` tinyint(1) DEFAULT 0 COMMENT '1=Bloque inscription si absent, 0=Alerte seulement',
  `libelle` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `format_accepte` varchar(255) DEFAULT NULL COMMENT 'pdf,jpg,png',
  `taille_max_mo` decimal(5,2) DEFAULT 5.00,
  `validite_jours` int(11) DEFAULT NULL COMMENT 'Nombre de jours validité (ex: certificat médical 3 mois)',
  `nombre_exemplaires` int(11) DEFAULT 1 COMMENT 'Nombre de copies requises',
  `instructions` text DEFAULT NULL COMMENT 'Consignes pour le document',
  `message_aide` text DEFAULT NULL COMMENT 'Message d''aide contextuelle',
  `exemple_url` varchar(500) DEFAULT NULL COMMENT 'Lien vers exemple',
  `ordre` int(11) DEFAULT 0,
  `actif` tinyint(1) DEFAULT 1,
  `cycles_concernes` text DEFAULT NULL COMMENT 'JSON: pour quels cycles',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_annee_type_doc` (`annee_scolaire_id`, `type_inscription`, `type_document`),
  KEY `idx_actif` (`actif`),
  KEY `idx_annee_type_actif` (`annee_scolaire_id`, `type_inscription`, `actif`, `ordre`),
  CONSTRAINT `fk_exig_annee` FOREIGN KEY (`annee_scolaire_id`) REFERENCES `annees_scolaires` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: documents_inscription
-- Commentaires de table : Documents fournis lors de l'inscription (acte naissance, certificat scolarité, etc.)
CREATE TABLE IF NOT EXISTS `documents_inscription` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `inscription_id` bigint(20) DEFAULT NULL COMMENT 'NULL si pas encore inscrit',
  `eleve_id` bigint(20) NOT NULL,
  `type_document` enum('acte_naissance', 'certificat_scolarite', 'bulletin_notes', 'photo_identite', 'autre') NOT NULL,
  `ordre_affichage` int(11) DEFAULT 0,
  `nom_fichier` varchar(255) NOT NULL,
  `chemin_fichier` varchar(500) NOT NULL,
  `taille_fichier` int(11) DEFAULT NULL COMMENT 'Taille en octets',
  `type_mime` varchar(100) DEFAULT NULL COMMENT 'image/jpeg, application/pdf, etc.',
  `statut` enum('en_attente', 'valide', 'refuse') DEFAULT 'en_attente',
  `obligatoire_pour_validation` tinyint(1) DEFAULT 1 COMMENT 'Document requis pour valider dossier',
  `valide_par` bigint(20) DEFAULT NULL COMMENT 'User qui a validé',
  `date_validation` datetime DEFAULT NULL,
  `motif_refus` text DEFAULT NULL,
  `numero_document` varchar(100) DEFAULT NULL COMMENT 'Numéro acte naissance, etc.',
  `date_emission` date DEFAULT NULL,
  `date_expiration` date DEFAULT NULL COMMENT 'Pour docs avec validité limitée',
  `lieu_emission` varchar(255) DEFAULT NULL,
  `remarques` text DEFAULT NULL,
  `telecharge_par` bigint(20) DEFAULT NULL COMMENT 'User qui a uploadé',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_inscription` (`inscription_id`),
  KEY `idx_eleve` (`eleve_id`),
  KEY `idx_type_statut` (`type_document`, `statut`),
  KEY `idx_statut` (`statut`),
  KEY `idx_eleve_statut_obligatoire` (`eleve_id`, `statut`, `obligatoire_pour_validation`),
  CONSTRAINT `fk_doc_insc_inscription` FOREIGN KEY (`inscription_id`) REFERENCES `inscriptions` (`id`),
  CONSTRAINT `fk_doc_insc_eleve` FOREIGN KEY (`eleve_id`) REFERENCES `eleves` (`id`),
  CONSTRAINT `fk_doc_insc_user` FOREIGN KEY (`valide_par`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: conseils_classe
-- Commentaires de table : Conseils de classe par période
CREATE TABLE IF NOT EXISTS `conseils_classe` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `classe_id` bigint(20) NOT NULL,
  `periode_id` bigint(20) NOT NULL,
  `annee_scolaire_id` bigint(20) NOT NULL,
  `date_conseil` date NOT NULL,
  `heure_debut` time DEFAULT NULL,
  `heure_fin` time DEFAULT NULL,
  `president_conseil` bigint(20) DEFAULT NULL COMMENT 'Généralement chef établissement',
  `secretaire` bigint(20) DEFAULT NULL COMMENT 'Enseignant secrétaire',
  `ordre_du_jour` text DEFAULT NULL,
  `pv_url` varchar(255) DEFAULT NULL COMMENT 'Procès-verbal scanné',
  `moyenne_classe` decimal(5,2) DEFAULT NULL,
  `taux_reussite` decimal(5,2) DEFAULT NULL COMMENT 'Pourcentage admis',
  `nb_felicitations` int(11) DEFAULT 0,
  `nb_encouragements` int(11) DEFAULT 0,
  `nb_avertissements_travail` int(11) DEFAULT 0,
  `nb_avertissements_conduite` int(11) DEFAULT 0,
  `appreciation_generale` text DEFAULT NULL,
  `points_forts` text DEFAULT NULL,
  `points_amelioration` text DEFAULT NULL,
  `recommendations` text DEFAULT NULL,
  `statut` enum('prevu', 'en_cours', 'cloture', 'annule') DEFAULT 'prevu',
  `date_cloture` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_classe_periode` (`classe_id`, `periode_id`, `annee_scolaire_id`),
  KEY `idx_date` (`date_conseil`),
  KEY `idx_statut` (`statut`),
  KEY `periode_id` (`periode_id`),
  KEY `annee_scolaire_id` (`annee_scolaire_id`),
  KEY `president_conseil` (`president_conseil`),
  KEY `secretaire` (`secretaire`),
  CONSTRAINT `fk_cc_classe` FOREIGN KEY (`classe_id`) REFERENCES `classes` (`id`),
  CONSTRAINT `fk_cc_periode` FOREIGN KEY (`periode_id`) REFERENCES `periodes` (`id`),
  CONSTRAINT `fk_cc_annee` FOREIGN KEY (`annee_scolaire_id`) REFERENCES `annees_scolaires` (`id`),
  CONSTRAINT `fk_cc_pres` FOREIGN KEY (`president_conseil`) REFERENCES `personnels` (`id`),
  CONSTRAINT `fk_cc_sec` FOREIGN KEY (`secretaire`) REFERENCES `personnels` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: decisions_conseil
-- Commentaires de table : Décisions individuelles des conseils de classe
CREATE TABLE IF NOT EXISTS `decisions_conseil` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `conseil_classe_id` bigint(20) NOT NULL,
  `eleve_id` bigint(20) NOT NULL,
  `bulletin_id` bigint(20) DEFAULT NULL COMMENT 'Lien vers bulletin concerné',
  `distinction` enum('felicitations', 'compliments', 'encouragements', 'tableau_honneur', 'prix_excellence', 'aucune') DEFAULT NULL,
  `avertissement` enum('travail', 'conduite', 'assiduite', 'travail_et_conduite', 'aucun') DEFAULT NULL,
  `appreciation_conseil` text DEFAULT NULL COMMENT 'Appréciation générale du conseil',
  `points_positifs` text DEFAULT NULL,
  `points_vigilance` text DEFAULT NULL,
  `conseils_eleve` text DEFAULT NULL,
  `decision_passage` enum('passage_acquis', 'passage_probable', 'passage_conditionnel', 'redoublement_envisage', 'reorientation_suggeree', 'non_decide') DEFAULT NULL COMMENT 'Uniquement si conseil de fin d année',
  `necessite_suivi` tinyint(1) DEFAULT 0,
  `type_suivi` enum('entretien_direction', 'tutorat', 'soutien_scolaire', 'orientation', 'psychologue', 'autre') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_conseil_eleve` (`conseil_classe_id`, `eleve_id`),
  KEY `idx_eleve` (`eleve_id`),
  KEY `idx_bulletin` (`bulletin_id`),
  KEY `idx_distinction` (`distinction`),
  KEY `idx_decision` (`decision_passage`),
  CONSTRAINT `fk_dc_conseil` FOREIGN KEY (`conseil_classe_id`) REFERENCES `conseils_classe` (`id`),
  CONSTRAINT `fk_dc_eleve` FOREIGN KEY (`eleve_id`) REFERENCES `eleves` (`id`),
  CONSTRAINT `fk_dc_bulletin` FOREIGN KEY (`bulletin_id`) REFERENCES `bulletins` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: parcours_eleves
-- Commentaires de table : Historique du parcours scolaire de chaque élève
CREATE TABLE IF NOT EXISTS `parcours_eleves` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `eleve_id` bigint(20) NOT NULL,
  `annee_scolaire_id` bigint(20) NOT NULL,
  `classe_id` bigint(20) NOT NULL,
  `inscription_id` bigint(20) DEFAULT NULL COMMENT 'Lien vers inscription de l''année',
  `resultat` enum('en_cours', 'admis', 'admis_avec_mention', 'redouble', 'reoriente', 'abandonne', 'exclus', 'transfere') DEFAULT 'en_cours',
  `mention` enum('passable', 'assez_bien', 'bien', 'tres_bien', 'excellence') DEFAULT NULL COMMENT 'Si admis avec mention',
  `moyenne_annuelle` decimal(5,2) DEFAULT NULL,
  `rang_classe` int(11) DEFAULT NULL,
  `effectif_classe` int(11) DEFAULT NULL COMMENT 'Total élèves de la classe',
  `classe_suivante_id` bigint(20) DEFAULT NULL COMMENT 'Classe prévue année suivante',
  `motif_reorientation` text DEFAULT NULL,
  `conseil_orientation` text DEFAULT NULL COMMENT 'Recommandations du conseil',
  `date_debut` date NOT NULL,
  `date_fin` date DEFAULT NULL,
  `date_decision_conseil` date DEFAULT NULL,
  `saisi_par` bigint(20) DEFAULT NULL,
  `valide_par` bigint(20) DEFAULT NULL,
  `date_validation` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_eleve_annee` (`eleve_id`, `annee_scolaire_id`),
  KEY `idx_eleve` (`eleve_id`, `annee_scolaire_id`),
  KEY `idx_classe_annee` (`classe_id`, `annee_scolaire_id`),
  KEY `idx_resultat` (`resultat`, `annee_scolaire_id`),
  KEY `idx_classe_suivante` (`classe_suivante_id`),
  KEY `annee_scolaire_id` (`annee_scolaire_id`),
  KEY `inscription_id` (`inscription_id`),
  CONSTRAINT `fk_parc_eleve` FOREIGN KEY (`eleve_id`) REFERENCES `eleves` (`id`),
  CONSTRAINT `fk_parc_annee` FOREIGN KEY (`annee_scolaire_id`) REFERENCES `annees_scolaires` (`id`),
  CONSTRAINT `fk_parc_classe` FOREIGN KEY (`classe_id`) REFERENCES `classes` (`id`),
  CONSTRAINT `fk_parc_insc` FOREIGN KEY (`inscription_id`) REFERENCES `inscriptions` (`id`),
  CONSTRAINT `fk_parc_next` FOREIGN KEY (`classe_suivante_id`) REFERENCES `classes` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: passages_conditionnels
-- Commentaires de table : Conditions pour passage en classe supérieure
CREATE TABLE IF NOT EXISTS `passages_conditionnels` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `parcours_id` bigint(20) NOT NULL COMMENT 'Lien vers parcours_eleves',
  `eleve_id` bigint(20) NOT NULL,
  `annee_scolaire_id` bigint(20) NOT NULL,
  `type_condition` enum('rattrapage_matiere', 'moyenne_minimale', 'assiduite', 'comportement', 'stage_ete', 'autre') NOT NULL,
  `matiere_id` bigint(20) DEFAULT NULL COMMENT 'Si rattrapage matière spécifique',
  `description_condition` text NOT NULL,
  `note_minimale_requise` decimal(5,2) DEFAULT NULL,
  `delai_limite` date DEFAULT NULL,
  `statut` enum('en_attente', 'en_cours', 'validee', 'non_validee', 'annulee') DEFAULT 'en_attente',
  `note_obtenue` decimal(5,2) DEFAULT NULL,
  `date_evaluation` date DEFAULT NULL,
  `commentaire_evaluation` text DEFAULT NULL,
  `consequence_si_echec` enum('redoublement', 'reorientation', 'exclusion') DEFAULT NULL,
  `decide_par` bigint(20) DEFAULT NULL COMMENT 'Qui a décidé la condition',
  `evalue_par` bigint(20) DEFAULT NULL COMMENT 'Qui a évalué',
  `date_decision` datetime DEFAULT NULL,
  `date_evaluation_finale` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_parcours` (`parcours_id`),
  KEY `idx_eleve_annee` (`eleve_id`, `annee_scolaire_id`),
  KEY `idx_statut` (`statut`, `delai_limite`),
  KEY `idx_matiere` (`matiere_id`),
  KEY `annee_scolaire_id` (`annee_scolaire_id`),
  CONSTRAINT `fk_pc_parcours` FOREIGN KEY (`parcours_id`) REFERENCES `parcours_eleves` (`id`),
  CONSTRAINT `fk_pc_eleve` FOREIGN KEY (`eleve_id`) REFERENCES `eleves` (`id`),
  CONSTRAINT `fk_pc_matiere` FOREIGN KEY (`matiere_id`) REFERENCES `matieres` (`id`),
  CONSTRAINT `fk_pc_annee` FOREIGN KEY (`annee_scolaire_id`) REFERENCES `annees_scolaires` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: abonnements_transport
CREATE TABLE IF NOT EXISTS `abonnements_transport` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `eleve_id` bigint(20) NOT NULL,
  `annee_scolaire_id` bigint(20) NOT NULL,
  `tarif_transport_id` bigint(20) NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `montant_total` decimal(10,2) NOT NULL,
  `montant_paye` decimal(10,2) DEFAULT 0.00,
  `statut` enum('actif', 'suspendu', 'termine', 'annule') DEFAULT 'actif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_abt_eleve` (`eleve_id`),
  KEY `fk_abt_annee` (`annee_scolaire_id`),
  KEY `fk_abt_tarif` (`tarif_transport_id`),
  CONSTRAINT `fk_abt_eleve` FOREIGN KEY (`eleve_id`) REFERENCES `eleves` (`id`),
  CONSTRAINT `fk_abt_annee` FOREIGN KEY (`annee_scolaire_id`) REFERENCES `annees_scolaires` (`id`),
  CONSTRAINT `fk_abt_tarif` FOREIGN KEY (`tarif_transport_id`) REFERENCES `tarifs_transport` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: alertes_parents
CREATE TABLE IF NOT EXISTS `alertes_parents` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `eleve_id` bigint(20) NOT NULL,
  `parent_id` bigint(20) NOT NULL,
  `annee_scolaire_id` bigint(20) NOT NULL,
  `type_alerte` enum('retard_frequent', 'retard_paiement_ecolage', 'absence_frequente', 'mauvaise_note', 'mauvais_comportement', 'exclusion_imminente') NOT NULL,
  `titre` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `priorite` enum('basse', 'normale', 'haute', 'urgente') DEFAULT 'normale',
  `telephone_destination` varchar(20) NOT NULL,
  `statut_envoi` enum('en_attente', 'envoye', 'echec', 'lu') DEFAULT 'en_attente',
  `date_envoi` datetime DEFAULT NULL,
  `nombre_tentatives` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_eleve_type` (`eleve_id`, `type_alerte`),
  KEY `fk_alerte_parent` (`parent_id`),
  KEY `fk_alerte_annee` (`annee_scolaire_id`),
  KEY `idx_alertes_envoi` (`statut_envoi`, `priorite`, `date_envoi`),
  CONSTRAINT `fk_alert_eleve` FOREIGN KEY (`eleve_id`) REFERENCES `eleves` (`id`),
  CONSTRAINT `fk_alerte_parent` FOREIGN KEY (`parent_id`) REFERENCES `parents` (`id`),
  CONSTRAINT `fk_alerte_annee` FOREIGN KEY (`annee_scolaire_id`) REFERENCES `annees_scolaires` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: articles
CREATE TABLE IF NOT EXISTS `articles` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `libelle` varchar(200) NOT NULL,
  `type_article` enum('tenue_sport', 'tenue_fete', 'fourniture', 'uniforme', 'autre') NOT NULL,
  `obligatoire` tinyint(1) DEFAULT 0,
  `cycles_concernes` text DEFAULT NULL COMMENT 'JSON: cycle_ids',
  `actif` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: commandes_articles
CREATE TABLE IF NOT EXISTS `commandes_articles` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `eleve_id` bigint(20) NOT NULL,
  `annee_scolaire_id` bigint(20) NOT NULL,
  `numero_commande` varchar(50) NOT NULL,
  `date_commande` date NOT NULL,
  `montant_total` decimal(10,2) NOT NULL,
  `montant_paye` decimal(10,2) DEFAULT 0.00,
  `statut` enum('en_attente', 'confirmee', 'livree', 'annulee') DEFAULT 'en_attente',
  `facture_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `numero_commande` (`numero_commande`),
  KEY `fk_cmd_eleve` (`eleve_id`),
  KEY `fk_cmd_facture` (`facture_id`),
  CONSTRAINT `fk_cmd_eleve` FOREIGN KEY (`eleve_id`) REFERENCES `eleves` (`id`),
  CONSTRAINT `fk_cmd_annee` FOREIGN KEY (`annee_scolaire_id`) REFERENCES `annees_scolaires` (`id`),
  CONSTRAINT `fk_cmd_facture` FOREIGN KEY (`facture_id`) REFERENCES `factures` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: fiches_paie
CREATE TABLE IF NOT EXISTS `fiches_paie` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `personnel_id` bigint(20) NOT NULL,
  `annee_scolaire_id` bigint(20) NOT NULL,
  `mois` tinyint(4) NOT NULL,
  `annee` year(4) NOT NULL,
  `salaire_brut` decimal(10,2) NOT NULL,
  `heures_travaillees` decimal(6,2) DEFAULT NULL,
  `heures_absence` decimal(6,2) DEFAULT 0.00,
  `heures_conge_maladie` decimal(6,2) DEFAULT 0.00,
  `heures_conge_sans_solde` decimal(6,2) DEFAULT 0.00,
  `montant_deduction_absences` decimal(10,2) DEFAULT 0.00,
  `montant_deduction_conge_maladie` decimal(10,2) DEFAULT 0.00,
  `salaire_net` decimal(10,2) NOT NULL,
  `date_paiement` date DEFAULT NULL,
  `statut` enum('en_preparation', 'validee', 'payee') DEFAULT 'en_preparation',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_personnel_mois` (`personnel_id`, `mois`, `annee`, `annee_scolaire_id`),
  CONSTRAINT `fk_fp_personnel` FOREIGN KEY (`personnel_id`) REFERENCES `personnels` (`id`),
  CONSTRAINT `fk_fp_annee` FOREIGN KEY (`annee_scolaire_id`) REFERENCES `annees_scolaires` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: lignes_commandes_articles
CREATE TABLE IF NOT EXISTS `lignes_commandes_articles` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `commande_id` bigint(20) NOT NULL,
  `article_id` bigint(20) NOT NULL,
  `quantite` int(11) NOT NULL DEFAULT 1,
  `prix_unitaire` decimal(10,2) NOT NULL,
  `montant` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_lca_commande` (`commande_id`),
  KEY `fk_lca_article` (`article_id`),
  CONSTRAINT `fk_lca_commande` FOREIGN KEY (`commande_id`) REFERENCES `commandes_articles` (`id`),
  CONSTRAINT `fk_lca_article` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: salaires_personnels
CREATE TABLE IF NOT EXISTS `salaires_personnels` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `personnel_id` bigint(20) NOT NULL,
  `annee_scolaire_id` bigint(20) NOT NULL,
  `type_contrat` enum('horaire_40h', 'horaire_matiere', 'mensuel') NOT NULL,
  `taux_horaire` decimal(10,2) DEFAULT NULL,
  `salaire_base_mensuel` decimal(10,2) DEFAULT NULL,
  `heures_max_semaine` decimal(5,2) DEFAULT 40.00,
  `heures_conge_maladie_non_deduites` decimal(5,2) DEFAULT 80.00,
  `date_debut` date NOT NULL,
  `date_fin` date DEFAULT NULL,
  `actif` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_sal_personnel` (`personnel_id`),
  KEY `fk_sal_annee` (`annee_scolaire_id`),
  CONSTRAINT `fk_sal_personnel` FOREIGN KEY (`personnel_id`) REFERENCES `personnels` (`id`),
  CONSTRAINT `fk_sal_annee` FOREIGN KEY (`annee_scolaire_id`) REFERENCES `annees_scolaires` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: seuils_alertes
CREATE TABLE IF NOT EXISTS `seuils_alertes` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `annee_scolaire_id` bigint(20) NOT NULL,
  `type_alerte` enum('retards', 'absences', 'notes', 'comportement') NOT NULL,
  `seuil_nombre` int(11) NOT NULL COMMENT 'Ex: 3 retards',
  `periode_jours` int(11) DEFAULT 30,
  `note_minimale` decimal(5,2) DEFAULT NULL,
  `actif` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_annee_type` (`annee_scolaire_id`, `type_alerte`),
  CONSTRAINT `fk_seuil_annee` FOREIGN KEY (`annee_scolaire_id`) REFERENCES `annees_scolaires` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: statuts_eleves_ecolage
CREATE TABLE IF NOT EXISTS `statuts_eleves_ecolage` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `eleve_id` bigint(20) NOT NULL,
  `annee_scolaire_id` bigint(20) NOT NULL,
  `mois` tinyint(4) NOT NULL,
  `annee` year(4) NOT NULL,
  `statut` enum('normal', 'retard', 'invalide', 'exclu', 'paye') NOT NULL DEFAULT 'normal',
  `peut_suivre_cours` tinyint(1) DEFAULT 1 COMMENT '0=exclu du cours',
  `date_exclusion` date DEFAULT NULL,
  `alerte_envoyee` tinyint(1) DEFAULT 0,
  `date_alerte` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_eleve_mois` (`eleve_id`, `annee_scolaire_id`, `mois`, `annee`),
  KEY `fk_see_annee` (`annee_scolaire_id`),
  KEY `idx_statut_eleve_cours` (`peut_suivre_cours`, `statut`),
  CONSTRAINT `fk_see_eleve` FOREIGN KEY (`eleve_id`) REFERENCES `eleves` (`id`),
  CONSTRAINT `fk_see_annee` FOREIGN KEY (`annee_scolaire_id`) REFERENCES `annees_scolaires` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: tarifs_articles
CREATE TABLE IF NOT EXISTS `tarifs_articles` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `article_id` bigint(20) NOT NULL,
  `annee_scolaire_id` bigint(20) NOT NULL,
  `prix_unitaire` decimal(10,2) NOT NULL,
  `taille` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_article_annee_taille` (`article_id`, `annee_scolaire_id`, `taille`),
  KEY `fk_ta_annee` (`annee_scolaire_id`),
  CONSTRAINT `fk_ta_article` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`),
  CONSTRAINT `fk_ta_annee` FOREIGN KEY (`annee_scolaire_id`) REFERENCES `annees_scolaires` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: tarifs_transport
CREATE TABLE IF NOT EXISTS `tarifs_transport` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `annee_scolaire_id` bigint(20) NOT NULL,
  `zone` varchar(100) NOT NULL,
  `tarif_mensuel` decimal(10,2) NOT NULL,
  `tarif_annuel` decimal(10,2) DEFAULT NULL,
  `actif` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_annee_zone` (`annee_scolaire_id`, `zone`),
  CONSTRAINT `fk_tt_annee` FOREIGN KEY (`annee_scolaire_id`) REFERENCES `annees_scolaires` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Créer la table manquante
CREATE TABLE IF NOT EXISTS `users_roles` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `role_id` bigint(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_role` (`user_id`, `role_id`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `fk_ur_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ur_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- View: vue_stats_absences
CREATE OR REPLACE VIEW `vue_stats_absences` AS
SELECT 
    a.eleve_id,
    e.matricule,
    CONCAT(e.nom, ' ', e.prenom) AS eleve,
    c.nom AS classe,
    COUNT(CASE WHEN a.type = 'absence' THEN 1 END) AS nb_absences,
    COUNT(CASE WHEN a.type = 'retard' THEN 1 END) AS nb_retards,
    COUNT(CASE WHEN a.justifiee = 0 THEN 1 END) AS nb_non_justifiees,
    MAX(a.date_absence) AS derniere_absence
FROM absences a
JOIN eleves e ON a.eleve_id = e.id
JOIN classes c ON a.classe_id = c.id
GROUP BY a.eleve_id, e.matricule, eleve, c.nom;

-- View: vue_suivi_inscriptions
CREATE OR REPLACE VIEW `vue_suivi_inscriptions` AS
SELECT 
    i.id AS inscription_id,
    i.statut_dossier,
    i.type_inscription,
    e.id AS eleve_id,
    e.matricule,
    e.nom,
    e.prenom,
    e.sexe,
    e.date_naissance,
    c.nom AS classe,
    n.libelle AS niveau,
    a.libelle AS annee_scolaire,
    i.date_inscription,
    i.date_validation,
    (SELECT COUNT(*) FROM exigences_documents_inscription ed WHERE ed.annee_scolaire_id = i.annee_scolaire_id AND ed.type_inscription = i.type_inscription AND ed.obligatoire = 1) AS nb_docs_requis,
    (SELECT COUNT(*) FROM documents_inscription di WHERE di.inscription_id = i.id AND di.statut = 'valide') AS nb_docs_valides,
    (SELECT COUNT(*) FROM documents_inscription di WHERE di.inscription_id = i.id AND di.statut = 'refuse') AS nb_docs_refuses,
    i.facture_inscription_id,
    f.numero_facture,
    f.montant_total AS montant_inscription,
    f.montant_paye,
    f.montant_restant,
    f.statut AS statut_paiement,
    GROUP_CONCAT(DISTINCT CONCAT(p.nom, ' ', p.prenom) SEPARATOR ', ') AS parents,
    (SELECT p2.telephone FROM eleves_parents ep2 JOIN parents p2 ON ep2.parent_id = p2.id WHERE ep2.eleve_id = e.id LIMIT 1) AS telephone_contact,
    CASE 
        WHEN i.bloquee = 1 AND i.frais_inscription_paye = 0 THEN 'urgente'
        WHEN i.bloquee = 1 AND i.premier_mois_ecolage_paye = 0 THEN 'haute'
        WHEN DATEDIFF(CURDATE(), i.date_inscription) > 30 THEN 'haute'
        WHEN DATEDIFF(CURDATE(), i.date_inscription) > 15 THEN 'normale'
        ELSE 'basse'
    END AS priorite,
    DATEDIFF(CURDATE(), i.date_inscription) AS jours_depuis_inscription,
    i.created_at,
    i.updated_at
FROM inscriptions i
JOIN eleves e ON i.eleve_id = e.id
JOIN classes c ON i.classe_id = c.id
JOIN annees_scolaires a ON i.annee_scolaire_id = a.id
JOIN niveaux n ON c.niveau_id = n.id
LEFT JOIN factures f ON i.facture_inscription_id = f.id
LEFT JOIN eleves_parents ep ON e.id = ep.eleve_id
LEFT JOIN parents p ON ep.parent_id = p.id
GROUP BY i.id, i.statut_dossier, i.type_inscription, e.id, e.matricule, e.nom, e.prenom, e.sexe, e.date_naissance, c.nom, n.libelle, a.libelle, i.date_inscription, i.date_validation, i.facture_inscription_id, f.numero_facture, f.montant_total, f.montant_paye, f.montant_restant, f.statut, i.bloquee, i.frais_inscription_paye, i.premier_mois_ecolage_paye, i.created_at, i.updated_at;

-- View: vue_stats_classe
CREATE OR REPLACE VIEW `vue_stats_classe` AS
SELECT 
    c.id AS classe_id,
    c.nom AS classe,
    p.id AS periode_id,
    p.nom AS periode,
    COUNT(b.id) AS nb_bulletins,
    AVG(b.moyenne_generale) AS moyenne_classe,
    MIN(b.moyenne_generale) AS note_min,
    MAX(b.moyenne_generale) AS note_max,
    SUM(CASE WHEN b.moyenne_generale >= 10 THEN 1 ELSE 0 END) AS nb_admis,
    SUM(CASE WHEN b.moyenne_generale < 10 THEN 1 ELSE 0 END) AS nb_non_admis
FROM classes c
JOIN periodes p ON p.annee_scolaire_id = c.annee_scolaire_id
LEFT JOIN bulletins b ON b.classe_id = c.id AND b.periode_id = p.id
GROUP BY c.id, c.nom, p.id, p.nom;

-- View: vue_enseignants
CREATE OR REPLACE VIEW `vue_enseignants` AS
SELECT 
    p.id,
    p.matricule,
    p.nom,
    p.prenom,
    p.date_naissance,
    p.lieu_naissance,
    p.sexe,
    p.cin,
    p.telephone,
    p.email,
    p.adresse,
    p.photo,
    p.date_embauche,
    p.type_contrat,
    p.statut,
    pe.diplome,
    pe.specialite,
    pe.grade,
    pe.anciennete_annees,
    p.created_at,
    p.updated_at
FROM personnels p
JOIN personnels_enseignants pe ON p.id = pe.personnel_id
WHERE p.type_personnel = 'enseignant';

-- View: vue_personnels_administratifs
CREATE OR REPLACE VIEW `vue_personnels_administratifs` AS
SELECT 
    p.id,
    p.matricule,
    p.nom,
    p.prenom,
    p.date_naissance,
    p.sexe,
    p.cin,
    p.telephone,
    p.email,
    p.adresse,
    p.photo,
    p.date_embauche,
    p.type_contrat,
    p.statut,
    pa.departement,
    pa.niveau_acces,
    post.code AS code_poste,
    post.libelle AS poste,
    post.niveau_hierarchique,
    CONCAT(resp.nom, ' ', resp.prenom) AS responsable,
    pa.responsable_id,
    p.created_at,
    p.updated_at
FROM personnels p
JOIN personnels_administratifs pa ON p.id = pa.personnel_id
JOIN postes_administratifs post ON pa.poste_id = post.id
LEFT JOIN personnels resp ON pa.responsable_id = resp.id
WHERE p.type_personnel = 'administratif';

-- View: vue_finances_eleve
CREATE OR REPLACE VIEW `vue_finances_eleve` AS
SELECT 
    e.id AS eleve_id,
    e.matricule,
    e.nom,
    e.prenom,
    c.nom AS classe,
    a.libelle AS annee_scolaire,
    COUNT(f.id) AS nb_factures,
    SUM(f.montant_total) AS total_facture,
    SUM(f.montant_paye) AS total_paye,
    SUM(f.montant_restant) AS total_restant,
    SUM(CASE WHEN f.statut = 'impayee' THEN 1 ELSE 0 END) AS nb_impayees,
    SUM(CASE WHEN f.statut = 'partiellement_payee' THEN 1 ELSE 0 END) AS nb_partielles
FROM eleves e
JOIN inscriptions i ON i.eleve_id = e.id
JOIN classes c ON i.classe_id = c.id
JOIN annees_scolaires a ON i.annee_scolaire_id = a.id
LEFT JOIN factures f ON f.eleve_id = e.id AND f.annee_scolaire_id = a.id
GROUP BY e.id, e.matricule, e.nom, e.prenom, c.nom, a.libelle;

-- View: vue_bulletin_complet
CREATE OR REPLACE VIEW `vue_bulletin_complet` AS
SELECT 
    b.id AS bulletin_id,
    e.id AS eleve_id,
    e.matricule,
    e.nom AS nom_eleve,
    e.prenom AS prenom_eleve,
    c.id AS classe_id,
    c.nom AS classe,
    s.libelle AS serie,
    p.id AS periode_id,
    p.nom AS periode,
    p.numero AS periode_numero,
    a.libelle AS annee_scolaire,
    m.id AS matiere_id,
    m.nom AS matiere,
    m.code AS matiere_code,
    bn.moyenne_interrogations,
    bn.note_examen,
    bn.note_bulletin,
    bn.coefficient,
    bn.note_ponderee,
    bn.rang_matiere,
    bn.appreciation_matiere,
    b.moyenne_generale,
    b.total_points,
    b.total_coefficients,
    b.rang,
    b.appreciation_generale,
    b.decision_conseil,
    b.statut,
    b.date_validation,
    b.valide_par
FROM bulletins b
JOIN eleves e ON b.eleve_id = e.id
JOIN classes c ON b.classe_id = c.id
LEFT JOIN series s ON c.serie_id = s.id
JOIN periodes p ON b.periode_id = p.id
JOIN annees_scolaires a ON b.annee_scolaire_id = a.id
JOIN bulletins_notes bn ON bn.bulletin_id = b.id
JOIN matieres m ON bn.matiere_id = m.id;

-- View: vue_alertes_a_envoyer_aujourdhui
CREATE OR REPLACE VIEW `vue_alertes_a_envoyer_aujourdhui` AS
SELECT 
    ap.eleve_id,
    e.nom,
    e.prenom,
    ap.parent_id,
    p.telephone,
    ap.type_alerte,
    ap.priorite,
    ap.message
FROM alertes_parents ap
JOIN eleves e ON ap.eleve_id = e.id
JOIN parents p ON ap.parent_id = p.id
WHERE ap.statut_envoi = 'en_attente'
  AND DATE(ap.created_at) = CURDATE();

-- View: vue_alertes_documents_manquants
CREATE OR REPLACE VIEW `vue_alertes_documents_manquants` AS
SELECT 
    i.id AS inscription_id,
    e.matricule,
    e.nom,
    e.prenom,
    c.nom AS classe,
    i.date_inscription,
    DATEDIFF(CURDATE(), i.date_inscription) AS jours_depuis_inscription,
    (SELECT COUNT(*) FROM exigences_documents_inscription ed WHERE ed.annee_scolaire_id = i.annee_scolaire_id AND ed.type_inscription = i.type_inscription AND ed.obligatoire = 1) AS nb_docs_requis,
    (SELECT COUNT(*) FROM documents_inscription di WHERE di.inscription_id = i.id) AS nb_docs_fournis,
    (SELECT COUNT(*) FROM exigences_documents_inscription ed WHERE ed.annee_scolaire_id = i.annee_scolaire_id AND ed.type_inscription = i.type_inscription AND ed.obligatoire = 1) - (SELECT COUNT(*) FROM documents_inscription di WHERE di.inscription_id = i.id AND di.statut = 'valide') AS nb_docs_manquants,
    GROUP_CONCAT(ed.libelle SEPARATOR ', ') AS documents_manquants,
    CASE 
        WHEN DATEDIFF(CURDATE(), i.date_inscription) > 30 THEN 'urgente'
        WHEN DATEDIFF(CURDATE(), i.date_inscription) > 15 THEN 'haute'
        ELSE 'normale'
    END AS priorite
FROM inscriptions i
JOIN eleves e ON i.eleve_id = e.id
JOIN classes c ON i.classe_id = c.id
LEFT JOIN exigences_documents_inscription ed ON ed.annee_scolaire_id = i.annee_scolaire_id AND ed.type_inscription = i.type_inscription AND ed.obligatoire = 1
LEFT JOIN documents_inscription di ON di.inscription_id = i.id AND di.type_document = ed.type_document AND di.statut = 'valide'
WHERE i.statut_dossier IN ('pre_inscription', 'documents_en_cours')
  AND di.id IS NULL
GROUP BY i.id, e.matricule, e.nom, e.prenom, c.nom, i.date_inscription;

-- View: vue_alertes_parcours
CREATE OR REPLACE VIEW `vue_alertes_parcours` AS
SELECT 
    CASE 
        WHEN pe.moyenne_annuelle < 10 THEN 'moyenne_insuffisante'
        WHEN pe.resultat = 'redouble' THEN 'redoublement'
        WHEN pe.resultat = 'reoriente' THEN 'reorientation'
        ELSE 'autre'
    END AS type_alerte,
    CONCAT(e.nom, ' ', e.prenom) AS eleve,
    e.matricule,
    c.nom AS classe,
    a.libelle AS annee_scolaire,
    pe.moyenne_annuelle,
    CONCAT('Alerte pour ', pe.resultat) AS message,
    CASE 
        WHEN pe.moyenne_annuelle < 8 THEN 'urgente'
        WHEN pe.moyenne_annuelle < 10 THEN 'haute'
        ELSE 'normale'
    END AS priorite,
    pe.id AS parcours_id
FROM parcours_eleves pe
JOIN eleves e ON pe.eleve_id = e.id
JOIN classes c ON pe.classe_id = c.id
JOIN annees_scolaires a ON pe.annee_scolaire_id = a.id
WHERE pe.resultat IN ('redouble', 'reoriente') OR pe.moyenne_annuelle < 10;

-- View: vue_alertes_systeme
CREATE OR REPLACE VIEW `vue_alertes_systeme` AS
SELECT 
    'retard_paiement' AS type_alerte,
    CONCAT('Retard de paiement pour facture #', f.numero_facture) AS message,
    f.id AS entite_id,
    'facture' AS entite_type,
    f.montant_restant AS montant,
    CASE 
        WHEN DATEDIFF(CURDATE(), f.date_echeance) > 30 THEN 'urgente'
        WHEN DATEDIFF(CURDATE(), f.date_echeance) > 15 THEN 'haute'
        ELSE 'normale'
    END AS priorite,
    f.date_echeance AS date_reference
FROM factures f
WHERE f.statut IN ('impayee', 'partiellement_payee')
  AND f.date_echeance < CURDATE()
UNION ALL
SELECT 
    'exclusion_imminente' AS type_alerte,
    CONCAT('Exclusion imminente pour ', e.matricule) AS message,
    e.id AS entite_id,
    'eleve' AS entite_type,
    NULL AS montant,
    'urgente' AS priorite,
    ee.date_exclusion AS date_reference
FROM echeanciers_ecolages ee
JOIN eleves e ON ee.eleve_id = e.id
WHERE ee.statut = 'impaye_exclu'
  AND ee.date_exclusion <= DATE_ADD(CURDATE(), INTERVAL 3 DAY);

-- View: vue_stats_absences_annee
CREATE OR REPLACE VIEW `vue_stats_absences_annee` AS
SELECT 
    abs.eleve_id,
    abs.annee_scolaire_id,
    a.libelle AS annee_scolaire,
    e.matricule,
    CONCAT(e.nom, ' ', e.prenom) AS eleve,
    c.nom AS classe,
    COUNT(CASE WHEN abs.type = 'absence' THEN 1 END) AS nb_absences,
    COUNT(CASE WHEN abs.type = 'retard' THEN 1 END) AS nb_retards,
    COUNT(CASE WHEN abs.justifiee = 0 THEN 1 END) AS nb_non_justifiees,
    COUNT(CASE WHEN abs.justifiee = 0 AND abs.type = 'absence' THEN 1 END) AS nb_absences_non_justifiees,
    MAX(abs.date_absence) AS derniere_absence
FROM absences abs
JOIN eleves e ON abs.eleve_id = e.id
JOIN classes c ON abs.classe_id = c.id
JOIN annees_scolaires a ON abs.annee_scolaire_id = a.id
GROUP BY abs.eleve_id, abs.annee_scolaire_id, a.libelle, e.matricule, eleve, c.nom;

-- View: vue_dashboard_direction
CREATE OR REPLACE VIEW `vue_dashboard_direction` AS
SELECT 
    (SELECT COUNT(*) FROM eleves WHERE statut = 'actif') AS total_eleves_actifs,
    (SELECT COUNT(*) FROM personnels WHERE type_personnel = 'enseignant' AND statut = 'actif') AS total_enseignants_actifs,
    (SELECT COUNT(*) FROM classes WHERE statut = 'actif') AS total_classes_actives,
    (SELECT COUNT(*) FROM eleves WHERE statut = 'actif' AND sexe = 'M') AS total_garcons,
    (SELECT COUNT(*) FROM eleves WHERE statut = 'actif' AND sexe = 'F') AS total_filles,
    (SELECT SUM(montant_restant) FROM factures WHERE statut IN ('impayee', 'partiellement_payee')) AS total_impayes,
    (SELECT COUNT(*) FROM factures WHERE statut = 'impayee') AS nb_factures_impayees,
    (SELECT COUNT(*) FROM absences WHERE date_absence = CURDATE()) AS absences_aujourdhui,
    (SELECT COUNT(*) FROM absences WHERE date_absence = CURDATE() AND justifiee = 0) AS absences_non_justifiees_aujourdhui,
    (SELECT COUNT(*) FROM sanctions WHERE statut = 'en_attente') AS sanctions_en_cours,
    (SELECT AVG(moyenne_generale) FROM bulletins WHERE statut = 'valide') AS moyenne_generale_ecole;

-- View: vue_echeanciers_classe
CREATE OR REPLACE VIEW `vue_echeanciers_classe` AS
SELECT 
    c.id AS classe_id,
    c.nom AS classe,
    a.libelle AS annee_scolaire,
    ee.mois_libelle,
    ee.mois,
    ee.annee,
    COUNT(DISTINCT ee.eleve_id) AS nb_eleves,
    SUM(ee.montant_du) AS montant_total_attendu,
    SUM(ee.montant_paye) AS montant_total_paye,
    SUM(ee.montant_restant) AS montant_total_restant,
    SUM(CASE WHEN ee.statut = 'paye' THEN 1 ELSE 0 END) AS nb_payes,
    SUM(CASE WHEN ee.statut = 'retard' THEN 1 ELSE 0 END) AS nb_retards,
    SUM(CASE WHEN ee.statut = 'en_attente' THEN 1 ELSE 0 END) AS nb_attente
FROM echeanciers_ecolages ee
JOIN inscriptions i ON ee.eleve_id = i.eleve_id AND ee.annee_scolaire_id = i.annee_scolaire_id
JOIN classes c ON i.classe_id = c.id
JOIN annees_scolaires a ON ee.annee_scolaire_id = a.id
GROUP BY c.id, c.nom, a.libelle, ee.mois_libelle, ee.mois, ee.annee;

-- View: vue_effectifs_classes
CREATE OR REPLACE VIEW `vue_effectifs_classes` AS
SELECT 
    c.id AS classe_id,
    c.nom AS classe,
    c.code AS code_classe,
    n.libelle AS niveau,
    s.libelle AS serie,
    c.annee_scolaire_id,
    a.libelle AS annee_scolaire,
    c.capacite,
    COUNT(DISTINCT i.eleve_id) AS effectif_inscrit,
    COUNT(DISTINCT CASE WHEN i.statut = 'validee' THEN i.eleve_id END) AS effectif_valide,
    (c.capacite - COUNT(DISTINCT i.eleve_id)) AS places_disponibles,
    CONCAT(pp.nom, ' ', pp.prenom) AS professeur_principal
FROM classes c
JOIN niveaux n ON c.niveau_id = n.id
LEFT JOIN series s ON c.serie_id = s.id
JOIN annees_scolaires a ON c.annee_scolaire_id = a.id
LEFT JOIN inscriptions i ON c.id = i.classe_id AND c.annee_scolaire_id = i.annee_scolaire_id
LEFT JOIN personnels pp ON c.professeur_principal_id = pp.id
GROUP BY c.id, c.nom, c.code, n.libelle, s.libelle, c.annee_scolaire_id, a.libelle, c.capacite, professeur_principal;

-- View: vue_emploi_temps
CREATE OR REPLACE VIEW `vue_emploi_temps` AS
SELECT 
    et.id,
    et.classe_id,
    c.nom AS classe,
    et.matiere_id,
    m.code AS matiere_code,
    m.nom AS matiere,
    et.personnel_id AS enseignant_id,
    CONCAT(p.nom, ' ', p.prenom) AS enseignant,
    et.jour_semaine,
    et.heure_debut,
    et.heure_fin,
    TIMESTAMPDIFF(MINUTE, CONCAT('2000-01-01 ', et.heure_debut), CONCAT('2000-01-01 ', et.heure_fin)) AS duree_minutes,
    et.remarque,
    a.libelle AS annee_scolaire
FROM emplois_temps et
JOIN classes c ON et.classe_id = c.id
JOIN matieres m ON et.matiere_id = m.id
LEFT JOIN personnels p ON et.personnel_id = p.id
JOIN annees_scolaires a ON et.annee_scolaire_id = a.id
WHERE et.actif = 1;

-- View: vue_emploi_temps_enseignant
CREATE OR REPLACE VIEW `vue_emploi_temps_enseignant` AS
SELECT 
    et.personnel_id AS enseignant_id,
    CONCAT(p.nom, ' ', p.prenom) AS enseignant,
    et.jour_semaine,
    et.heure_debut,
    et.heure_fin,
    c.nom AS classe,
    m.nom AS matiere,
    TIMESTAMPDIFF(MINUTE, CONCAT('2000-01-01 ', et.heure_debut), CONCAT('2000-01-01 ', et.heure_fin)) AS duree_minutes
FROM emplois_temps et
JOIN personnels p ON et.personnel_id = p.id
JOIN classes c ON et.classe_id = c.id
JOIN matieres m ON et.matiere_id = m.id
WHERE et.actif = 1;

-- View: vue_emploi_temps_v2
CREATE OR REPLACE VIEW `vue_emploi_temps_v2` AS
SELECT 
    et.id,
    et.classe_id,
    c.nom AS classe,
    et.matiere_id,
    m.code AS matiere_code,
    m.nom AS matiere,
    et.personnel_id,
    CONCAT(p.nom, ' ', p.prenom) AS enseignant,
    et.jour_semaine,
    et.heure_debut,
    et.heure_fin,
    TIMESTAMPDIFF(MINUTE, CONCAT('2000-01-01 ', et.heure_debut), CONCAT('2000-01-01 ', et.heure_fin)) AS duree_minutes,
    et.remarque,
    a.libelle AS annee_scolaire
FROM emplois_temps et
JOIN classes c ON et.classe_id = c.id
JOIN matieres m ON et.matiere_id = m.id
LEFT JOIN personnels p ON et.personnel_id = p.id
JOIN annees_scolaires a ON et.annee_scolaire_id = a.id
WHERE et.actif = 1;

-- View: vue_matieres_par_classe
CREATE OR REPLACE VIEW `vue_matieres_par_classe` AS
SELECT 
    mc.classe_id,
    c.nom AS classe,
    mc.matiere_id,
    m.code,
    m.nom AS matiere,
    mc.coefficient,
    mc.heures_semaine,
    mc.obligatoire,
    n.id AS niveau_id,
    n.libelle AS niveau,
    s.id AS serie_id,
    s.libelle AS serie
FROM matieres_classes mc
JOIN classes c ON mc.classe_id = c.id
JOIN matieres m ON mc.matiere_id = m.id
JOIN niveaux n ON c.niveau_id = n.id
LEFT JOIN series s ON c.serie_id = s.id;

-- View: vue_notes_bulletin
CREATE OR REPLACE VIEW `vue_notes_bulletin` AS
SELECT 
    e.id AS eleve_id,
    e.nom,
    e.prenom,
    c.id AS classe_id,
    c.nom AS classe,
    s.libelle AS serie,
    m.id AS matiere_id,
    m.nom AS matiere,
    p.id AS periode_id,
    p.nom AS periode,
    mc.coefficient,
    AVG(ni.note) AS moyenne_interrogations,
    ne.note AS note_examen,
    bn.note_bulletin
FROM eleves e
JOIN inscriptions i ON e.id = i.eleve_id
JOIN classes c ON i.classe_id = c.id
LEFT JOIN series s ON c.serie_id = s.id
JOIN periodes p ON p.annee_scolaire_id = i.annee_scolaire_id
JOIN matieres_classes mc ON mc.classe_id = c.id AND mc.annee_scolaire_id = i.annee_scolaire_id
JOIN matieres m ON mc.matiere_id = m.id
LEFT JOIN notes_interrogations ni ON ni.eleve_id = e.id
LEFT JOIN interrogations int ON ni.interrogation_id = int.id AND int.matiere_id = m.id AND int.periode_id = p.id
LEFT JOIN notes_examens ne ON ne.eleve_id = e.id
LEFT JOIN examens_finaux ex ON ne.examen_id = ex.id AND ex.matiere_id = m.id AND ex.periode_id = p.id
LEFT JOIN bulletins b ON b.eleve_id = e.id AND b.periode_id = p.id
LEFT JOIN bulletins_notes bn ON bn.bulletin_id = b.id AND bn.matiere_id = m.id
GROUP BY e.id, e.nom, e.prenom, c.id, c.nom, s.libelle, m.id, m.nom, p.id, p.nom, mc.coefficient, ne.note, bn.note_bulletin;

-- View: vue_parcours_eleves_complet
CREATE OR REPLACE VIEW `vue_parcours_eleves_complet` AS
SELECT 
    pe.id AS parcours_id,
    pe.eleve_id,
    e.matricule,
    e.nom,
    e.prenom,
    e.date_naissance,
    e.sexe,
    a.libelle AS annee_scolaire,
    a.date_debut AS annee_debut,
    a.date_fin AS annee_fin,
    c.nom AS classe,
    n.libelle AS niveau,
    n.ordre AS niveau_ordre,
    s.libelle AS serie,
    pe.resultat,
    pe.mention,
    pe.moyenne_annuelle,
    pe.rang_classe,
    pe.effectif_classe,
    c2.nom AS classe_suivante,
    n2.libelle AS niveau_suivant,
    pe.motif_reorientation,
    pe.conseil_orientation,
    (SELECT COUNT(*) FROM passages_conditionnels pc WHERE pc.parcours_id = pe.id AND pc.statut = 'en_cours') AS nb_conditions_en_cours,
    (SELECT COUNT(*) FROM passages_conditionnels pc WHERE pc.parcours_id = pe.id AND pc.statut = 'non_validee') AS nb_conditions_echouees,
    pe.date_debut,
    pe.date_fin,
    pe.date_decision_conseil,
    CONCAT(pp.nom, ' ', pp.prenom) AS professeur_principal,
    pe.created_at,
    pe.updated_at
FROM parcours_eleves pe
JOIN eleves e ON pe.eleve_id = e.id
JOIN classes c ON pe.classe_id = c.id
JOIN niveaux n ON c.niveau_id = n.id
LEFT JOIN series s ON c.serie_id = s.id
JOIN annees_scolaires a ON pe.annee_scolaire_id = a.id
LEFT JOIN classes c2 ON pe.classe_suivante_id = c2.id
LEFT JOIN niveaux n2 ON c2.niveau_id = n2.id
LEFT JOIN personnels pp ON c.professeur_principal_id = pp.id;

-- View: vue_performance_enseignants
CREATE OR REPLACE VIEW `vue_performance_enseignants` AS
SELECT 
    ec.personnel_id AS enseignant_id,
    p.matricule,
    CONCAT(p.nom, ' ', p.prenom) AS enseignant,
    COUNT(DISTINCT ec.classe_id) AS nb_classes,
    COUNT(DISTINCT ec.matiere_id) AS nb_matieres,
    COUNT(DISTINCT et.id) AS nb_creneaux_semaine,
    COUNT(DISTINCT int.id) AS nb_interrogations,
    COUNT(DISTINCT ex.id) AS nb_examens,
    AVG(ni.note) AS moyenne_notes_interrogations,
    AVG(ne.note) AS moyenne_notes_examens
FROM enseignants_classes ec
JOIN personnels p ON ec.personnel_id = p.id
LEFT JOIN emplois_temps et ON et.personnel_id = ec.personnel_id
LEFT JOIN interrogations int ON int.personnel_id = ec.personnel_id
LEFT JOIN examens_finaux ex ON ex.personnel_id = ec.personnel_id
LEFT JOIN notes_interrogations ni ON ni.interrogation_id = int.id
LEFT JOIN notes_examens ne ON ne.examen_id = ex.id
GROUP BY ec.personnel_id, p.matricule, enseignant;

-- View: vue_performance_enseignants_v2
CREATE OR REPLACE VIEW `vue_performance_enseignants_v2` AS
SELECT 
    p.id AS personnel_id,
    p.matricule,
    CONCAT(p.nom, ' ', p.prenom) AS enseignant,
    pe.specialite,
    pe.grade,
    COUNT(DISTINCT ec.classe_id) AS nb_classes,
    COUNT(DISTINCT ec.matiere_id) AS nb_matieres,
    COUNT(DISTINCT et.id) AS nb_creneaux_semaine,
    COUNT(DISTINCT int.id) AS nb_interrogations,
    COUNT(DISTINCT ex.id) AS nb_examens,
    AVG(ni.note) AS moyenne_notes_interrogations,
    AVG(ne.note) AS moyenne_notes_examens
FROM personnels p
JOIN personnels_enseignants pe ON p.id = pe.personnel_id
LEFT JOIN enseignants_classes ec ON ec.personnel_id = p.id
LEFT JOIN emplois_temps et ON et.personnel_id = p.id
LEFT JOIN interrogations int ON int.personnel_id = p.id
LEFT JOIN examens_finaux ex ON ex.personnel_id = p.id
LEFT JOIN notes_interrogations ni ON ni.interrogation_id = int.id
LEFT JOIN notes_examens ne ON ne.examen_id = ex.id
WHERE p.type_personnel = 'enseignant'
GROUP BY p.id, p.matricule, enseignant, pe.specialite, pe.grade;

-- View: vue_personnels_actifs
CREATE OR REPLACE VIEW `vue_personnels_actifs` AS
SELECT 
    p.id,
    p.matricule,
    p.nom,
    p.prenom,
    p.type_personnel,
    p.sexe,
    p.email,
    p.telephone,
    p.statut,
    p.date_embauche,
    CASE 
        WHEN p.type_personnel = 'enseignant' THEN pe.specialite
        WHEN p.type_personnel = 'administratif' THEN pa.departement
        ELSE NULL
    END AS fonction,
    pa.departement,
    p.created_at
FROM personnels p
LEFT JOIN personnels_enseignants pe ON p.id = pe.personnel_id
LEFT JOIN personnels_administratifs pa ON p.id = pa.personnel_id
WHERE p.statut = 'actif';

-- View: vue_stats_classes_detaillees
CREATE OR REPLACE VIEW `vue_stats_classes_detaillees` AS
SELECT 
    c.id AS classe_id,
    c.nom AS classe,
    c.code,
    n.libelle AS niveau,
    s.libelle AS serie,
    a.libelle AS annee_scolaire,
    CONCAT(pp.nom, ' ', pp.prenom) AS professeur_principal,
    pp.email AS email_professeur,
    c.capacite,
    COUNT(DISTINCT i.eleve_id) AS effectif_actuel,
    (c.capacite - COUNT(DISTINCT i.eleve_id)) AS places_disponibles,
    ROUND((COUNT(DISTINCT i.eleve_id) / NULLIF(c.capacite, 0)) * 100, 2) AS taux_remplissage,
    COUNT(DISTINCT CASE WHEN e.sexe = 'M' THEN e.id END) AS nb_garcons,
    COUNT(DISTINCT CASE WHEN e.sexe = 'F' THEN e.id END) AS nb_filles,
    COUNT(DISTINCT mc.matiere_id) AS nb_matieres,
    COUNT(DISTINCT et.id) AS nb_creneaux_semaine,
    SUM(TIMESTAMPDIFF(MINUTE, CONCAT('2000-01-01 ', et.heure_debut), CONCAT('2000-01-01 ', et.heure_fin))) AS total_minutes_semaine,
    SUM(f.montant_restant) AS impaye_total_classe,
    c.statut,
    c.created_at
FROM classes c
JOIN niveaux n ON c.niveau_id = n.id
LEFT JOIN series s ON c.serie_id = s.id
JOIN annees_scolaires a ON c.annee_scolaire_id = a.id
LEFT JOIN personnels pp ON c.professeur_principal_id = pp.id
LEFT JOIN inscriptions i ON c.id = i.classe_id AND c.annee_scolaire_id = i.annee_scolaire_id
LEFT JOIN eleves e ON i.eleve_id = e.id
LEFT JOIN matieres_classes mc ON mc.classe_id = c.id AND mc.annee_scolaire_id = c.annee_scolaire_id
LEFT JOIN emplois_temps et ON et.classe_id = c.id AND et.annee_scolaire_id = c.annee_scolaire_id
LEFT JOIN echeanciers_ecolages ee ON ee.eleve_id = i.eleve_id AND ee.annee_scolaire_id = i.annee_scolaire_id
LEFT JOIN factures f ON f.eleve_id = i.eleve_id AND f.annee_scolaire_id = i.annee_scolaire_id
GROUP BY c.id, c.nom, c.code, n.libelle, s.libelle, a.libelle, professeur_principal, pp.email, c.capacite, c.statut, c.created_at;

-- View: vue_stats_discipline_annee
CREATE OR REPLACE VIEW `vue_stats_discipline_annee` AS
SELECT 
    s.eleve_id,
    e.matricule,
    CONCAT(e.nom, ' ', e.prenom) AS eleve,
    s.annee_scolaire_id,
    a.libelle AS annee_scolaire,
    c.nom AS classe,
    COUNT(s.id) AS nb_sanctions,
    SUM(ts.gravite) AS score_gravite,
    MAX(s.date_sanction) AS derniere_sanction,
    GROUP_CONCAT(ts.libelle SEPARATOR ', ') AS types_sanctions
FROM sanctions s
JOIN eleves e ON s.eleve_id = e.id
JOIN classes c ON s.classe_id = c.id
JOIN annees_scolaires a ON s.annee_scolaire_id = a.id
JOIN types_sanctions ts ON s.type_sanction_id = ts.id
GROUP BY s.eleve_id, e.matricule, eleve, s.annee_scolaire_id, a.libelle, c.nom;

-- View: vue_stats_emploi_temps
CREATE OR REPLACE VIEW `vue_stats_emploi_temps` AS
SELECT 
    c.id AS classe_id,
    c.nom AS classe,
    COUNT(et.id) AS nb_creneaux,
    SUM(TIMESTAMPDIFF(MINUTE, CONCAT('2000-01-01 ', et.heure_debut), CONCAT('2000-01-01 ', et.heure_fin))) / 60.0 AS total_heures_semaine,
    COUNT(DISTINCT et.matiere_id) AS nb_matieres,
    COUNT(DISTINCT et.personnel_id) AS nb_enseignants
FROM classes c
LEFT JOIN emplois_temps et ON et.classe_id = c.id AND et.actif = 1
GROUP BY c.id, c.nom;

-- View: vue_stats_notes_eleve
CREATE OR REPLACE VIEW `vue_stats_notes_eleve` AS
SELECT 
    e.id AS eleve_id,
    e.matricule,
    CONCAT(e.nom, ' ', e.prenom) AS eleve,
    c.nom AS classe,
    p.nom AS periode,
    p.id AS periode_id,
    COUNT(DISTINCT ni.interrogation_id) AS nb_interrogations,
    COUNT(DISTINCT ne.examen_id) AS nb_examens,
    AVG(ni.note) AS moyenne_interrogations,
    AVG(ne.note) AS moyenne_examens,
    SUM(CASE WHEN ni.absent = 1 THEN 1 ELSE 0 END) AS nb_absences_interro,
    SUM(CASE WHEN ne.absent = 1 THEN 1 ELSE 0 END) AS nb_absences_examen
FROM eleves e
JOIN inscriptions i ON e.id = i.eleve_id
JOIN classes c ON i.classe_id = c.id
JOIN periodes p ON p.annee_scolaire_id = i.annee_scolaire_id
LEFT JOIN notes_interrogations ni ON ni.eleve_id = e.id
LEFT JOIN interrogations int ON ni.interrogation_id = int.id AND int.classe_id = c.id AND int.periode_id = p.id
LEFT JOIN notes_examens ne ON ne.eleve_id = e.id
LEFT JOIN examens_finaux ex ON ne.examen_id = ex.id AND ex.classe_id = c.id AND ex.periode_id = p.id
GROUP BY e.id, e.matricule, eleve, c.nom, p.nom, p.id;

-- View: vue_stats_personnel_departement
CREATE OR REPLACE VIEW `vue_stats_personnel_departement` AS
SELECT 
    pa.departement,
    COUNT(DISTINCT pa.personnel_id) AS nb_personnels,
    SUM(CASE WHEN p.statut = 'actif' THEN 1 ELSE 0 END) AS nb_actifs,
    SUM(CASE WHEN p.sexe = 'M' THEN 1 ELSE 0 END) AS nb_hommes,
    SUM(CASE WHEN p.sexe = 'F' THEN 1 ELSE 0 END) AS nb_femmes
FROM personnels_administratifs pa
JOIN personnels p ON pa.personnel_id = p.id
GROUP BY pa.departement;

-- View: vue_suivi_ecolage_temps_reel
CREATE OR REPLACE VIEW `vue_suivi_ecolage_temps_reel` AS
SELECT 
    ee.eleve_id,
    e.matricule,
    CONCAT(e.nom, ' ', e.prenom) AS eleve,
    c.nom AS classe,
    ee.mois,
    ee.mois_libelle,
    ee.date_limite_normale,
    ee.date_limite_grace,
    ee.date_exclusion,
    ee.montant_du,
    ee.montant_paye,
    ee.montant_restant,
    ee.statut,
    CASE 
        WHEN ee.statut = 'paye' THEN 'paye'
        WHEN ee.statut = 'impaye_exclu' THEN 'exclu'
        WHEN CURDATE() > ee.date_exclusion THEN 'exclu'
        WHEN CURDATE() > ee.date_limite_grace THEN 'retard'
        WHEN CURDATE() > ee.date_limite_normale THEN 'retard'
        ELSE 'normal'
    END AS statut_calcule,
    CASE 
        WHEN ee.statut = 'impaye_exclu' OR CURDATE() > ee.date_exclusion THEN 0
        ELSE 1
    END AS peut_suivre_cours,
    p.telephone AS tel_parent
FROM echeanciers_ecolages ee
JOIN eleves e ON ee.eleve_id = e.id
JOIN inscriptions i ON ee.eleve_id = i.eleve_id AND ee.annee_scolaire_id = i.annee_scolaire_id
JOIN classes c ON i.classe_id = c.id
LEFT JOIN eleves_parents ep ON e.id = ep.eleve_id
LEFT JOIN parents p ON ep.parent_id = p.id;

-- View: vue_synthese_financiere_annee
CREATE OR REPLACE VIEW `vue_synthese_financiere_annee` AS
SELECT 
    e.id AS eleve_id,
    e.matricule,
    e.nom,
    e.prenom,
    i.annee_scolaire_id,
    a.libelle AS annee_scolaire,
    c.nom AS classe,
    i.id AS inscription_id,
    i.statut_dossier,
    COUNT(DISTINCT f.id) AS nb_factures,
    SUM(f.montant_total) AS total_facture,
    SUM(f.montant_paye) AS total_paye,
    SUM(f.montant_restant) AS total_restant,
    COUNT(DISTINCT ee.id) AS nb_echeances,
    SUM(CASE WHEN ee.statut = 'paye' THEN 1 ELSE 0 END) AS nb_echeances_payees,
    SUM(ee.montant_restant) AS total_ecolage_restant,
    CASE 
        WHEN SUM(f.montant_restant) = 0 THEN 'solde'
        WHEN SUM(f.montant_restant) < SUM(f.montant_total) THEN 'partiel'
        ELSE 'impaye'
    END AS statut_paiement
FROM eleves e
JOIN inscriptions i ON e.id = i.eleve_id
JOIN classes c ON i.classe_id = c.id
JOIN annees_scolaires a ON i.annee_scolaire_id = a.id
LEFT JOIN factures f ON f.eleve_id = e.id AND f.annee_scolaire_id = i.annee_scolaire_id
LEFT JOIN echeanciers_ecolages ee ON ee.eleve_id = e.id AND ee.annee_scolaire_id = i.annee_scolaire_id
GROUP BY e.id, e.matricule, e.nom, e.prenom, i.annee_scolaire_id, a.libelle, c.nom, i.id, i.statut_dossier;

SET FOREIGN_KEY_CHECKS=1;

-- Rôles d'un utilisateur via ses groupes
SELECT DISTINCT 
    r.id,
    r.code,
    r.nom,
    r.niveau,
    ug.nom as nom_groupe
FROM roles r
INNER JOIN user_group_roles ugr ON r.id = ugr.role_id
INNER JOIN user_group_members ugm ON ugr.group_id = ugm.group_id
WHERE ugm.user_id = :user_id
ORDER BY r.niveau DESC;