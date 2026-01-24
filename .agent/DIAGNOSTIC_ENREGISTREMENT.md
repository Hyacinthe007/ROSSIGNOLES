# 🔍 Diagnostic du Problème d'Enregistrement

## Étapes de diagnostic

### 1️⃣ Vérifier les logs d'erreur

**Accédez à** : `http://localhost/ROSSIGNOLES/view_logs.php`

Ce script affichera les dernières erreurs PHP. Recherchez :
- ❌ Messages d'erreur en rouge
- 🟡 Warnings en orange
- 🔵 Messages de debug (=== DÉBUT ENREGISTREMENT PERSONNEL ===)

---

### 2️⃣ Tester l'enregistrement directement

**Accédez à** : `http://localhost/ROSSIGNOLES/test_personnel.php`

Ce script teste l'enregistrement sans passer par le formulaire.
- Si ça fonctionne ✅ : Le problème vient du formulaire ou de la session
- Si ça échoue ❌ : Le problème vient du modèle ou de la base de données

---

### 3️⃣ Tester le formulaire étape par étape

1. **Ouvrez la console du navigateur** (F12)
2. Allez sur `http://localhost/ROSSIGNOLES/personnel/nouveau?etape=1`
3. Sélectionnez "Enseignant"
4. Remplissez le formulaire à l'étape 2
5. **AVANT de cliquer sur Enregistrer**, vérifiez dans la console :
   - Y a-t-il des erreurs JavaScript ?
   - Le champ caché `specialite` a-t-il une valeur ?

6. Cliquez sur "Enregistrer"
7. Regardez dans la console :
   - La requête POST est-elle envoyée ?
   - Quel est le code de réponse (200, 302, 500) ?

---

### 4️⃣ Vérifier la session

Ajoutez ce code temporairement au début de `etape2_formulaire.php` (ligne 1) :

```php
<?php
echo "<div style='background: yellow; padding: 10px; margin: 10px;'>";
echo "<h3>DEBUG SESSION</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";
echo "</div>";
?>
```

Rechargez la page et vérifiez si `$_SESSION['personnel_data']['type_personnel']` existe.

---

### 5️⃣ Vérifier la base de données

Exécutez cette requête SQL dans phpMyAdmin :

```sql
-- Vérifier les derniers personnels créés
SELECT * FROM personnels ORDER BY created_at DESC LIMIT 5;

-- Vérifier la structure de la table
DESCRIBE personnels;

-- Vérifier les contraintes
SHOW CREATE TABLE personnels_enseignants;
SHOW CREATE TABLE personnels_administratifs;
```

---

## Problèmes courants et solutions

### ❌ Erreur : "Session expirée"
**Cause** : La session `personnel_data` n'existe pas
**Solution** : Recommencez depuis l'étape 1

### ❌ Erreur : "Column 'xxx' cannot be null"
**Cause** : Un champ obligatoire n'est pas rempli
**Solution** : Vérifiez que tous les champs requis sont bien envoyés dans le POST

### ❌ Erreur : "Duplicate entry for key 'matricule'"
**Cause** : Le matricule existe déjà
**Solution** : Vérifiez la fonction `genererMatricule()`

### ❌ Erreur : "Cannot add foreign key constraint"
**Cause** : Le `poste_id` n'existe pas dans `postes_administratifs`
**Solution** : Déjà corrigé - le poste est maintenant optionnel

### ❌ Rien ne se passe, pas d'erreur
**Cause possible** :
1. JavaScript bloque la soumission
2. Validation HTML5 échoue silencieusement
3. Redirection immédiate sans traitement

**Solution** : Vérifiez la console du navigateur

---

## Que faire maintenant ?

1. ✅ Accédez à `http://localhost/ROSSIGNOLES/view_logs.php`
2. ✅ Essayez d'enregistrer un nouveau personnel
3. ✅ Rafraîchissez la page des logs
4. ✅ **Copiez-moi les messages d'erreur** que vous voyez

Avec ces informations, je pourrai identifier exactement où se situe le problème !
