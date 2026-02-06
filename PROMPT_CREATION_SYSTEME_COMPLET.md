# 🎓 PROMPT COMPLET : Système de Gestion Scolaire et RH "ROSSIGNOLES"

## 📋 CONTEXTE GÉNÉRAL

Tu es un expert Full-Stack PHP spécialisé dans les systèmes de gestion d'établissements scolaires. Tu vas créer un **ERP éducatif complet** nommé **ROSSIGNOLES**, un système de gestion intégré pour établissements scolaires incluant la gestion administrative, pédagogique, financière et RH.

---

## 🏗️ ARCHITECTURE TECHNIQUE

### Stack Technologique
- **Backend** : PHP 8.x (POO strict, architecture MVC personnalisée)
- **Base de données** : MySQL/MariaDB
- **Frontend** : HTML5, CSS3 (Flexbox/Grid), JavaScript Vanilla (Fetch API pour AJAX)
- **Bibliothèques** : 
  - DomPDF ou TCPDF pour génération PDF
  - PHPSpreadsheet pour export Excel
  - Font Awesome pour les icônes
- **Serveur** : Apache avec mod_rewrite activé

### Structure MVC Personnalisée
```
ROSSIGNOLES/
├── app/
│   ├── Controllers/     # Contrôleurs (37 fichiers)
│   ├── Models/          # Modèles (86 fichiers)
│   ├── Services/        # Logique métier (PaieService, FinanceService)
│   └── Views/           # Vues organisées par module (37 dossiers)
├── config/              # Configuration (database, app, mail, sms)
├── database/
│   ├── migrations/      # Scripts SQL de migration
│   └── init_*.php       # Scripts d'initialisation
├── public/
│   ├── css/            # Styles globaux et par module
│   ├── js/             # Scripts JavaScript
│   ├── images/         # Assets visuels
│   └── uploads/        # Documents uploadés
├── routes/
│   └── web.php         # Définition des routes (425 routes)
├── storage/            # Logs et fichiers temporaires
├── vendor/             # Dépendances Composer
├── .htaccess           # Configuration Apache
├── index.php           # Point d'entrée unique
└── composer.json       # Dépendances PHP
```

