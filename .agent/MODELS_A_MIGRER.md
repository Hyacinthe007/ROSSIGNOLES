# 🚨 MODÈLES À MIGRER D'URGENCE

Basé sur l'erreur actuelle, voici les modèles qui doivent être migrés immédiatement :

## ✅ Déjà migrés
- BaseModel
- User  
- Eleve
- Classe
- Inscription
- Facture
- Paiement
- Configuration
- LogActivite
- Role
- UserGroup
- Personnel
- AnneeScolaire
- Permission

- Niveau
- Serie
- ParentModel

- Bulletin
- Note
- Matiere
- TypeFacture
- LigneFacture
- ModePaiement
- Article
- TarifArticle
- DocumentsInscription
- TarifInscription

## 🟡 À migrer ensuite
- [ ] InscriptionArticle.php
- [ ] EcheancierEcolage.php
- [ ] Cycle.php
- [ ] TypeFrais.php
- [ ] TarifArticle.php

## 📝 Stratégie
1. Migrer les 3 modèles critiques (LogActivite, Role, UserGroup)
2. Tester l'application
3. Migrer le reste par batch de 5
