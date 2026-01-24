# 🔄 MIGRATION VERS NAMESPACES PSR-4

**Date de début :** 24 janvier 2026  
**Temps estimé :** 6 heures  
**Priorité :** 🔴 CRITIQUE  

---

## 🎯 OBJECTIF

Migrer toutes les classes PHP du projet vers une structure PSR-4 avec namespaces, conformément aux standards modernes PHP.

---

## 📊 ÉTAT ACTUEL

```
❌ Toutes les classes sont dans le namespace global
❌ Autoloading via classmap (lent et non standard)
❌ Conflits de noms potentiels
❌ Impossible d'utiliser des packages modernes
```

---

## 🎯 ÉTAT CIBLE

```php
// Structure PSR-4
App\
├── Controllers\
│   ├── BaseController.php
│   ├── ElevesController.php
│   └── ...
├── Models\
│   ├── BaseModel.php
│   ├── Eleve.php
│   └── ...
├── Services\
├── Repositories\
├── Middleware\
├── Helpers\
└── Core\
```

---

## 📝 PLAN D'EXÉCUTION (6 étapes)

### ÉTAPE 1 : Mise à jour composer.json (15 min)
```json
{
    "autoload": {
        "psr-4": {
            "App\\": "app/"
        },
        "files": [
            "app/Helpers/functions.php"
        ]
    }
}
```

### ÉTAPE 2 : Migration des Models (90 min)
- Ajouter `namespace App\Models;` en haut
- Ajouter `use` statements pour les dépendances
- Mettre à jour les extends/implements

### ÉTAPE 3 : Migration des Controllers (90 min)
- Ajouter `namespace App\Controllers;`
- Importer les modèles avec `use App\Models\...`
- Mettre à jour BaseController

### ÉTAPE 4 : Migration Middleware, Services, Helpers (60 min)
- Middleware → `App\Middleware\`
- Services → `App\Services\`
- Core → `App\Core\`

### ÉTAPE 5 : Mise à jour des références (90 min)
- Routes (web.php, api.php)
- Index.php
- Vues (instanciations de classes)

### ÉTAPE 6 : Tests et validation (30 min)
- Régénérer autoloader
- Tester toutes les routes principales
- Vérifier les logs d'erreurs

---

## 🔧 COMMANDES À EXÉCUTER

```bash
# 1. Régénérer l'autoloader
composer dump-autoload

# 2. Vérifier qu'il n'y a pas d'erreurs
php -l app/Models/*.php
php -l app/Controllers/*.php

# 3. Tester l'application
# Naviguer vers http://localhost/ROSSIGNOLES
```

---

## ⚠️ POINTS D'ATTENTION

1. **Ordre de migration** : Models → Controllers → Middleware → Routes
2. **Helpers globaux** : Garder functions.php en autoload files
3. **Vues** : Mettre à jour les `new ClassName()` si nécessaire
4. **Git** : Commiter après chaque étape majeure

---

## 📋 CHECKLIST DE MIGRATION

### Models
- [ ] BaseModel.php
- [ ] Eleve.php
- [ ] Classe.php
- [ ] Inscription.php
- [ ] Facture.php
- [ ] Paiement.php
- [ ] Personnel.php
- [ ] User.php
- [ ] (+ tous les autres modèles)

### Controllers
- [ ] BaseController.php
- [ ] ElevesController.php
- [ ] ClassesController.php
- [ ] InscriptionsController.php
- [ ] FinanceController.php
- [ ] (+ tous les autres contrôleurs)

### Autres
- [ ] Router.php
- [ ] CsrfMiddleware.php
- [ ] Services (si existants)
- [ ] Routes (web.php, api.php)
- [ ] index.php

---

## 🎯 RÉSULTAT ATTENDU

Après migration :
```php
<?php
declare(strict_types=1);

namespace App\Models;

use PDO;
use PDOException;

class Eleve extends BaseModel
{
    protected string $table = 'eleves';
    // ...
}
```

---

## 📈 BÉNÉFICES IMMÉDIATS

✅ Conformité PSR-4  
✅ Autoloading optimisé  
✅ Pas de conflits de noms  
✅ Compatible avec packages modernes  
✅ IDE autocomplete amélioré  
✅ Préparation pour typage strict  

---

**Prêt à commencer ? On y va ! 🚀**
