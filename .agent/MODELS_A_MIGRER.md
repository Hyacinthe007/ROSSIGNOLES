# 🚨 STATUT DE LA MIGRATION PSR-4

## ✅ Déjà migrés (App\Models)
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
- PersonnelEnseignant
- PersonnelAdministratif
- Absence
- Document
- AbsencePersonnel
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
- EmploisTemps
- EnseignantsClasses
- MatieresSeries
- MatieresNiveaux
- MatieresClasses
- CalendrierScolaire
- ConseilClasse
- DecisionConseil

## 🟡 À migrer prochainement
- [ ] InscriptionArticle.php
- [ ] EcheancierEcolage.php
- [ ] Cycle.php
- [ ] TypeFrais.php
- [ ] TarifArticle.php (doublon ?)
- [ ] ... tous les autres fichiers dans app/Models/

## 📝 Stratégie
1. Migration à la demande (lorsqu'une erreur survient)
2. Validation immédiate par test runtime
3. Nettoyage des `require_once` dans les contrôleurs associés
