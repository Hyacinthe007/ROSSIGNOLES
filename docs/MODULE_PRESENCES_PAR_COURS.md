# 📊 Module de Suivi de Présence par Cours

## 🎯 Objectif

Ce module permet de **visualiser facilement la liste des élèves présents ou absents pour chaque cours** basé sur l'emploi du temps, sans entrer dans les détails du contenu du cours.

## ✨ Fonctionnalités

### 1. **Vue Principale : Cours du Jour** (`/presences`)
- Affiche tous les cours prévus pour une date donnée
- Pour chaque cours, affiche :
  - ✅ Horaire (ex: 08:00 - 09:30)
  - ✅ Classe (ex: 6èmeA)
  - ✅ Matière (ex: Mathématiques)
  - ✅ Enseignant
  - ✅ Effectif total de la classe
  - ✅ Nombre d'élèves présents
  - ✅ Nombre d'élèves absents
  - ✅ Taux de présence (%)

**Filtres disponibles** :
- Date (sélecteur de date)
- Classe (toutes ou une classe spécifique)
- Enseignant (tous ou un enseignant spécifique)

**Statistiques globales** :
- Nombre total de cours
- Total présents
- Total absents
- Taux de présence global

### 2. **Détails d'un Cours** (`/presences/details-cours`)
- Liste complète de tous les élèves de la classe
- Pour chaque élève :
  - ✅ Photo (si disponible)
  - ✅ Matricule
  - ✅ Nom et Prénom
  - ✅ Statut : **Présent** (badge vert) ou **Absent** (badge rouge)
  - ✅ Si absent : Motif et statut "Justifié" (badge jaune)

**Fonctionnalités** :
- Impression de la liste (bouton Imprimer)
- Statistiques du cours :
  - Effectif total
  - Nombre de présents
  - Nombre d'absents
  - Taux de présence

### 3. **Historique des Cours** (`/presences/historique`)
- Vue sur une période (date début - date fin)
- Pour chaque cours récurrent de l'emploi du temps :
  - ✅ Jour de la semaine
  - ✅ Horaire
  - ✅ Classe et Matière
  - ✅ Enseignant
  - ✅ Nombre de cours effectués dans la période
  - ✅ Total présents sur la période
  - ✅ Total absents sur la période
  - ✅ Taux de présence moyen

**Analyse visuelle** :
- Graphiques de présence par classe (barres de progression)

## 🗂️ Structure des Fichiers

### Contrôleur
```
app/Controllers/PresencesController.php
```
**Méthodes** :
- `index()` - Liste des cours du jour
- `detailsCours()` - Détails d'un cours spécifique
- `historique()` - Historique sur une période

### Vues
```
app/Views/presences/
├── index.php           # Liste des cours du jour
├── details_cours.php   # Liste détaillée des élèves
└── historique.php      # Historique des cours
```

### Routes
```php
['pattern' => 'presences', 'method' => 'GET', 'handler' => 'PresencesController@index'],
['pattern' => 'presences/details-cours', 'method' => 'GET', 'handler' => 'PresencesController@detailsCours'],
['pattern' => 'presences/historique', 'method' => 'GET', 'handler' => 'PresencesController@historique'],
```

## 🔗 Accès au Module

### Menu Sidebar
**Pédagogie** > **Présences par cours**

### URLs Directes
- Liste du jour : `http://localhost/ROSSIGNOLES/presences`
- Historique : `http://localhost/ROSSIGNOLES/presences/historique`

## 📊 Logique de Fonctionnement

### Comment ça marche ?

1. **Récupération des cours** :
   - Le système lit l'emploi du temps (`emplois_temps`)
   - Filtre par jour de la semaine (ex: "lundi")
   - Récupère les informations : classe, matière, enseignant, horaires

2. **Calcul des présences** :
   - Pour chaque cours, compte le nombre total d'élèves inscrits dans la classe
   - Compte le nombre d'absences enregistrées pour ce cours à cette date
   - Calcule : **Présents = Total - Absents**