### Principes de Conception
1. **Séparation des responsabilités** : MVC strict
2. **Sécurité** : 
   - Protection CSRF sur tous les formulaires POST
   - Validation côté serveur et client
   - Gestion de sessions avec timeout (15 minutes d'inactivité)
   - Hachage bcrypt pour les mots de passe
3. **Performance** : 
   - Requêtes SQL optimisées avec JOIN
   - Pagination sur toutes les listes
   - Lazy loading des images
4. **Maintenabilité** : 
   - Code commenté en français
   - Nommage cohérent (camelCase pour méthodes, snake_case pour BDD)
   - Logs d'activité pour audit

---

## 📊 MODULES FONCTIONNELS (37 MODULES)

### 1️⃣ MODULE AUTHENTIFICATION & SÉCURITÉ
**Fonctionnalités** :
- Connexion/Déconnexion avec gestion de session
- Réinitialisation de mot de passe par email
- Gestion des rôles et permissions (RBAC)
- Groupes d'utilisateurs (Administrateur, Enseignant, Parent, etc.)
- Logs d'activité traçables

**Tables BDD** :
- `users` (id, username, email, password_hash, groupe_id, actif, created_at)
- `user_groups` (id, nom, description)
- `roles` (id, nom, description)
- `permissions` (id, code, module, action, description)
- `roles_permissions` (role_id, permission_id)
- `users_roles` (user_id, role_id)
- `logs_activites` (id, user_id, action, module, details, ip, created_at)

**Routes** :
- `GET/POST /auth/login` - Connexion
- `GET /auth/logout` - Déconnexion
- `GET/POST /auth/password-reset` - Réinitialisation

---

### 2️⃣ MODULE DASHBOARD
**Fonctionnalités** :
- Tableau de bord personnalisé par rôle
- Statistiques en temps réel (élèves, personnel, finances)
- Graphiques interactifs (Chart.js)
- Notifications et alertes

**Widgets** :
- Nombre total d'élèves par niveau
- Taux de présence du jour
- Paiements en attente
- Prochains événements du calendrier

---

### 3️⃣ MODULE ÉLÈVES (Gestion Complète)
**Fonctionnalités** :
- Liste filtrable et paginée des élèves
- Fiche détaillée de l'élève (état civil, parcours, documents)
- Ajout/Modification/Suppression d'élèves
- Historique du parcours scolaire
- Génération de certificats de scolarité (PDF)
- Export Excel/PDF de la liste

**Tables BDD** :
- `eleves` (id, matricule, nom, prenom, date_naissance, lieu_naissance, sexe, adresse, photo, created_at)
- `eleves_parents` (eleve_id, parent_id, lien_parente)
- `parcours_eleves` (id, eleve_id, annee_scolaire_id, classe_id, statut, decision_conseil)

**Routes** :
- `GET /eleves/list` - Liste des élèves
- `GET/POST /eleves/add` - Ajouter un élève
- `GET/POST /eleves/edit/{id}` - Modifier
- `GET /eleves/details/{id}` - Détails
- `GET /eleves/parcours/{id}` - Parcours scolaire
- `GET /eleves/certificat/{id}` - Certificat PDF
- `GET /eleves/export-pdf` - Export PDF
- `GET /eleves/export-excel` - Export Excel

---

### 4️⃣ MODULE PARENTS
**Fonctionnalités** :
- Gestion des parents/tuteurs
- Association parent-enfant (plusieurs enfants possibles)
- Compte utilisateur pour accès parent
- Historique des paiements par parent

**Tables BDD** :
- `parents` (id, nom, prenom, telephone, email, profession, adresse, user_id)

**Routes** :
- `GET /parents/list` - Liste des parents
- `GET/POST /parents/add` - Ajouter
- `GET/POST /parents/edit/{id}` - Modifier
- `GET /parents/details/{id}` - Détails avec liste des enfants

---

### 5️⃣ MODULE INSCRIPTIONS (Workflow Multi-Étapes)
**Fonctionnalités** :
- **Processus en 6 étapes** :
  1. Sélection du parent (existant ou nouveau)
  2. Informations de l'élève
  3. Choix de la classe et tarifs
  4. Upload des documents justificatifs
  5. Récapitulatif
  6. **Paiement et validation** (inscription créée UNIQUEMENT après paiement)
- Gestion des réinscriptions
- Génération automatique du matricule
- Reçu de paiement d'inscription (PDF)

**Tables BDD** :
- `inscriptions` (id, annee_scolaire_id, eleve_id, classe_id, montant_inscription, montant_ecolage, statut, created_at)
- `inscriptions_historique` (id, inscription_id, action, details, user_id, created_at)
- `documents_inscriptions` (id, inscription_id, type_document_id, fichier, uploaded_at)
- `exigences_documents_inscriptions` (id, niveau_id, type_document_id, obligatoire)

**Routes** :
- `GET /inscriptions/liste` - Liste des inscriptions
- `GET/POST /inscriptions/nouveau` - Nouvelle inscription (multi-étapes)
- `GET /inscriptions/details/{id}` - Détails
- `GET/POST /inscriptions/documents/{id}` - Gestion documents
- `GET /inscriptions/recu/{id}` - Reçu PDF

**Règle Critique** : L'inscription n'est enregistrée en BDD qu'à l'étape 6 après confirmation du paiement.

---

### 6️⃣ MODULE CLASSES & PÉDAGOGIE
**Fonctionnalités** :
- Gestion des cycles (Primaire, Collège, Lycée)
- Gestion des niveaux (6ème, 5ème, etc.)
- Gestion des séries (S, L, ES, etc.)
- Création de classes (6èmeA, TerminaleS1, etc.)
- Association élèves-classes par année scolaire
- Gestion des coefficients par matière/série/niveau
- Liste des élèves par classe

**Tables BDD** :
- `cycles` (id, nom, ordre)
- `niveaux` (id, cycle_id, nom, code, ordre)
- `series` (id, nom, code, niveau_id, actif)
- `classes` (id, niveau_id, serie_id, code_classe, nom_classe, annee_scolaire_id, capacite)
- `matieres` (id, nom, code, couleur)
- `matieres_series` (id, matiere_id, serie_id, coefficient, obligatoire)
- `matieres_niveaux` (id, matiere_id, niveau_id, coefficient)
- `matieres_classes` (id, matiere_id, classe_id, coefficient)

**Routes** :
- `GET /classes/list` - Liste des classes
- `GET/POST /classes/add` - Créer une classe
- `GET/POST /classes/associer` - Associer élèves à classes
- `GET /classes/eleves` - Liste élèves par classe
- `GET /pedagogie/niveaux` - Gestion niveaux
- `GET /pedagogie/series` - Gestion séries
- `GET /pedagogie/series/coefficients/{id}` - Gestion coefficients

---

### 7️⃣ MODULE MATIÈRES & ENSEIGNEMENTS
**Fonctionnalités** :
- Création de matières (Mathématiques, Français, etc.)
- Association matières-enseignants
- Gestion des enseignements (matière + classe + enseignant)
- Emplois du temps par classe

**Tables BDD** :
- `enseignements` (id, matiere_id, classe_id, enseignant_id, annee_scolaire_id)
- `emplois_temps` (id, enseignement_id, jour_semaine, heure_debut, heure_fin, salle)

**Routes** :
- `GET /matieres/list` - Liste matières
- `GET/POST /matieres/add` - Ajouter matière
- `GET /pedagogie/enseignements` - Liste enseignements
- `GET/POST /pedagogie/emplois-temps/add` - Ajouter créneau

---

### 8️⃣ MODULE ASSIDUITÉ (Absences & Retards) ⭐ UNIFIÉ
**Fonctionnalités** :
- **Interface unifiée avec onglets** : "Absences" et "Retards"
- Saisie rapide par classe ou par élève
- Recherche d'élèves avec autocomplétion
- Affichage détaillé : Code Classe, Période (ex: 07:30 - 09:00), Matière, Professeur
- **Toggle Switch AJAX** pour modifier le statut Justifié/Non justifié sans rechargement
- Bouton "Retour à la liste" stylisé
- Statistiques d'assiduité par élève
- Export PDF des absences

**Tables BDD** :
- `absences` (id, eleve_id, classe_id, date_absence, type, periode, heure_debut, heure_fin, matiere_id, enseignant_id, justifiee, motif, created_by, created_at)

**Routes** :
- `GET /absences/list` - Liste (avec paramètre ?type=absence ou ?type=retard)
- `GET/POST /absences/add` - Ajouter
- `POST /absences/toggle-justifiee` - Toggle AJAX du statut
- `GET /absences/search-eleves` - Autocomplétion
- `GET /absences/get-emplois-temps` - Récupérer emploi du temps

**Design** :
- Onglets Bootstrap ou CSS personnalisés
- Bouton dynamique : "Ajouter une absence" ou "Ajouter un retard" selon l'onglet actif
- Toggle switch avec animation CSS

---

### 9️⃣ MODULE ÉVALUATIONS (Notes, Bulletins)
**Fonctionnalités** :
- Gestion des périodes (Trimestre 1, 2, 3 ou Semestre 1, 2)
- Création d'interrogations et examens
- **Saisie de notes en masse** (interface optimisée)
- Import Excel de notes
- Calcul automatique des moyennes pondérées
- Génération de bulletins trimestriels/semestriels (PDF)
- Validation des bulletins (verrouillage)
- Statistiques par classe (moyenne générale, taux de réussite)

**Tables BDD** :
- `periodes` (id, annee_scolaire_id, nom, type, date_debut, date_fin, ordre)
- `interrogations` (id, enseignement_id, periode_id, date_interrogation, note_sur, coefficient)
- `examens_finaux` (id, matiere_id, classe_id, periode_id, date_examen, note_sur, coefficient)
- `notes_interrogations` (id, interrogation_id, eleve_id, note, appreciation)
- `notes_examens` (id, examen_id, eleve_id, note, appreciation)
- `bulletins` (id, eleve_id, classe_id, periode_id, moyenne_generale, rang, appreciation_generale, decision, valide, created_at)
- `bulletins_notes` (bulletin_id, matiere_id, moyenne, appreciation)

**Routes** :
- `GET /notes/saisie-masse` - Interface de saisie optimisée
- `POST /notes/saisie-masse/save` - Sauvegarde AJAX
- `POST /notes/saisie-masse/import` - Import Excel
- `GET/POST /bulletins/generer` - Génération bulletins
- `GET /bulletins/pdf/{id}` - Export PDF
- `POST /bulletins/valider-tout` - Validation en masse

---

### 🔟 MODULE FINANCE (Complet) 💰
**Fonctionnalités** :
- **Dashboard financier** : Recettes du jour/mois/année
- **Gestion des tarifs** : Inscription, écolage par niveau/classe
- **Échéancier d'écolage** : 
  - Génération automatique des échéances mensuelles
  - Suivi des paiements et retards
  - Relances SMS automatiques
- **Paiements** :
  - Enregistrement de paiements (inscription, écolage, frais divers)
  - Modes de paiement (Espèces, Chèque, Virement, Mobile Money)
  - Génération de reçus numérotés (PAY-2024-001)
- **Reçus** : Export PDF avec logo établissement
- **Types de frais** : Cantine, Transport, Fournitures, etc.
- **Caisse consolidée** : Journal de caisse

**Tables BDD** :
- `tarifs_inscriptions` (id, niveau_id, annee_scolaire_id, montant, actif)
- `tarifs_ecolages` (id, niveau_id, annee_scolaire_id, montant_mensuel, actif)
- `types_frais` (id, nom, description, montant_defaut)
- `echeancier_ecolages` (id, eleve_id, annee_scolaire_id, mois, montant, date_echeance, statut)
- `paiements` (id, eleve_id, parent_id, type_paiement, montant, mode_paiement, numero_recu, date_paiement, created_by)
- `modes_paiements` (id, nom)

**Routes** :
- `GET /finance/dashboard` - Tableau de bord
- `GET /finance/ecolage` - Liste écolages
- `GET /finance/echeanciers` - Échéanciers
- `GET/POST /echeancier/generer` - Générer échéanciers
- `GET /finance/recus` - Liste des reçus
- `GET /finance/export-recu/{id}` - Reçu PDF
- `GET /finance/types-frais` - Gestion types de frais

**Règle Importante** : Numérotation automatique des reçus (PAY-YYYY-NNN).

---

### 1️⃣1️⃣ MODULE PAIE DU PERSONNEL 💼 (Législation Malgache 2026)
**Fonctionnalités** :
- **Configuration** :
  - Paramétrage des taux de cotisations (CNAPS, OSTIE, FMFP)
  - Gestion des tranches IRSA (6 tranches progressives)
  - Modification des tranches IRSA via interface
- **Contrats de paie** :
  - Création de contrats (CDI, CDD, Stage, Intérim)
  - Salaire brut de base
  - Nombre d'enfants à charge (pour réduction IRSA)
  - Option de soumission aux cotisations
- **Bulletins de paie** :
  - Génération automatique mensuelle
  - Calcul automatique :
    - Base imposable IRSA
    - IRSA progressif avec réduction par enfant (2 000 Ar/enfant)
    - Cotisations salariales (CNAPS 1%, OSTIE 1%)
    - Cotisations patronales (CNAPS 13%, OSTIE 5%, FMFP 1%)
    - Salaire net
    - Coût employeur total
  - Validation des bulletins (verrouillage)
  - Export PDF

**Tables BDD** :
- `paie_parametres_cotisations` (id, nom, taux_salarial, taux_patronal, actif)
- `paie_tranches_irsa` (id, montant_min, montant_max, taux, ordre, annee)
- `paie_contrats` (id, personnel_id, type_contrat, salaire_brut_base, nb_enfants, soumis_cotisations, date_debut, date_fin, actif)
- `paie_bulletins` (id, contrat_id, periode, salaire_brut, cnaps_salarial, ostie_salarial, base_imposable_irsa, irsa_brut, reduction_irsa, irsa_net, salaire_net, cnaps_patronal, ostie_patronal, fmfp_patronal, cout_total_employeur, valide, created_at)
- `paie_retenues_diverses` (id, bulletin_id, libelle, montant)

**Routes** :
- `GET /paie` - Accueil module
- `GET /paie/configuration` - Configuration taux
- `POST /paie/configuration/update` - MAJ cotisations
- `POST /paie/configuration/update-irsa` - MAJ tranches IRSA
- `GET /paie/contrats` - Liste contrats
- `GET/POST /paie/contrats/form` - Formulaire contrat
- `GET /paie/bulletins` - Liste bulletins
- `POST /paie/bulletins/generer` - Génération mensuelle
- `GET /paie/bulletins/detail` - Détail bulletin
- `POST /paie/bulletins/valider` - Validation

**Logique de Calcul IRSA** :
```
1. Base imposable = Salaire brut - CNAPS (1%) - OSTIE (1%)  [si CDI/CDD soumis]
2. IRSA brut = Application progressive des tranches
3. Réduction = nb_enfants × 2 000 Ar
4. IRSA net = MAX(3 000 Ar, IRSA brut - Réduction)
5. Salaire net = Salaire brut - CNAPS - OSTIE - IRSA net - Retenues diverses
```

**Tranches IRSA 2026** :
- 0 – 350 000 Ar : 0%
- 350 001 – 400 000 Ar : 5%
- 400 001 – 500 000 Ar : 10%
- 500 001 – 600 000 Ar : 15%
- 600 001 – 4 000 000 Ar : 20%
- 4 000 001 et plus : 25%

---

### 1️⃣2️⃣ MODULE PERSONNEL (RH Complet)
**Fonctionnalités** :
- **Formulaire multi-étapes (6 étapes)** :
  1. Type de personnel (Enseignant/Administratif)
  2. État civil (Nom, Prénom, Date naissance, etc.)
  3. Formation (Diplôme, Année d'obtention - **OBLIGATOIRE**)
  4. Situation familiale (Nombre d'enfants - **OBLIGATOIRE**, Situation matrimoniale - **OBLIGATOIRE**)
  5. Contrat (Type - **OBLIGATOIRE**, Grade - **OBLIGATOIRE**, Date début)
  6. Documents justificatifs
- Liste consolidée du personnel (enseignants + administratifs)
- Fiche détaillée du personnel
- Génération de certificat de travail (PDF)
- Gestion des absences du personnel
- Export Excel/PDF

**Tables BDD** :
- `personnels` (id, matricule, nom, prenom, date_naissance, lieu_naissance, sexe, telephone, email, adresse, photo, type_personnel, created_at)
- `personnels_enseignants` (id, personnel_id, diplome, specialite, annee_obtention)
- `personnels_administratifs` (id, personnel_id, poste_id, service)
- `postes_administratifs` (id, nom, description)
- `absences_personnels` (id, personnel_id, date_debut, date_fin, type_absence, motif, justifiee, statut_validation, created_at)

**Routes** :
- `GET /liste-personnel` - Liste unifiée
- `GET/POST /personnel/nouveau` - Formulaire multi-étapes
- `GET /personnel/details/{id}` - Fiche détaillée
- `GET /personnel/certificat/{id}` - Certificat PDF
- `GET /absences_personnel/list` - Absences personnel

---

### 1️⃣3️⃣ MODULE ENSEIGNANTS
**Fonctionnalités** :
- Liste des enseignants
- Association enseignants-matières-classes
- Emploi du temps de l'enseignant
- Historique des enseignements

**Tables** : Utilise `personnels` + `personnels_enseignants` + `enseignements`

---

### 1️⃣4️⃣ MODULE CALENDRIER SCOLAIRE
**Fonctionnalités** :
- Gestion des années scolaires (2024-2025, etc.)
- Activation de l'année scolaire en cours
- Gestion des vacances scolaires
- Gestion des jours fériés
- Calendrier visuel

**Tables BDD** :
- `annees_scolaires` (id, nom, date_debut, date_fin, active, created_at)
- `calendrier_scolaire` (id, annee_scolaire_id, type, nom, date_debut, date_fin, description)

---

### 1️⃣5️⃣ MODULE NOTIFICATIONS & MESSAGERIE
**Fonctionnalités** :
- Envoi de SMS (via API Telma/Orange/Airtel)
- Envoi d'emails
- Modèles de notifications prédéfinis
- Messagerie interne
- Historique des envois

**Tables BDD** :
- `notifications` (id, type, destinataire, contenu, statut, sent_at)
- `modeles_notifications` (id, nom, type, sujet, contenu, variables)
- `messages` (id, expediteur_id, destinataire_id, sujet, contenu, lu, created_at)

---

### 1️⃣6️⃣ MODULE SYSTÈME & CONFIGURATION
**Fonctionnalités** :
- Paramètres de l'école (nom, logo, adresse, contacts)
- Gestion des utilisateurs
- Gestion des groupes et permissions
- Synchronisation automatique des comptes parents
- Logs d'activité
- Sauvegarde de la base de données

**Routes** :
- `GET/POST /systeme/config` - Configuration école
- `GET /systeme/utilisateurs` - Gestion utilisateurs
- `GET /systeme/groupes` - Gestion groupes
- `GET /systeme/logs` - Logs d'activité

---

### MODULES ADDITIONNELS (17-37)
17. **Sanctions** - Gestion des sanctions disciplinaires
18. **Conseils de classe** - Décisions de passage/redoublement
19. **Parcours scolaires** - Historique complet élève
20. **Articles scolaires** - Vente fournitures/uniformes
21. **Annonces** - Actualités et communications
22. **Rôles** - Gestion fine des permissions
23-37. (Autres modules selon besoins spécifiques)

---

## 🎨 DESIGN & INTERFACE UTILISATEUR

### Principes de Design
1. **Design Premium et Moderne** :
   - Palette de couleurs harmonieuse (HSL personnalisées, pas de couleurs génériques)
   - Mode sombre/clair
   - Glassmorphism pour les cartes
   - Gradients subtils
   - Micro-animations sur hover et interactions

2. **Typographie** :
   - Google Fonts : Inter, Roboto ou Outfit
   - Hiérarchie claire (h1-h6)

3. **Composants Réutilisables** :
   - Boutons avec états (primary, secondary, success, danger, warning)
   - Cartes (cards) avec ombre portée
   - Modales de confirmation
   - Alertes (success, error, warning, info)
   - Tables responsives avec tri et pagination
   - Formulaires avec validation visuelle

4. **Sidebar Navigation** :
   - Menu latéral collapsible
   - Icônes Font Awesome
   - Sous-menus déroulants
   - Indicateur de page active

5. **Responsive Design** :
   - Mobile-first approach
   - Breakpoints : 576px, 768px, 992px, 1200px
   - Menu hamburger sur mobile

### Structure du Layout
```html
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - ROSSIGNOLES</title>
    <link rel="stylesheet" href="/public/css/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'layout/header.php'; ?>
    <?php include 'layout/sidebar.php'; ?>
    
    <main class="content">
        <?php include 'layout/breadcrumb.php'; ?>
        <div class="container">
            <!-- Contenu de la page -->
        </div>
    </main>
    
    <?php include 'layout/footer.php'; ?>
    <script src="/public/js/main.js"></script>
</body>
</html>
```

### Menu Sidebar (Structure)
```
📊 Tableau de bord
👥 Inscriptions
   ├─ Nouvelle inscription
   ├─ Liste des inscriptions
   └─ Documents requis
🎓 Élèves
   ├─ Liste des élèves
   ├─ Ajouter un élève
   └─ Parcours scolaires
👨‍👩‍👧 Parents
🏫 Classes & Pédagogie
   ├─ Classes
   ├─ Niveaux & Séries
   ├─ Matières
   ├─ Enseignements
   └─ Emplois du temps
📝 Évaluations
   ├─ Saisie de notes
   ├─ Bulletins
   └─ Examens
📅 Assiduité
   ├─ Absences (onglet)
   └─ Retards (onglet)
👨‍🏫 Personnel
   ├─ Liste du personnel
   ├─ Nouveau personnel
   ├─ Enseignants
   └─ Absences personnel
💰 Finance
   ├─ Dashboard
   ├─ Échéancier
   ├─ Paiements
   ├─ Reçus
   ├─ Tarifs
   └─ Paie du personnel ⭐
📆 Calendrier
⚙️ Système
   ├─ Configuration
   ├─ Utilisateurs
   ├─ Groupes & Permissions
   └─ Logs
```

---

## 🗄️ BASE DE DONNÉES (Structure Complète)

### Schéma Relationnel (86 Tables)

#### Authentification & Sécurité
```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    groupe_id INT,
    actif BOOLEAN DEFAULT TRUE,
    last_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (groupe_id) REFERENCES user_groups(id)
);

CREATE TABLE user_groups (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(50) UNIQUE NOT NULL,
    description TEXT
);

CREATE TABLE roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(50) UNIQUE NOT NULL,
    description TEXT
);

CREATE TABLE permissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(100) UNIQUE NOT NULL,
    module VARCHAR(50) NOT NULL,
    action VARCHAR(50) NOT NULL,
    description TEXT
);

CREATE TABLE roles_permissions (
    role_id INT,
    permission_id INT,
    PRIMARY KEY (role_id, permission_id),
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
);

CREATE TABLE users_roles (
    user_id INT,
    role_id INT,
    PRIMARY KEY (user_id, role_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
);

CREATE TABLE logs_activites (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    module VARCHAR(50),
    details TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

#### Élèves & Parents
```sql
CREATE TABLE eleves (
    id INT PRIMARY KEY AUTO_INCREMENT,
    matricule VARCHAR(20) UNIQUE NOT NULL,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    date_naissance DATE NOT NULL,
    lieu_naissance VARCHAR(100),
    sexe ENUM('M', 'F') NOT NULL,
    adresse TEXT,
    telephone VARCHAR(20),
    email VARCHAR(100),
    photo VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_matricule (matricule),
    INDEX idx_nom_prenom (nom, prenom)
);

CREATE TABLE parents (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    telephone VARCHAR(20) NOT NULL,
    email VARCHAR(100),
    profession VARCHAR(100),
    adresse TEXT,
    user_id INT UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE eleves_parents (
    id INT PRIMARY KEY AUTO_INCREMENT,
    eleve_id INT NOT NULL,
    parent_id INT NOT NULL,
    lien_parente ENUM('Père', 'Mère', 'Tuteur', 'Autre') NOT NULL,
    FOREIGN KEY (eleve_id) REFERENCES eleves(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES parents(id) ON DELETE CASCADE
);
```

#### Pédagogie
```sql
CREATE TABLE annees_scolaires (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(20) UNIQUE NOT NULL,
    date_debut DATE NOT NULL,
    date_fin DATE NOT NULL,
    active BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE cycles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(50) UNIQUE NOT NULL,
    ordre INT NOT NULL
);

CREATE TABLE niveaux (
    id INT PRIMARY KEY AUTO_INCREMENT,
    cycle_id INT NOT NULL,
    nom VARCHAR(50) NOT NULL,
    code VARCHAR(10) UNIQUE NOT NULL,
    ordre INT NOT NULL,
    FOREIGN KEY (cycle_id) REFERENCES cycles(id)
);

CREATE TABLE series (
    id INT PRIMARY KEY AUTO_INCREMENT,
    niveau_id INT NOT NULL,
    nom VARCHAR(50) NOT NULL,
    code VARCHAR(10) NOT NULL,
    actif BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (niveau_id) REFERENCES niveaux(id)
);

CREATE TABLE classes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    niveau_id INT NOT NULL,
    serie_id INT,
    code_classe VARCHAR(20) UNIQUE NOT NULL,
    nom_classe VARCHAR(100) NOT NULL,
    annee_scolaire_id INT NOT NULL,
    capacite INT DEFAULT 40,
    FOREIGN KEY (niveau_id) REFERENCES niveaux(id),
    FOREIGN KEY (serie_id) REFERENCES series(id),
    FOREIGN KEY (annee_scolaire_id) REFERENCES annees_scolaires(id)
);

CREATE TABLE matieres (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) UNIQUE NOT NULL,
    code VARCHAR(10) UNIQUE NOT NULL,
    couleur VARCHAR(7) DEFAULT '#3498db'
);

CREATE TABLE matieres_series (
    id INT PRIMARY KEY AUTO_INCREMENT,
    matiere_id INT NOT NULL,
    serie_id INT NOT NULL,
    coefficient DECIMAL(3,1) DEFAULT 1.0,
    obligatoire BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (matiere_id) REFERENCES matieres(id),
    FOREIGN KEY (serie_id) REFERENCES series(id)
);

CREATE TABLE parcours_eleves (
    id INT PRIMARY KEY AUTO_INCREMENT,
    eleve_id INT NOT NULL,
    annee_scolaire_id INT NOT NULL,
    classe_id INT NOT NULL,
    statut ENUM('Inscrit', 'Admis', 'Redoublant', 'Exclu', 'Transféré') DEFAULT 'Inscrit',
    decision_conseil VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (eleve_id) REFERENCES eleves(id),
    FOREIGN KEY (annee_scolaire_id) REFERENCES annees_scolaires(id),
    FOREIGN KEY (classe_id) REFERENCES classes(id)
);
```

#### Assiduité
```sql
CREATE TABLE absences (
    id INT PRIMARY KEY AUTO_INCREMENT,
    eleve_id INT NOT NULL,
    classe_id INT NOT NULL,
    date_absence DATE NOT NULL,
    type ENUM('absence', 'retard') NOT NULL,
    periode VARCHAR(50),
    heure_debut TIME,
    heure_fin TIME,
    matiere_id INT,
    enseignant_id INT,
    justifiee BOOLEAN DEFAULT FALSE,
    motif TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (eleve_id) REFERENCES eleves(id),
    FOREIGN KEY (classe_id) REFERENCES classes(id),
    FOREIGN KEY (matiere_id) REFERENCES matieres(id),
    FOREIGN KEY (enseignant_id) REFERENCES personnels(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    INDEX idx_date_type (date_absence, type),
    INDEX idx_eleve_date (eleve_id, date_absence)
);
```

#### Évaluations
```sql
CREATE TABLE periodes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    annee_scolaire_id INT NOT NULL,
    nom VARCHAR(50) NOT NULL,
    type ENUM('Trimestre', 'Semestre') NOT NULL,
    date_debut DATE NOT NULL,
    date_fin DATE NOT NULL,
    ordre INT NOT NULL,
    FOREIGN KEY (annee_scolaire_id) REFERENCES annees_scolaires(id)
);

CREATE TABLE bulletins (
    id INT PRIMARY KEY AUTO_INCREMENT,
    eleve_id INT NOT NULL,
    classe_id INT NOT NULL,
    periode_id INT NOT NULL,
    moyenne_generale DECIMAL(5,2),
    rang INT,
    appreciation_generale TEXT,
    decision VARCHAR(100),
    valide BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (eleve_id) REFERENCES eleves(id),
    FOREIGN KEY (classe_id) REFERENCES classes(id),
    FOREIGN KEY (periode_id) REFERENCES periodes(id),
    UNIQUE KEY unique_bulletin (eleve_id, periode_id)
);
```

#### Finance
```sql
CREATE TABLE tarifs_inscriptions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    niveau_id INT NOT NULL,
    annee_scolaire_id INT NOT NULL,
    montant DECIMAL(10,2) NOT NULL,
    actif BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (niveau_id) REFERENCES niveaux(id),
    FOREIGN KEY (annee_scolaire_id) REFERENCES annees_scolaires(id)
);

CREATE TABLE echeancier_ecolages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    eleve_id INT NOT NULL,
    annee_scolaire_id INT NOT NULL,
    mois INT NOT NULL,
    montant DECIMAL(10,2) NOT NULL,
    date_echeance DATE NOT NULL,
    statut ENUM('En attente', 'Payé', 'En retard') DEFAULT 'En attente',
    FOREIGN KEY (eleve_id) REFERENCES eleves(id),
    FOREIGN KEY (annee_scolaire_id) REFERENCES annees_scolaires(id),
    INDEX idx_statut_date (statut, date_echeance)
);

CREATE TABLE paiements (
    id INT PRIMARY KEY AUTO_INCREMENT,
    eleve_id INT NOT NULL,
    parent_id INT,
    type_paiement ENUM('Inscription', 'Écolage', 'Frais divers') NOT NULL,
    montant DECIMAL(10,2) NOT NULL,
    mode_paiement ENUM('Espèces', 'Chèque', 'Virement', 'Mobile Money') NOT NULL,
    numero_recu VARCHAR(50) UNIQUE NOT NULL,
    date_paiement DATE NOT NULL,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (eleve_id) REFERENCES eleves(id),
    FOREIGN KEY (parent_id) REFERENCES parents(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    INDEX idx_numero_recu (numero_recu)
);
```

#### Paie
```sql
CREATE TABLE paie_parametres_cotisations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(50) UNIQUE NOT NULL,
    taux_salarial DECIMAL(5,2) NOT NULL,
    taux_patronal DECIMAL(5,2) NOT NULL,
    actif BOOLEAN DEFAULT TRUE
);

CREATE TABLE paie_tranches_irsa (
    id INT PRIMARY KEY AUTO_INCREMENT,
    montant_min DECIMAL(12,2) NOT NULL,
    montant_max DECIMAL(12,2),
    taux DECIMAL(5,2) NOT NULL,
    ordre INT NOT NULL,
    annee INT NOT NULL DEFAULT 2026
);

CREATE TABLE paie_contrats (
    id INT PRIMARY KEY AUTO_INCREMENT,
    personnel_id INT NOT NULL,
    type_contrat ENUM('CDI', 'CDD', 'Stage', 'Intérim') NOT NULL,
    salaire_brut_base DECIMAL(12,2) NOT NULL,
    nb_enfants INT DEFAULT 0,
    soumis_cotisations BOOLEAN DEFAULT TRUE,
    date_debut DATE NOT NULL,
    date_fin DATE,
    actif BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (personnel_id) REFERENCES personnels(id)
);

CREATE TABLE paie_bulletins (
    id INT PRIMARY KEY AUTO_INCREMENT,
    contrat_id INT NOT NULL,
    periode VARCHAR(7) NOT NULL, -- Format: YYYY-MM
    salaire_brut DECIMAL(12,2) NOT NULL,
    cnaps_salarial DECIMAL(12,2) DEFAULT 0,
    ostie_salarial DECIMAL(12,2) DEFAULT 0,
    base_imposable_irsa DECIMAL(12,2) DEFAULT 0,
    irsa_brut DECIMAL(12,2) DEFAULT 0,
    reduction_irsa DECIMAL(12,2) DEFAULT 0,
    irsa_net DECIMAL(12,2) DEFAULT 0,
    salaire_net DECIMAL(12,2) NOT NULL,
    cnaps_patronal DECIMAL(12,2) DEFAULT 0,
    ostie_patronal DECIMAL(12,2) DEFAULT 0,
    fmfp_patronal DECIMAL(12,2) DEFAULT 0,
    cout_total_employeur DECIMAL(12,2) NOT NULL,
    valide BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (contrat_id) REFERENCES paie_contrats(id),
    UNIQUE KEY unique_bulletin_paie (contrat_id, periode)
);
```

#### Personnel
```sql
CREATE TABLE personnels (
    id INT PRIMARY KEY AUTO_INCREMENT,
    matricule VARCHAR(20) UNIQUE NOT NULL,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    date_naissance DATE NOT NULL,
    lieu_naissance VARCHAR(100),
    sexe ENUM('M', 'F') NOT NULL,
    telephone VARCHAR(20),
    email VARCHAR(100),
    adresse TEXT,
    photo VARCHAR(255),
    type_personnel ENUM('Enseignant', 'Administratif') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE personnels_enseignants (
    id INT PRIMARY KEY AUTO_INCREMENT,
    personnel_id INT UNIQUE NOT NULL,
    diplome VARCHAR(100) NOT NULL,
    specialite VARCHAR(100),
    annee_obtention INT NOT NULL,
    FOREIGN KEY (personnel_id) REFERENCES personnels(id) ON DELETE CASCADE
);

CREATE TABLE absences_personnels (
    id INT PRIMARY KEY AUTO_INCREMENT,
    personnel_id INT NOT NULL,
    date_debut DATE NOT NULL,
    date_fin DATE NOT NULL,
    type_absence ENUM('Congé', 'Maladie', 'Permission', 'Autre') NOT NULL,
    motif TEXT,
    justifiee BOOLEAN DEFAULT FALSE,
    statut_validation ENUM('En attente', 'Validée', 'Refusée') DEFAULT 'En attente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (personnel_id) REFERENCES personnels(id)
);
```

---

## 🔐 SÉCURITÉ & BONNES PRATIQUES

### 1. Protection CSRF
Tous les formulaires POST doivent inclure un token CSRF :
```php
// Génération du token (dans BaseController)
protected function generateCsrfToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Vérification (dans Middleware)
public function verifyCsrf() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            throw new Exception('Token CSRF invalide');
        }
    }
}

