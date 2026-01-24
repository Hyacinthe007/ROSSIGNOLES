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

## 🟡 À migrer ensuite
- [ ] Niveau.php
- [ ] Serie.php
- [ ] ParentModel.php
- [ ] Bulletin.php
- [ ] Note.php
- [ ] Matiere.php
- [ ] ModePaiement.php
- [ ] TypeFacture.php
- [ ] LigneFacture.php
- [ ] TarifInscription.php
- [ ] Article.php
- [ ] InscriptionArticle.php
- [ ] DocumentsInscription.php
- [ ] EcheancierEcolage.php
- [ ] Cycle.php
- [ ] TypeFrais.php
- [ ] TarifArticle.php
- [ ] Permission.php

## 📝 Stratégie
1. Migrer les 3 modèles critiques (LogActivite, Role, UserGroup)
2. Tester l'application
3. Migrer le reste par batch de 5
