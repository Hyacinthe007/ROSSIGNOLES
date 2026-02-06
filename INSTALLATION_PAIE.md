# 🎉 Module de Paie - Installation Complète

## ✅ Fichiers créés

### 📁 Modèles (app/Models/)
- ✅ `PaieParametreCotisation.php` - Gestion des taux de cotisations
- ✅ `PaieTrancheIrsa.php` - Gestion des tranches IRSA
- ✅ `PaieContrat.php` - Gestion des contrats
- ✅ `PaieBulletin.php` - Gestion des bulletins

### 📁 Services (app/Services/)
- ✅ `PaieService.php` - Logique métier complète

### 📁 Contrôleurs (app/Controllers/)
- ✅ `PaieController.php` - Gestion des requêtes HTTP

### 📁 Vues (app/Views/paie/)
- ✅ `index.php` - Page d'accueil du module
- ✅ `configuration.php` - Configuration des taux
- ✅ `contrats.php` - Liste des contrats
- ✅ `bulletins.php` - Liste des bulletins
- ✅ `bulletin_detail.php` - Détail d'un bulletin

### 📁 Base de données
- ✅ `database/migrations/2026_02_03_correction_paie.sql` - Migration BDD
- ✅ `database/migrations/2026_02_03_permissions_paie.sql` - Permissions
- ✅ `database/init_paie.php` - Script d'initialisation

### 📁 Documentation
- ✅ `docs/MODULE_PAIE.md` - Documentation complète
- ✅ `install_paie.bat` - Script d'installation automatique

### 📁 Routes
- ✅ Routes ajoutées dans `routes/web.php`
- ✅ Lien ajouté dans `app/Views/layout/sidebar.php`

---

## 🚀 Installation

### Option 1 : Installation Automatique (Recommandée)

Exécutez simplement :
```bash
install_paie.bat
```

### Option 2 : Installation Manuelle

#### Étape 1 : Migration de la base de données
```bash
mysql -u root rossignoles < database/migrations/2026_02_03_correction_paie.sql
```

#### Étape 2 : Ajout des permissions
```bash
mysql -u root rossignoles < database/migrations/2026_02_03_permissions_paie.sql
```

#### Étape 3 : Initialisation des données
```bash
php database/init_paie.php
```

---

## 📋 Vérification

Après l'installation :

1. ✅ Connectez-vous en tant qu'administrateur
2. ✅ Le lien **"Paie du personnel"** doit apparaître dans le menu **Finance**
3. ✅ Accédez à : `http://localhost/ROSSIGNOLES/paie`

---

## 🎯 Fonctionnalités Disponibles

### 1. Configuration
- Paramétrage des taux de cotisations (CNAPS, OSTIE, FMFP)
- Visualisation des tranches IRSA 2026

### 2. Gestion des Contrats
- Création/modification des contrats de paie
- Définition du salaire brut de base
- Option de soumission aux cotisations
- Gestion du nombre d'enfants à charge

### 3. Bulletins de Paie
- Génération automatique des bulletins mensuels
- Calcul automatique de :
  - IRSA progressif avec réduction par enfant
  - Cotisations sociales (CNAPS, OSTIE)
  - Charges patronales (CNAPS, OSTIE, FMFP)
  - Salaire net et coût employeur
- Validation des bulletins
- Impression des bulletins

---

## 🔐 Permissions

Les permissions suivantes ont été créées :

| Code | Description |
|------|-------------|
| `paie.read` | Consulter la paie et les bulletins |
| `paie.create` | Créer des bulletins de paie |
| `paie.update` | Modifier la configuration de paie |
| `paie.validate` | Valider des bulletins de paie |
| `paie.delete` | Supprimer des bulletins (brouillon) |

Par défaut, le groupe **Administrateur** a toutes les permissions.

---

## 📊 Structure de la Base de Données

### Tables créées :

1. **`paie_parametres_cotisations`**
   - Stocke les taux de cotisations sociales
   - CNAPS, OSTIE, FMFP