// Dans les vues
<input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
```

### 2. Validation des Données
```php
// Exemple de validation
public function validateEleve($data) {
    $errors = [];
    
    if (empty($data['nom'])) {
        $errors['nom'] = 'Le nom est obligatoire';
    }
    
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Email invalide';
    }
    
    return $errors;
}
```

### 3. Gestion de Session
```php
// Timeout de session (15 minutes)
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 900)) {
    session_unset();
    session_destroy();
    header('Location: /auth/login?timeout=1');
    exit;
}
$_SESSION['last_activity'] = time();
```

### 4. Requêtes Préparées
```php
// Toujours utiliser des requêtes préparées
$stmt = $this->db->prepare("SELECT * FROM eleves WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
```

---

## 📱 FONCTIONNALITÉS AJAX

### Toggle Switch pour Absences
```javascript
// public/js/absences.js
document.querySelectorAll('.toggle-justifiee').forEach(toggle => {
    toggle.addEventListener('change', async function() {
        const absenceId = this.dataset.absenceId;
        const justifiee = this.checked;
        
        try {
            const response = await fetch('/absences/toggle-justifiee', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': document.querySelector('[name="csrf_token"]').value
                },
                body: JSON.stringify({ id: absenceId, justifiee })
            });
            
            const data = await response.json();
            
            if (data.success) {
                showNotification('Statut mis à jour', 'success');
            } else {
                this.checked = !justifiee; // Revert
                showNotification('Erreur lors de la mise à jour', 'error');
            }
        } catch (error) {
            console.error(error);
            this.checked = !justifiee;
        }
    });
});
```

---

## 📄 GÉNÉRATION DE PDF

### Exemple : Reçu de Paiement
```php
// app/Controllers/FinanceController.php
public function exportRecuPdf($id) {
    require_once __DIR__ . '/../../vendor/autoload.php';
    
    $paiement = $this->paiementModel->find($id);
    $eleve = $this->eleveModel->find($paiement['eleve_id']);
    $ecole = $this->parametresModel->getParametres();
    
    $html = $this->renderView('pdf/recu_paiement', [
        'paiement' => $paiement,
        'eleve' => $eleve,
        'ecole' => $ecole
    ]);
    
    $dompdf = new \Dompdf\Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $dompdf->stream("Recu_{$paiement['numero_recu']}.pdf");
}
```

---

## 🚀 INSTALLATION & DÉPLOIEMENT

### Prérequis
- PHP 8.0+
- MySQL 5.7+ ou MariaDB 10.3+
- Apache 2.4+ avec mod_rewrite
- Composer

### Installation
```bash
# 1. Cloner le projet
git clone https://github.com/votre-repo/rossignoles.git
cd rossignoles

