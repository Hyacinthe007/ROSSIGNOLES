# Modifications - Formulaire Personnel

## Date : 23 janvier 2026

### 1️⃣ Modification du Format du Matricule ✅

**Fichier modifié** : `app/Controllers/PersonnelController.php`

**Changement** :
- **AVANT** : `ENS-2026-0001` (avec l'année)
- **APRÈS** : `ENS-0001` (sans l'année)

**Détails** :
- Suppression de la variable `$year` dans la fonction `genererMatricule()`
- Modification de la requête SQL pour compter tous les personnels avec le préfixe (ENS ou PER)
- Simplification du format de sortie : `sprintf('%s-%04d', $prefix, $next)`

**Avantages** :
- Format plus simple et plus court
- Matricule unique et permanent pour chaque membre du personnel
- Pas de duplication de numéros entre années

---

### 2️⃣ Champ Spécialité Multi-Sélection avec Autocomplétion ✅

**Fichier modifié** : `app/Views/personnel_consolide/etape2_formulaire.php`

**Changement** :
- **AVANT** : Champ texte simple (`<input type="text">`)
- **APRÈS** : Système de tags avec autocomplétion

**Fonctionnalités** :
1. **Champ de recherche** : L'utilisateur tape pour filtrer les spécialités
2. **Liste déroulante dynamique** : Affiche les suggestions correspondantes
3. **Sélection multiple** : Possibilité de sélectionner plusieurs spécialités
4. **Tags visuels** : Chaque spécialité sélectionnée apparaît comme un badge
5. **Suppression facile** : Clic sur le ✕ pour retirer une spécialité
6. **Validation** : Touche Entrée pour sélectionner la première suggestion

**Liste des spécialités disponibles** :
- Mathématiques
- Physique
- Chimie
- Biologie
- Sciences de la Vie et de la Terre (SVT)
- Français
- Anglais
- Espagnol
- Allemand
- Histoire
- Géographie
- Philosophie
- Économie
- Informatique
- Éducation Physique et Sportive (EPS)
- Arts Plastiques
- Musique
- Technologie
- Sciences Physiques

**Stockage** :
- Les spécialités sélectionnées sont stockées dans un champ caché séparées par des virgules
- Format : `"Mathématiques, Physique, Chimie"`

**Interface** :
- Design moderne avec badges arrondis bleu indigo
- Effet hover sur les suggestions
- Responsive et accessible

---

## Comment tester

1. Accédez à `http://localhost/ROSSIGNOLES/personnel/nouveau?etape=1`
2. Choisissez "Enseignant"
3. À l'étape 2 :
   - **Matricule** : Vérifiez qu'il est au format `ENS-0001`
   - **Spécialité** : 
     - Tapez "Math" → La liste affiche "Mathématiques"
     - Cliquez ou appuyez sur Entrée pour sélectionner
     - Le tag apparaît en dessous
     - Répétez pour ajouter d'autres spécialités
     - Cliquez sur ✕ pour supprimer un tag

---

## Notes techniques

- Le champ caché `specialite` contient toutes les spécialités séparées par des virgules
- La validation HTML5 `required` s'applique au champ caché
- Le JavaScript est encapsulé dans un `DOMContentLoaded` pour éviter les conflits
- Le système fonctionne uniquement pour les enseignants (condition `if ($isEnseignant)`)