3. **Identification des absences** :
   - Les absences sont liées à un cours par :
     - `classe_id`
     - `date_absence`
     - `heure_debut` et `heure_fin` (correspondant à l'emploi du temps)

### Exemple de Requête SQL

```sql
-- Récupérer les cours du jour
SELECT et.id, et.heure_debut, et.heure_fin,
       m.nom as matiere_nom,
       c.code as classe_code,
       CONCAT(p.nom, ' ', p.prenom) as enseignant_nom,
       (SELECT COUNT(*) FROM inscriptions i 
        WHERE i.classe_id = c.id 
        AND i.statut IN ('active', 'validee', 'en_cours')) as nb_eleves_total
FROM emplois_temps et
JOIN matieres m ON et.matiere_id = m.id
JOIN classes c ON et.classe_id = c.id
LEFT JOIN personnels p ON et.personnel_id = p.id
WHERE et.annee_scolaire_id = ?
  AND et.jour_semaine = 'lundi'
  AND et.actif = 1
ORDER BY et.heure_debut ASC;

-- Compter les absents pour un cours
SELECT COUNT(*) as count 
FROM absences 
WHERE classe_id = ? 
  AND date_absence = ?
  AND heure_debut = ?
  AND heure_fin = ?
  AND type = 'absence';
```

## 🎨 Interface Utilisateur

### Codes Couleur
- **Vert** : Présent / Taux ≥ 90%
- **Jaune** : Justifié / Taux 75-89%
- **Rouge** : Absent / Taux < 75%
- **Bleu** : Informations générales

### Badges
- **Effectif** : Badge bleu info
- **Présents** : Badge vert success
- **Absents** : Badge rouge danger
- **Taux** : Badge dynamique selon le taux

## 📈 Cas d'Usage

### Scénario 1 : Enseignant vérifie la présence
1. Accède à `/presences`
2. Sélectionne la date du jour
3. Clique sur "Détails" pour son cours de 08:00
4. Voit la liste complète avec qui est présent/absent
5. Peut imprimer la liste pour ses archives

### Scénario 2 : Directeur analyse l'assiduité
1. Accède à `/presences/historique`
2. Sélectionne une période (ex: dernier mois)
3. Filtre par classe (ex: Terminale S)
4. Voit les statistiques de présence pour tous les cours
5. Identifie les cours avec faible taux de présence

### Scénario 3 : Surveillance quotidienne
1. Accède à `/presences` chaque matin
2. Voit en un coup d'œil tous les cours de la journée
3. Repère rapidement les cours avec beaucoup d'absents
4. Peut intervenir si nécessaire

## 🔧 Personnalisations Possibles

### Améliorations Futures
- [ ] Export Excel de la liste de présence
- [ ] Export PDF du récapitulatif du jour
- [ ] Notifications automatiques si taux < seuil
- [ ] Graphiques d'évolution de l'assiduité
- [ ] Comparaison entre classes
- [ ] Alerte pour absences répétées d'un élève

## ⚙️ Configuration Requise

### Tables Utilisées
- `emplois_temps` - Emplois du temps des classes
- `absences` - Enregistrements d'absences
- `inscriptions` - Élèves inscrits par classe
- `classes`, `matieres`, `personnels` - Données de référence

### Permissions
- `absences.view` - Requis pour accéder au module

## 📝 Notes Importantes

1. **Synchronisation avec l'emploi du temps** :
   - Les présences sont basées sur l'emploi du temps actif
   - Si un cours n'est pas dans l'emploi du temps, il n'apparaîtra pas

2. **Gestion des absences** :
   - Les absences doivent être enregistrées avec les horaires correspondant à l'emploi du temps
   - Le système fait la correspondance automatiquement

3. **Performance** :
   - Les requêtes sont optimisées avec des JOIN
   - Pagination recommandée pour l'historique sur longues périodes

---

**Date de création** : 2026-02-06  
**Version** : 1.0  
**Auteur** : Système ERP ROSSIGNOLES
