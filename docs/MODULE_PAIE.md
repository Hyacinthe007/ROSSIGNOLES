# Module de Gestion de la Paie - ROSSIGNOLES

## 📋 Vue d'ensemble

Le module de paie a été mis en place pour gérer le calcul automatique des salaires selon la législation malgache 2026, incluant :
- Calcul de l'IRSA (Impôt sur les Revenus Salariaux et Assimilés)
- Cotisations sociales (CNAPS, OSTIE, FMFP)
- Génération de bulletins de paie
- Gestion des contrats de personnel

## 🗂️ Structure de la base de données

### Tables créées :

1. **`paie_parametres_cotisations`** - Taux de cotisations sociales
   - CNAPS (salarial 1%, patronal 13%)
   - OSTIE (salarial 1%, patronal 5%)
   - FMFP (salarial 0%, patronal 1%)

2. **`paie_tranches_irsa`** - Grille progressive de l'IRSA 2026
   - 6 tranches de 0% à 25%
   - Minimum de perception : 3 000 Ar

3. **`paie_contrats`** - Contrats et salaires du personnel
   - Salaire brut de base
   - Option de soumission aux cotisations

4. **`paie_bulletins`** - Bulletins de paie mensuels
   - Détails complets du calcul
   - Historique des paiements

5. **`paie_retenues_diverses`** - Autres retenues (avances, cantine, etc.)

### Tables supprimées (doublons) :
- ❌ `fiches_paie`
- ❌ `salaires_personnels`

## 📁 Fichiers créés

### Modèles (app/Models/)
- `PaieParametreCotisation.php` - Gestion des taux de cotisations
- `PaieTrancheIrsa.php` - Gestion des tranches IRSA + calcul
- `PaieContrat.php` - Gestion des contrats
- `PaieBulletin.php` - Gestion des bulletins de paie

### Services (app/Services/)
- `PaieService.php` - Logique métier complète du calcul de paie

### Contrôleurs (app/Controllers/)
- `PaieController.php` - Gestion des requêtes HTTP

### Vues (app/Views/paie/)
- `index.php` - Page d'accueil du module

### Scripts
- `database/migrations/2026_02_03_correction_paie.sql` - Migration BDD
- `database/init_paie.php` - Script d'initialisation

## 🚀 Installation et Configuration

### Étape 1 : Exécuter la migration SQL
```bash
# Via MySQL
mysql -u root rossignoles < database/migrations/2026_02_03_correction_paie.sql

# OU via phpMyAdmin
# Importer le fichier SQL dans phpMyAdmin
```

### Étape 2 : Initialiser les données par défaut
```bash
php database/init_paie.php
```

Cette commande va :
- ✓ Créer les 3 paramètres de cotisations (CNAPS, OSTIE, FMFP)
- ✓ Créer les 6 tranches IRSA 2026

### Étape 3 : Accéder au module
URL : `http://localhost/ROSSIGNOLES/paie`

## 📊 Routes disponibles

| Route | Méthode | Description |
|-------|---------|-------------|
| `/paie` | GET | Page d'accueil |
| `/paie/configuration` | GET | Configuration des taux |
| `/paie/configuration/update` | POST | Mise à jour des taux |
| `/paie/contrats` | GET | Liste des contrats |
| `/paie/contrats/form` | GET | Formulaire contrat |
| `/paie/contrats/save` | POST | Enregistrer un contrat |
| `/paie/bulletins` | GET | Liste des bulletins |
| `/paie/bulletins/generer` | POST | Générer les bulletins |
| `/paie/bulletins/detail` | GET | Détail d'un bulletin |
| `/paie/bulletins/valider` | POST | Valider un bulletin |

## 🧮 Logique de calcul

### 1. Détermination de la base imposable IRSA

```
SI type_contrat = 'CDI' OU (type_contrat = 'CDD' ET soumis_cotisations = true) :
    Base imposable = Salaire brut - CNAPS (1%) - OSTIE (1%)
SINON (Stagiaire/Intérimaire) :
    Base imposable = Salaire brut
```

### 2. Calcul de l'IRSA brut (progressif)

Le système parcourt les tranches et applique le taux correspondant :
```
Exemple : Salaire de 800 000 Ar
- 0 à 350 000 : 0% = 0 Ar
- 350 001 à 400 000 : 5% = 2 500 Ar
- 400 001 à 500 000 : 10% = 10 000 Ar
- 500 001 à 600 000 : 15% = 15 000 Ar
- 600 001 à 800 000 : 20% = 40 000 Ar
Total IRSA brut = 67 500 Ar
```

### 3. Application de la réduction

```
Réduction = nb_enfants × 2 000 Ar
IRSA net = MAX(3 000 Ar, IRSA brut - Réduction)
```

### 4. Calcul du salaire net

```
Salaire net = Salaire brut 
            - CNAPS salarial 
            - OSTIE salarial 
            - IRSA net 
            - Autres retenues
```

### 5. Calcul du coût employeur

```
Coût total = Salaire brut 
           + CNAPS patronal (13%) 
           + OSTIE patronal (5%) 
           + FMFP patronal (1%)
```

## 🔧 Prochaines étapes recommandées

### À court terme :
1. ✅ Créer les vues manquantes :
   - `paie/configuration.php` - Interface de configuration
   - `paie/contrats.php` - Liste des contrats
   - `paie/bulletins.php` - Liste des bulletins
   - `paie/bulletin_detail.php` - Détail d'un bulletin

2. ✅ Ajouter les permissions dans la table `permissions` :
   ```sql
   INSERT INTO permissions (code, module, action, description) VALUES
   ('paie.read', 'paie', 'read', 'Consulter la paie'),
   ('paie.create', 'paie', 'create', 'Créer des bulletins'),
   ('paie.update', 'paie', 'update', 'Modifier la configuration'),
   ('paie.validate', 'paie', 'validate', 'Valider des bulletins');
   ```

3. ✅ Créer un lien dans le menu (sidebar) :
   ```html
   <a href="/paie" class="nav-link">
       <i class="fas fa-money-bill-wave"></i>
       <span>Paie</span>
   </a>
   ```

### À moyen terme :
4. Ajouter l'export PDF des bulletins
5. Créer un tableau de bord avec statistiques
6. Implémenter la gestion des retenues diverses
7. Ajouter un historique des modifications de taux

## ⚠️ Points d'attention

1. **Sécurité** : Les bulletins validés ne doivent plus être modifiables
2. **Audit** : Toute modification de taux doit être tracée
3. **Archivage** : Conserver l'historique des tranches IRSA par année
4. **Validation** : Vérifier les calculs avec un expert-comptable

## 📞 Support

Pour toute question sur le module de paie :
- Consulter ce document
- Vérifier les logs dans `logs_activites`
- Contacter l'équipe de développement

---

**Date de création** : 2026-02-03  
**Version** : 1.0  
**Auteur** : Système ERP ROSSIGNOLES