# 2. Installer les dépendances
composer install

# 3. Configuration
cp .env.example .env
# Éditer .env avec vos paramètres BDD

# 4. Créer la base de données
mysql -u root -p
CREATE DATABASE rossignoles CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# 5. Importer le schéma
mysql -u root -p rossignoles < database/schema.sql

# 6. Initialiser les données
php database/init_paie.php
php database/init_permissions.php

# 7. Configurer Apache
# Pointer le DocumentRoot vers le dossier du projet
# Activer mod_rewrite

# 8. Accéder à l'application
http://localhost/ROSSIGNOLES
```

### Compte par défaut
- **Username** : admin
- **Password** : admin123 (à changer immédiatement)

---

## 📚 DOCUMENTATION ADDITIONNELLE

### Routes (425 routes définies)
Voir le fichier `routes/web.php` pour la liste complète.

### Modèles (86 modèles)
Chaque table a son modèle dans `app/Models/`.

### Contrôleurs (37 contrôleurs)
Organisés par module dans `app/Controllers/`.

---

## 🎯 PROCHAINES ÉTAPES & AMÉLIORATIONS

### Court terme
- [ ] Export PDF des bulletins de paie
- [ ] Tableau de bord avec graphiques interactifs
- [ ] Gestion des retenues diverses (paie)
- [ ] Envoi automatique des bulletins par email

### Moyen terme
- [ ] Application mobile (React Native ou Flutter)
- [ ] API REST pour intégrations tierces
- [ ] Système de notifications push
- [ ] Intégration comptabilité (export vers logiciels comptables)

### Long terme
- [ ] Intelligence artificielle pour prédiction des résultats
- [ ] Plateforme e-learning intégrée
- [ ] Gestion de la cantine et du transport
- [ ] Portail parent avec suivi en temps réel

---

## 📞 SUPPORT & MAINTENANCE

### Logs
- Logs d'erreurs : `storage/logs/error.log`
- Logs d'activité : Table `logs_activites`

### Sauvegarde
```bash
# Sauvegarde automatique quotidienne
0 2 * * * mysqldump -u root -p rossignoles > /backups/rossignoles_$(date +\%Y\%m\%d).sql
```

---

## ✅ CHECKLIST DE DÉVELOPPEMENT

### Phase 1 : Infrastructure (Semaine 1-2)
- [ ] Configuration de l'environnement
- [ ] Structure MVC de base
- [ ] Système de routing
- [ ] Connexion BDD
- [ ] Authentification & sessions
- [ ] Middleware CSRF

### Phase 2 : Modules Core (Semaine 3-6)
- [ ] Module Élèves
- [ ] Module Parents
- [ ] Module Inscriptions (workflow 6 étapes)
- [ ] Module Classes & Pédagogie
- [ ] Module Assiduité (onglets Absences/Retards)

### Phase 3 : Modules Avancés (Semaine 7-10)
- [ ] Module Évaluations & Bulletins
- [ ] Module Finance (échéancier, paiements, reçus)
- [ ] Module Personnel (formulaire 6 étapes)
- [ ] Module Paie (calcul IRSA, bulletins)

### Phase 4 : Finitions (Semaine 11-12)
- [ ] Design premium et responsive
- [ ] Génération PDF (certificats, bulletins, reçus)
- [ ] Exports Excel
- [ ] Tests et corrections
- [ ] Documentation utilisateur

---

## 🎓 EXEMPLE DE CODE : Contrôleur Absences

```php
<?php
// app/Controllers/AbsencesController.php

