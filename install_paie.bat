@echo off
REM Script d'installation du module de paie
REM Exécute toutes les migrations et initialisations nécessaires

echo ========================================
echo Installation du Module de Paie
echo ========================================
echo.

REM 1. Migration de la structure BDD
echo [1/3] Execution de la migration de la base de donnees...
mysql -u root rossignoles < database\migrations\2026_02_03_correction_paie.sql
if %errorlevel% neq 0 (
    echo ERREUR lors de la migration de la base de donnees !
    pause
    exit /b 1
)
echo      OK - Structure de la base de donnees mise a jour
echo.

REM 2. Ajout des permissions
echo [2/3] Ajout des permissions...
mysql -u root rossignoles < database\migrations\2026_02_03_permissions_paie.sql
if %errorlevel% neq 0 (
    echo ERREUR lors de l'ajout des permissions !
    pause
    exit /b 1
)
echo      OK - Permissions ajoutees
echo.

REM 3. Initialisation des donnees par defaut
echo [3/3] Initialisation des donnees par defaut...
php database\init_paie.php
if %errorlevel% neq 0 (
    echo ERREUR lors de l'initialisation !
    pause
    exit /b 1
)
echo.

echo ========================================
echo Installation terminee avec succes !
echo ========================================
echo.
echo Le module de paie est maintenant disponible :
echo http://localhost/ROSSIGNOLES/paie
echo.
echo Le lien "Paie du personnel" est visible dans le menu Finance
echo (pour les utilisateurs ayant la permission 'paie.read')
echo.
pause
