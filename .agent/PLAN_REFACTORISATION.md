# 🔧 PLAN DE REFACTORISATION - Amélioration de la cohérence du code

**Date :** 24 janvier 2026  
**Objectif :** Déplacer toutes les requêtes SQL des contrôleurs vers les modèles

---

## 📊 État actuel

### ✅ Déjà refactorisé :
- `BulletinsController` → `Bulletin::getAllWithDetails()`
- `BulletinsController` → `Classe::getAllWithCycleAndNiveau()`

### 🔄 À refactoriser (par priorité) :

#### **1. NotesController** (719 lignes - PRIORITÉ HAUTE)
**Requêtes à déplacer :**
- Ligne 46-53 : Récupération examens par classe/période → `ExamenFinal::getByClassePeriode()`
- Ligne 56-63 : Récupération interrogations par classe/période → `Interrogation::getByClassePeriode()`
- Ligne 424-431 : Détails évaluation avec matière/classe → `ExamenFinal::getDetailsWithRelations()` / `Interrogation::getDetailsWithRelations()`
- Ligne 486-494 : Élèves avec notes → `ExamenFinal::getElevesWithNotes()` / `Interrogation::getElevesWithNotes()`
- Ligne 534-549 : Persistance notes → Déplacer vers `NoteExamen` / `NoteInterrogation`
- Ligne 582-595 : Récupération périodes/classes → Déjà dans modèles, utiliser `getAll()`
- Ligne 613-631 : Statistiques globales → `Bulletin::getStatistiquesGlobales()`
- Ligne 645-662 : Statistiques par classe → `Bulletin::getStatistiquesParClasse()`
- Ligne 681-703 : Moyennes élèves → `Bulletin::getMoyennesEleves()`

#### **2. InscriptionsController** (61 Ko - PRIORITÉ HAUTE)
**Actions :**
- Analyser et extraire les requêtes complexes
- Créer méthodes dans `Inscription`, `Eleve`, `Facture`

#### **3. FinanceController** (45 Ko - PRIORITÉ MOYENNE)
**Actions :**
- Déplacer requêtes vers `Paiement`, `Facture`, `EcheancierEcolage`

#### **4. PedagogieController** (36 Ko - PRIORITÉ MOYENNE)
**Actions :**
- Déplacer vers `Matiere`, `EmploiDuTemps`, `Classe`

#### **5. ClassesController** (26 Ko - PRIORITÉ BASSE)
**Actions :**
- Déplacer vers `Classe`, `Inscription`, `Eleve`

---

## 🎯 Méthodologie

### **Étape 1 : Identifier les requêtes**
- Chercher tous les `$model->query()` dans le contrôleur
- Identifier les requêtes réutilisables

### **Étape 2 : Créer les méthodes dans les modèles**
- Nommer clairement : `getByClassePeriode()`, `getStatistiques()`, etc.
- Ajouter PHPDoc avec paramètres et retour
- Gérer les paramètres optionnels

### **Étape 3 : Mettre à jour le contrôleur**
- Remplacer `$model->query()` par `$model->methodeName()`
- Vérifier que la logique reste identique

### **Étape 4 : Tester et commiter**
- Vérifier que l'interface fonctionne
- Commit avec message descriptif

---

## 📝 Conventions de nommage

### **Méthodes de récupération :**
- `getAll()` : Tous les enregistrements
- `getBy[Critere]()` : Filtré par un critère
- `getAllWith[Relations]()` : Avec jointures
- `getStatistiques[Type]()` : Calculs agrégés

### **Méthodes de manipulation :**
- `create()`, `update()`, `delete()` : CRUD de base
- `persist[Action]()` : Opérations complexes

---

## ✅ Checklist par contrôleur

### NotesController
- [ ] ExamenFinal::getByClassePeriode()
- [ ] Interrogation::getByClassePeriode()
- [ ] ExamenFinal::getDetailsWithRelations()
- [ ] Interrogation::getDetailsWithRelations()
- [ ] ExamenFinal::getElevesWithNotes()
- [ ] Interrogation::getElevesWithNotes()
- [ ] Bulletin::getStatistiquesGlobales()
- [ ] Bulletin::getStatistiquesParClasse()
- [ ] Bulletin::getMoyennesEleves()

### InscriptionsController
- [ ] À analyser

### FinanceController
- [ ] À analyser

### PedagogieController
- [ ] À analyser

### ClassesController
- [ ] À analyser

---

## 🎁 Bénéfices attendus

1. **Réutilisabilité** : Méthodes disponibles pour tous les contrôleurs
2. **Testabilité** : Plus facile de tester les modèles isolément
3. **Maintenabilité** : Modifications SQL centralisées
4. **Lisibilité** : Contrôleurs plus courts et clairs
5. **Performance** : Possibilité d'optimiser les requêtes au même endroit

---

## 📅 Planning

- **Jour 1** : NotesController (3-4h)
- **Jour 2** : InscriptionsController (4-5h)
- **Jour 3** : FinanceController (3-4h)
- **Jour 4** : PedagogieController (2-3h)
- **Jour 5** : ClassesController + Révision (2-3h)

**Total estimé : 14-19 heures**