class AbsencesController extends BaseController {
    
    public function list() {
        $type = $_GET['type'] ?? 'absence';
        $page = $_GET['page'] ?? 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $absences = $this->absenceModel->getAbsencesWithDetails($type, $limit, $offset);
        $total = $this->absenceModel->countByType($type);
        
        $this->render('absences/list', [
            'absences' => $absences,
            'type' => $type,
            'pagination' => [
                'current' => $page,
                'total' => ceil($total / $limit)
            ]
        ]);
    }
    
    public function toggleJustifiee() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
            return;
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'] ?? null;
        $justifiee = $data['justifiee'] ?? false;
        
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID manquant']);
            return;
        }
        
        $result = $this->absenceModel->update($id, ['justifiee' => $justifiee]);
        
        if ($result) {
            $this->logActivity('absences', 'toggle_justifiee', "Absence #{$id} marquée comme " . ($justifiee ? 'justifiée' : 'non justifiée'));
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
        }
    }
}
```

---

## 🎨 EXEMPLE DE VUE : Liste Absences avec Onglets

```php
<!-- app/Views/absences/list.php -->
<div class="page-header">
    <h1><i class="fas fa-calendar-check"></i> Assiduité</h1>
</div>

<!-- Onglets -->
<ul class="nav nav-tabs" role="tablist">
    <li class="nav-item">
        <a class="nav-link <?= $type === 'absence' ? 'active' : '' ?>" 
           href="/absences/list?type=absence">
            Absences
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $type === 'retard' ? 'active' : '' ?>" 
           href="/absences/list?type=retard">
            Retards
        </a>
    </li>
