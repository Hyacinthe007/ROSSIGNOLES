# 🏫 ROSSIGNOLES V2 — Proposition d'Optimisation

> **Date** : 15 février 2026  
> **Contexte** : Système de gestion scolaire ERP — Madagascar  
> **Stack actuelle** : PHP 8.2 + MySQL/MariaDB + Framework MVC custom  

--- 

## 📋 TABLE DES MATIÈRES

1. [Analyse du Système Actuel](#1-analyse-du-système-actuel)
2. [Problèmes Identifiés](#2-problèmes-identifiés)
3. [Schéma de BDD Optimisé](#3-schéma-de-bdd-optimisé)
4. [Architecture Applicative V2](#4-architecture-applicative-v2)
5. [Plan de Migration](#5-plan-de-migration)

---

## 1. ANALYSE DU SYSTÈME ACTUEL

### 1.1 Modules Fonctionnels Existants (15 modules)

| Module | Tables | Contrôleurs | Description |
|--------|--------|-------------|-------------|
| **Académique** | `cycles`, `niveaux`, `series`, `classes`, `matieres` | PedagogieController | Structure pédagogique |
| **Élèves** | `eleves`, `parents`, `eleves_parents` | ElevesController, ParentsController | Gestion des élèves et familles |
| **Inscriptions** | `inscriptions`, `inscriptions_historique`, `documents_inscription`, `exigences_documents_inscription` | InscriptionsController | Workflow complet d'inscription |
| **Personnel** | `personnels`, `personnels_enseignants`, `personnels_administratifs`, `postes_administratifs` | PersonnelController | RH unifiée |
| **Évaluations** | `interrogations`, `notes_interrogations`, `examens_finaux`, `notes_examens` | NotesController, InterrogationsController, ExamensController | Notes et évaluations |
| **Bulletins** | `bulletins`, `bulletins_notes` | BulletinsController | Génération des bulletins |
| **Présences** | `absences`, `absences_personnels` | AbsencesController, PresencesController | Absences et retards |
| **Finance** | `factures`, `lignes_facture`, `paiements`, `echeanciers_ecolages`, `ecolages_payes` | FinanceController, PaiementMensuelController, EcheancierController | Facturation et paiements |
| **Discipline** | `sanctions`, `types_sanctions` | SanctionsController | Sanctions disciplinaires |
| **Emploi du temps** | `emplois_temps` | PedagogieController | Planification horaire |
| **Conseils de classe** | `conseils_classe`, `decisions_conseil` | ConseilsController | Conseils et décisions |
| **Parcours** | `parcours_eleves`, `passages_conditionnels` | ParcoursController | Historique scolaire |
| **Paie** | `fiches_paie`, `salaires_personnels` | PaieController | Gestion de la paie |
| **Communication** | `annonces`, `messages`, `notifications`, `alertes_parents` | NotificationsController, AnnoncesController | Messagerie et alertes |
| **Système** | `users`, `roles`, `permissions`, `sessions`, `logs_activites`, `parametres_ecole` | SystemeController, RolesController | Administration |

### 1.2 Points Forts Actuels
- ✅ Schéma relationnel riche avec FK bien définies
- ✅ Soft delete sur `classes` et `personnels`
- ✅ Vues SQL pré-calculées (20+ vues)
- ✅ Système RBAC (users → roles → permissions)
- ✅ Workflow d'inscription complet avec statuts
- ✅ Échéancier d'écolage avec dates de grâce et exclusion
- ✅ Architecture MVC propre (Controllers/Models/Views/Services)

---

## 2. PROBLÈMES IDENTIFIÉS

### 2.1 Base de Données

| # | Problème | Impact | Tables Concernées |
|---|----------|--------|-------------------|
| **P1** | **Duplication calendrier/jours_feries** — Deux tables pour le même concept | Incohérence des données | `calendrier_scolaire`, `jours_feries` |
| **P2** | **`matieres_series` vs `matieres_classes`** — Double gestion des coefficients | Conflits de coefficients | `matieres_series`, `matieres_classes` |
| **P3** | **`annee_scolaire_id` omniprésent** — Redondance dans les tables enfants (la classe contient déjà l'année) | Risque de désynchronisation | `absences`, `sanctions`, `enseignants_classes`, etc. |
| **P4** | **Pas de table `inscriptions_articles`** — Les articles scolaires ne sont pas liés aux inscriptions | Perte de traçabilité | `articles`, `commandes_articles` |
| **P5** | **`effectif_actuel` dénormalisé** — Stocké dans `classes` mais doit rester synchronisé manuellement | Données obsolètes | `classes` |
| **P6** | **`users_roles` dupliquée** — Définie 2 fois dans le schéma (lignes 200 et 1668) | Erreur SQL | `users_roles` |
| **P7** | **Pas d'index composite sur les recherches fréquentes** — Recherche élèves par nom+prénom non indexée | Performances dégradées | `eleves`, `personnels` |
| **P8** | **`matieres_enseignees` en TEXT** dans `personnels_enseignants` — Données non structurées | Non requêtable | `personnels_enseignants` |
| **P9** | **Pas d'audit trail unifié** — `saisi_par`, `modifie_par`, `valide_par` dispersés sans standard | Traçabilité faible | Multiple tables |
| **P10** | **Enum `statut_dossier` inscription trop complexe** — 8 statuts dans un seul enum | Maintenance difficile | `inscriptions` |

### 2.2 Architecture Applicative

| # | Problème | Impact |
|---|----------|--------|
| **A1** | **Routes dupliquées** — `presences/list` et `absences/list` pointent vers le même contrôleur | Confusion dans le code |
| **A2** | **Pas de Service Layer systématique** — Logique métier dans les contrôleurs (30KB+ pour FinanceController) | Difficile à tester |
| **A3** | **Pas de Repository Pattern** — Les modèles font du CRUD et des requêtes complexes | Couplage fort |
| **A4** | **Pas de système de cache** — Chaque vue SQL est recalculée à chaque requête | Performances |
| **A5** | **Pas de système de queues** — SMS et emails envoyés synchroniquement | Latence des requêtes |
| **A6** | **Pas de Middleware structuré** — Seul CSRF est géré, pas de rate limiting, logging, etc. |Sécurité |

---

## 3. SCHÉMA DE BDD OPTIMISÉ

### 3.1 Principes d'Optimisation
1. **Éliminer les redondances** — Fusionner les tables dupliquées
2. **Normaliser les statuts** — Utiliser des tables de référence au lieu d'enums
3. **Standardiser l'audit** — Trait `Auditable` sur toutes les tables critiques
4. **Optimiser les index** — Index composites sur les recherches fréquentes
5. **Conserver la logique métier** — Même workflow, même terminologie

### 3.2 Tables Restructurées

#### A. STRUCTURE ACADÉMIQUE (inchangée mais nettoyée)

```sql
-- ✅ INCHANGÉ: cycles, niveaux, series, matieres
-- Les 4 tables restent identiques, structure solide

-- 🔄 AMÉLIORÉ: classes — Retirer effectif_actuel (calculé dynamiquement)
ALTER TABLE classes DROP COLUMN effectif_actuel;
-- Ajouter une colonne pour la section (si plusieurs sections par niveau)
ALTER TABLE classes ADD COLUMN section VARCHAR(10) DEFAULT NULL 
  COMMENT 'A, B, C... pour classes parallèles' AFTER code;

-- 🔄 FUSIONNÉ: matieres_niveaux remplace matieres_series + matieres_classes
-- Hiérarchie: Série → Niveau → Classe (héritage des coefficients avec override)
CREATE TABLE IF NOT EXISTS `coefficients_matieres` (
  `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `matiere_id` BIGINT(20) NOT NULL,
  `annee_scolaire_id` BIGINT(20) NOT NULL,
  -- Cible polymorphique: soit série, soit niveau, soit classe
  `cible_type` ENUM('serie', 'niveau', 'classe') NOT NULL,
  `cible_id` BIGINT(20) NOT NULL,
  `coefficient` DECIMAL(4,2) NOT NULL DEFAULT 1.00,
  `heures_semaine` DECIMAL(4,1) DEFAULT NULL,
  `obligatoire` TINYINT(1) DEFAULT 1,
  `actif` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_matiere_cible_annee` (`matiere_id`, `cible_type`, `cible_id`, `annee_scolaire_id`),
  KEY `idx_cible` (`cible_type`, `cible_id`),
  CONSTRAINT `fk_coeff_matiere` FOREIGN KEY (`matiere_id`) REFERENCES `matieres` (`id`),
  CONSTRAINT `fk_coeff_annee` FOREIGN KEY (`annee_scolaire_id`) REFERENCES `annees_scolaires` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COMMENT='Coefficient unifié — résolution: Classe > Niveau > Série';
```

#### B. CALENDRIER UNIFIÉ (fusion de 2 tables)

```sql
-- 🔄 FUSIONNÉ: calendrier_scolaire + jours_feries → evenements_calendrier
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
  `bloque_cours` TINYINT(1) DEFAULT 1,
  `actif` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_annee_dates` (`annee_scolaire_id`, `date_debut`, `date_fin`),
  KEY `idx_type_actif` (`type`, `actif`),
  CONSTRAINT `fk_evt_annee` FOREIGN KEY (`annee_scolaire_id`) REFERENCES `annees_scolaires` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COMMENT='Calendrier unifié — remplace calendrier_scolaire + jours_feries';
```

#### C. ÉVALUATIONS UNIFIÉES (simplification majeure)

```sql
-- 🔄 FUSIONNÉ: interrogations + examens_finaux → evaluations
CREATE TABLE IF NOT EXISTS `evaluations` (
  `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `type` ENUM('interrogation', 'devoir', 'examen', 'tp', 'oral') NOT NULL,
  `classe_id` BIGINT(20) NOT NULL,
  `matiere_id` BIGINT(20) NOT NULL,
  `personnel_id` BIGINT(20) DEFAULT NULL,
  `periode_id` BIGINT(20) NOT NULL,
  `annee_scolaire_id` BIGINT(20) NOT NULL,
  `nom` VARCHAR(255) NOT NULL,
  `date_evaluation` DATE NOT NULL,
  `heure_debut` TIME DEFAULT NULL,
  `heure_fin` TIME DEFAULT NULL,
  `duree_minutes` INT DEFAULT NULL,
  `note_sur` DECIMAL(5,2) DEFAULT 20.00,
  `poids` DECIMAL(3,2) DEFAULT 1.00 COMMENT 'Poids dans la moyenne (ex: examen=2, interro=1)',
  `description` TEXT DEFAULT NULL,
  `consignes` TEXT DEFAULT NULL,
  `sujet_url` VARCHAR(255) DEFAULT NULL,
  `statut` ENUM('planifiee', 'en_cours', 'terminee', 'annulee') DEFAULT 'planifiee',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_classe_matiere_periode` (`classe_id`, `matiere_id`, `periode_id`),
  KEY `idx_type_statut` (`type`, `statut`),
  KEY `idx_date` (`date_evaluation`),
  KEY `idx_personnel` (`personnel_id`),
  KEY `idx_annee_type` (`annee_scolaire_id`, `type`, `statut`),
  CONSTRAINT `fk_eval_classe` FOREIGN KEY (`classe_id`) REFERENCES `classes` (`id`),
  CONSTRAINT `fk_eval_matiere` FOREIGN KEY (`matiere_id`) REFERENCES `matieres` (`id`),
  CONSTRAINT `fk_eval_personnel` FOREIGN KEY (`personnel_id`) REFERENCES `personnels` (`id`),
  CONSTRAINT `fk_eval_periode` FOREIGN KEY (`periode_id`) REFERENCES `periodes` (`id`),
  CONSTRAINT `fk_eval_annee` FOREIGN KEY (`annee_scolaire_id`) REFERENCES `annees_scolaires` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COMMENT='Table unifiée — remplace interrogations + examens_finaux';

-- 🔄 FUSIONNÉ: notes_interrogations + notes_examens → notes
CREATE TABLE IF NOT EXISTS `notes` (
  `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `evaluation_id` BIGINT(20) NOT NULL,
  `eleve_id` BIGINT(20) NOT NULL,
  `note` DECIMAL(5,2) DEFAULT NULL,
  `absent` TINYINT(1) DEFAULT 0,
  `dispense` TINYINT(1) DEFAULT 0 COMMENT 'Dispensé de cette évaluation',
  `appreciation` TEXT DEFAULT NULL,
  `saisi_par` BIGINT(20) DEFAULT NULL,
  `date_saisie` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `modifie_par` BIGINT(20) DEFAULT NULL,
  `date_modification` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_eleve_eval` (`eleve_id`, `evaluation_id`),
  KEY `idx_evaluation` (`evaluation_id`),
  KEY `idx_note` (`note`),
  CONSTRAINT `fk_note_eval` FOREIGN KEY (`evaluation_id`) REFERENCES `evaluations` (`id`),
  CONSTRAINT `fk_note_eleve` FOREIGN KEY (`eleve_id`) REFERENCES `eleves` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COMMENT='Table unifiée — remplace notes_interrogations + notes_examens';
```

#### D. ÉLÈVES — Index de Recherche

```sql
-- 🆕 Index optimisés pour la recherche
ALTER TABLE eleves ADD FULLTEXT INDEX `ft_eleves_nom_prenom` (`nom`, `prenom`);
ALTER TABLE eleves ADD KEY `idx_nom_prenom` (`nom`, `prenom`);
ALTER TABLE personnels ADD FULLTEXT INDEX `ft_personnel_nom_prenom` (`nom`, `prenom`);
ALTER TABLE personnels ADD KEY `idx_nom_prenom` (`nom`, `prenom`);
```

#### E. AUDIT TRAIL UNIFIÉ

```sql
-- 🆕 Table d'audit centralisée (remplace les colonnes dispersées)
CREATE TABLE IF NOT EXISTS `audit_trail` (
  `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT(20) DEFAULT NULL,
  `action` ENUM('create', 'update', 'delete', 'validate', 'reject', 'export') NOT NULL,
  `table_name` VARCHAR(100) NOT NULL,
  `record_id` BIGINT(20) NOT NULL,
  `old_values` JSON DEFAULT NULL COMMENT 'Anciennes valeurs (pour update/delete)',
  `new_values` JSON DEFAULT NULL COMMENT 'Nouvelles valeurs (pour create/update)',
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `user_agent` VARCHAR(500) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_table_record` (`table_name`, `record_id`),
  KEY `idx_user_date` (`user_id`, `created_at`),
  KEY `idx_action_date` (`action`, `created_at`),
  KEY `idx_date` (`created_at`),
  CONSTRAINT `fk_audit_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COMMENT='Audit centralisé — remplace logs_activites + colonnes saisi_par/modifie_par dispersées';
```

#### F. PERSONNELS_ENSEIGNANTS — Nettoyage

```sql
-- 🔄 Retirer matieres_enseignees (TEXT) → utiliser enseignants_classes comme source de vérité
ALTER TABLE personnels_enseignants DROP COLUMN matieres_enseignees;
```

### 3.3 Récapitulatif des Changements BDD

| Action | Avant | Après | Gain |
|--------|-------|-------|------|
| Fusion | `calendrier_scolaire` + `jours_feries` | `evenements_calendrier` | -1 table |
| Fusion | `interrogations` + `examens_finaux` | `evaluations` | -1 table |
| Fusion | `notes_interrogations` + `notes_examens` | `notes` | -1 table |
| Fusion | `matieres_series` + `matieres_classes` | `coefficients_matieres` | -1 table, logique plus claire |
| Ajout | — | `audit_trail` | Traçabilité centralisée |
| Suppression | `effectif_actuel` dans classes | Calculé dynamiquement | Plus de désynchronisation |
| Suppression | `matieres_enseignees` (TEXT) | Via `enseignants_classes` | Données structurées |
| Index | Aucun fulltext | Fulltext sur nom/prénom | Recherche rapide |

**Tables totales** : ~55 → ~52 (+ 1 audit_trail)

---

## 4. ARCHITECTURE APPLICATIVE V2

### 4.1 Structure de Dossiers Proposée

```
ROSSIGNOLES/
├── app/
│   ├── Controllers/          # Inchangé — mais allégés
│   ├── Core/
│   │   ├── Router.php        # Existant
│   │   ├── Cache.php         # 🆕 Cache fichier simple
│   │   ├── Queue.php         # 🆕 File d'attente BDD simple
│   │   └── EventDispatcher.php  # 🆕 Événements métier
│   ├── Events/               # 🆕 
│   │   ├── InscriptionValidee.php
│   │   ├── PaiementRecu.php
│   │   └── NotesSaisies.php
│   ├── Listeners/            # 🆕 
│   │   ├── EnvoyerSmsParent.php
│   │   ├── MettreAJourEcheancier.php
│   │   └── GenererNotification.php
│   ├── Helpers/              # Existant
│   ├── Middleware/            # Existant — enrichi
│   │   ├── CsrfMiddleware.php
│   │   ├── RateLimitMiddleware.php   # 🆕
│   │   └── AuditMiddleware.php       # 🆕
│   ├── Models/               # Existant
│   ├── Repositories/         # 🆕 Requêtes complexes extraites des modèles
│   │   ├── EleveRepository.php
│   │   ├── FinanceRepository.php
│   │   └── BulletinRepository.php
│   ├── Services/             # Existant — complété systématiquement
│   │   ├── InscriptionService.php    # 🆕 Extrait de InscriptionsController
│   │   ├── EvaluationService.php     # 🆕 Logique notes unifiées
│   │   └── ...
│   └── Views/                # Existant
├── config/                   # Existant
├── database/
│   ├── migrations/           # Existant — ajout V2
│   │   └── 2026_02_15_v2_optimisation.sql  # 🆕
│   └── rossignoles_schema_v2.sql           # 🆕
├── routes/                   # Existant — nettoyé
└── ...
```

### 4.2 Améliorations Clés du Code

#### 1. Service Layer Systématique
```
Avant:  Controller (30KB) → Model → BDD
Après:  Controller (5KB) → Service → Repository → Model → BDD
```

#### 2. Cache Fichier Simple (sans Redis)
```php
// Utilisation : données rarement modifiées (cycles, niveaux, matières)
$cycles = Cache::remember('cycles_actifs', 3600, function() {
    return (new Cycle())->all(['actif' => 1]);
});
```

#### 3. Événements Métier
```php
// Quand un paiement est reçu, déclencher automatiquement :
// → Mise à jour de l'échéancier
// → Notification au parent  
// → Log d'audit
EventDispatcher::dispatch(new PaiementRecu($paiement));
```

### 4.3 Routes Nettoyées

```
Suppressions:
- presences/list, presences/saisie  → Utiliser absences/* uniquement
- evaluations/*                      → Redirigé vers un contrôleur unifié

Ajouts:
- evaluations/list (GET)             → EvaluationsController@list (unifié)
- evaluations/add (GET/POST)         → EvaluationsController@add (unifié)
- evaluations/notes/{id} (GET/POST)  → EvaluationsController@notes (unifié)
```

---

## 5. PLAN DE MIGRATION

### Phase 1 — Fondations (Semaine 1-2)
- [ ] Créer `audit_trail` et `AuditMiddleware`
- [ ] Fusionner `calendrier_scolaire` + `jours_feries` → `evenements_calendrier` avec migration des données
- [ ] Ajouter index fulltext sur `eleves` et `personnels`
- [ ] Créer le `Cache.php` simple (fichier)

### Phase 2 — Évaluations Unifiées (Semaine 3-4)
- [ ] Créer `evaluations` et `notes`
- [ ] Migrer les données de `interrogations` → `evaluations` (type='interrogation')
- [ ] Migrer les données de `examens_finaux` → `evaluations` (type='examen')
- [ ] Migrer `notes_interrogations` + `notes_examens` → `notes`
- [ ] Créer `EvaluationService` et `EvaluationsController` unifié
- [ ] Mettre à jour `BulletinService` pour utiliser la nouvelle table `notes`

### Phase 3 — Coefficients Unifiés (Semaine 5)
- [ ] Créer `coefficients_matieres`
- [ ] Migrer `matieres_series` et `matieres_classes` → `coefficients_matieres`
- [ ] Implémenter la logique d'héritage (Classe > Niveau > Série)

### Phase 4 — Refactoring Services (Semaine 6-8)
- [ ] Extraire la logique de `FinanceController` (37KB) → `FinanceService` + `FinanceRepository`
- [ ] Extraire `InscriptionsController` (30KB) → `InscriptionService`
- [ ] Extraire `ElevesController` (24KB) → Repositories
- [ ] Implémenter l'`EventDispatcher` et les Listeners

### Phase 5 — Nettoyage (Semaine 9-10)
- [ ] Supprimer les anciennes tables (avec backup)
- [ ] Nettoyer les routes dupliquées
- [ ] Mettre à jour toutes les vues SQL
- [ ] Tests de non-régression complets

---

## RÉSUMÉ DES GAINS ATTENDUS

| Métrique | Avant | Après | Amélioration |
|----------|-------|-------|-------------|
| Tables | ~55 | ~52 | -5% complexité |
| Taille contrôleurs max | 37KB | ~8KB | -78% |
| Requêtes dupliquées | Fréquentes | Via Repository | Centralisées |
| Traçabilité | Partielle (colonnes dispersées) | Complète (audit_trail) | 100% couverture |
| Recherche élèves | LIKE '%..%' | FULLTEXT | ~10x plus rapide |
| Cohérence coefficients | 2 sources (série+classe) | 1 source hiérarchique | Pas de conflit |
| Évaluations | 4 tables | 2 tables | Code simplifié |
| Calendrier | 2 tables | 1 table | Plus de doublons |

> **⚠ Important** : Toutes les modifications préservent la logique métier existante 
> (workflow inscription, échéancier écolage, bulletins, paie). 
> Le changement est structurel, pas fonctionnel.