2. **`paie_tranches_irsa`**
   - Grille progressive de l'IRSA 2026
   - 6 tranches de 0% à 25%

3. **`paie_contrats`**
   - Contrats et salaires du personnel
   - Salaire brut de base
   - Option de soumission aux cotisations

4. **`paie_bulletins`**
   - Bulletins de paie mensuels
   - Détails complets du calcul
   - Historique des paiements

5. **`paie_retenues_diverses`**
   - Autres retenues (avances, cantine, etc.)

### Tables supprimées (doublons) :
- ❌ `fiches_paie`
- ❌ `salaires_personnels`

---

## 🧮 Logique de Calcul

### 1. Base Imposable IRSA
```
SI type_contrat = 'CDI' OU (type_contrat = 'CDD' ET soumis_cotisations = true) :
    Base imposable = Salaire brut - CNAPS (1%) - OSTIE (1%)
SINON (Stagiaire/Intérimaire) :
    Base imposable = Salaire brut
```

### 2. IRSA Brut (Progressif)
Application des tranches progressives :
- 0 – 350 000 Ar : 0%
- 350 001 – 400 000 Ar : 5%
- 400 001 – 500 000 Ar : 10%
- 500 001 – 600 000 Ar : 15%
- 600 001 – 4 000 000 Ar : 20%
- 4 000 001 et plus : 25%

### 3. Réduction et IRSA Net
```
Réduction = nb_enfants × 2 000 Ar
IRSA net = MAX(3 000 Ar, IRSA brut - Réduction)
```

### 4. Salaire Net
```
Salaire net = Salaire brut 
            - CNAPS salarial 
            - OSTIE salarial 
            - IRSA net 
            - Autres retenues
```

### 5. Coût Employeur
```
Coût total = Salaire brut 
           + CNAPS patronal (13%) 
           + OSTIE patronal (5%) 
           + FMFP patronal (1%)
```

---

## 🎓 Utilisation

### Workflow Recommandé

1. **Configuration initiale**
   - Vérifier les taux de cotisations dans `/paie/configuration`
   - Les valeurs par défaut sont conformes à la législation 2026

2. **Création des contrats**
   - Aller dans `/paie/contrats`
   - Créer un contrat pour chaque membre du personnel
   - Définir le salaire brut et le nombre d'enfants

3. **Génération des bulletins**
   - Aller dans `/paie/bulletins`
   - Sélectionner la période (mois)
   - Cliquer sur "Générer les bulletins du mois"

4. **Validation et impression**
   - Vérifier les bulletins générés
   - Valider les bulletins corrects
   - Imprimer ou exporter si nécessaire

---

## ⚠️ Points d'Attention

1. **Sécurité** : Les bulletins validés ne peuvent plus être modifiés
2. **Audit** : Toute modification de taux doit être tracée
3. **Archivage** : Conserver l'historique des tranches IRSA par année
4. **Validation** : Vérifier les calculs avec un expert-comptable

---

## 🔄 Prochaines Améliorations

### À court terme :
- [ ] Export PDF des bulletins
- [ ] Gestion des retenues diverses
- [ ] Formulaire de création de contrat

### À moyen terme :
- [ ] Tableau de bord avec statistiques
- [ ] Historique des modifications de taux
- [ ] Export Excel des bulletins
- [ ] Envoi par email des bulletins

### À long terme :
- [ ] Intégration avec la comptabilité
- [ ] Génération automatique des déclarations CNAPS/OSTIE
- [ ] Gestion des primes et indemnités

---

## 📞 Support

Pour toute question sur le module de paie :
- Consulter la documentation : `docs/MODULE_PAIE.md`
- Vérifier les logs dans `logs_activites`
- Contacter l'équipe de développement

---

**Date de création** : 2026-02-03  
**Version** : 1.0  
**Auteur** : Système ERP ROSSIGNOLES  
**Statut** : ✅ Opérationnel