</ul>

<div class="tab-content">
    <div class="actions-bar">
        <a href="/absences/add?type=<?= $type ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> 
            Ajouter <?= $type === 'absence' ? 'une absence' : 'un retard' ?>
        </a>
    </div>
    
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Date</th>
                <th>Élève</th>
                <th>Code Classe</th>
                <th>Période</th>
                <th>Matière</th>
                <th>Professeur</th>
                <th>Justifié</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($absences as $absence): ?>
            <tr>
                <td><?= date('d/m/Y', strtotime($absence['date_absence'])) ?></td>
                <td><?= htmlspecialchars($absence['nom_eleve']) ?></td>
                <td><?= htmlspecialchars($absence['code_classe']) ?></td>
                <td>
                    <?php if ($absence['heure_debut'] && $absence['heure_fin']): ?>
                        <?= date('H:i', strtotime($absence['heure_debut'])) ?> - 
                        <?= date('H:i', strtotime($absence['heure_fin'])) ?>
                    <?php else: ?>
                        <?= htmlspecialchars($absence['periode']) ?>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($absence['matiere'] ?? '-') ?></td>
                <td><?= htmlspecialchars($absence['professeur'] ?? '-') ?></td>
                <td>
                    <label class="switch">
                        <input type="checkbox" 
                               class="toggle-justifiee" 
                               data-absence-id="<?= $absence['id'] ?>"
                               <?= $absence['justifiee'] ? 'checked' : '' ?>>
                        <span class="slider"></span>
                    </label>
                </td>
                <td>
                    <a href="/absences/edit/<?= $absence['id'] ?>" class="btn btn-sm btn-info">
                        <i class="fas fa-edit"></i>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="/public/js/absences.js"></script>
```

---

## 🎯 OBJECTIFS DE QUALITÉ

1. **Performance** : Temps de chargement < 2 secondes
2. **Sécurité** : 0 vulnérabilité critique
3. **Accessibilité** : Score Lighthouse > 90
4. **Maintenabilité** : Code coverage > 70%
5. **UX** : Satisfaction utilisateur > 4/5

---

**Date de création** : 2026-02-06  
**Version** : 1.0  
**Auteur** : Système ERP ROSSIGNOLES  
**Licence** : Propriétaire

---

## 🚀 COMMENCER MAINTENANT

Pour démarrer le développement, commence par :

1. **Créer la structure de base** :
   ```bash
   mkdir -p app/{Controllers,Models,Services,Views}
   mkdir -p config database public/{css,js,images} routes storage
   ```

2. **Configurer le point d'entrée** (`index.php`)

3. **Créer le système de routing** (`routes/web.php`)

4. **Développer le module d'authentification** (priorité 1)

5. **Implémenter les modules dans l'ordre** : Élèves → Inscriptions → Classes → Assiduité → Finance → Paie

Bonne chance ! 🎉
