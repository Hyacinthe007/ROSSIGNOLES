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
- Configuration ✅ (vient d'être fait)

## 🔴 À migrer MAINTENANT (utilisés par SystemeController)
- [ ] LogActivite.php
- [ ] Role.php
- [ ] UserGroup.php

## 🟡 À migrer ensuite (utilisés par d'autres contrôleurs)
- [ ] Personnel.php
- [ ] AnneeScolaire.php
- [ ] Niveau.php
- [ ] Serie.php
- [ ] Parent.php (ParentModel.php)
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
