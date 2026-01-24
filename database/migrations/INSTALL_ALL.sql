-- ============================================================================
-- SCRIPT D'INSTALLATION RAPIDE
-- Description : Installe toutes les fonctionnalités en une seule commande
-- Date : 2026-01-21
-- ============================================================================

-- IMPORTANT : Exécutez ce script depuis MySQL avec :
-- mysql -u root -p abonnements_transport < d:/WEB/htdocs/ROSSIGNOLES/database/migrations/INSTALL_ALL.sql

USE abonnements_transport;

SELECT '========================================' as message;
SELECT '🚀 INSTALLATION DU SYSTÈME DE NOTES' as message;
SELECT '========================================' as message;
SELECT '' as message;

-- ============================================================================
-- ÉTAPE 1 : Vérification des prérequis
-- ============================================================================

SELECT '📋 ÉTAPE 1/4 : Vérification des prérequis...' as message;

-- Vérifier que la table bulletins_annuels existe
SET @table_exists = (
    SELECT COUNT(*) 
    FROM information_schema.TABLES 
    WHERE TABLE_SCHEMA = DATABASE() 
      AND TABLE_NAME = 'bulletins_annuels'
);

SELECT 
    CASE 
        WHEN @table_exists > 0 THEN '✅ Table bulletins_annuels existe'
        ELSE '❌ ERREUR : Table bulletins_annuels manquante'
    END as status;

-- ============================================================================
-- ÉTAPE 2 : Installation des procédures et fonctions
-- ============================================================================

SELECT '' as message;
SELECT '📦 ÉTAPE 2/4 : Installation des procédures et fonctions...' as message;

-- Supprimer les anciennes versions si elles existent
DROP PROCEDURE IF EXISTS verifier_ecolage_eleve;
DROP FUNCTION IF EXISTS calculer_moyenne_bulletin;
DROP PROCEDURE IF EXISTS generer_bulletin_annuel;

DELIMITER $$

-- Procédure : verifier_ecolage_eleve
CREATE PROCEDURE verifier_ecolage_eleve(
    IN p_eleve_id BIGINT,
    IN p_annee_id BIGINT,
    IN p_mois TINYINT,
    OUT p_peut_passer TINYINT,
    OUT p_message VARCHAR(500)
)
BEGIN
    DECLARE v_inscription_validee TINYINT DEFAULT 0;
    DECLARE v_inscription_bloquee TINYINT DEFAULT 1;
    DECLARE v_peut_suivre_cours TINYINT DEFAULT 0;
    DECLARE v_statut_ecolage VARCHAR(50);
    DECLARE v_montant_restant DECIMAL(10,2);
    DECLARE v_nom_eleve VARCHAR(200);
    
    SET p_peut_passer = 0;
    SET p_message = '';
    
    SELECT CONCAT(nom, ' ', prenom) INTO v_nom_eleve
    FROM eleves WHERE id = p_eleve_id;
    
    SELECT 
        CASE WHEN statut = 'validee' THEN 1 ELSE 0 END,
        bloquee
    INTO v_inscription_validee, v_inscription_bloquee
    FROM inscriptions
    WHERE eleve_id = p_eleve_id AND annee_scolaire_id = p_annee_id
    LIMIT 1;
    
    IF v_inscription_validee = 0 THEN
        SET p_peut_passer = 0;
        SET p_message = CONCAT('❌ ', v_nom_eleve, ' : Inscription non validée.');
        LEAVE verifier_ecolage_eleve;
    END IF;
    
    IF v_inscription_bloquee = 1 THEN
        SET p_peut_passer = 0;
        SET p_message = CONCAT('❌ ', v_nom_eleve, ' : Inscription bloquée. Paiement initial requis.');
        LEAVE verifier_ecolage_eleve;
    END IF;
    
    SELECT 
        COALESCE(peut_suivre_cours, 1),
        statut,
        COALESCE(montant_restant, 0)
    INTO v_peut_suivre_cours, v_statut_ecolage, v_montant_restant
    FROM (
        SELECT see.peut_suivre_cours, ech.statut, ech.montant_restant
        FROM statuts_eleves_ecolage see
        INNER JOIN echeanciers_ecolages ech ON (
            ech.eleve_id = see.eleve_id
            AND ech.annee_scolaire_id = see.annee_scolaire_id
            AND ech.mois = see.mois
            AND ech.annee = see.annee
        )
        WHERE see.eleve_id = p_eleve_id
          AND see.annee_scolaire_id = p_annee_id
          AND see.mois = p_mois
          AND see.annee = YEAR(CURDATE())
        LIMIT 1
    ) AS statut_data;
    
    IF v_peut_suivre_cours = 0 THEN
        SET p_peut_passer = 0;
        SET p_message = CONCAT('❌ ', v_nom_eleve, ' : Exclu pour impayé. Montant dû : ', 
                              CAST(v_montant_restant AS CHAR), ' Ar.');
        LEAVE verifier_ecolage_eleve;
    END IF;
    
    IF v_statut_ecolage IN ('retard', 'partiellement_paye') THEN
        SET p_peut_passer = 0;
        SET p_message = CONCAT('⚠️ ', v_nom_eleve, ' : Retard de paiement. Montant : ', 
                              CAST(v_montant_restant AS CHAR), ' Ar.');
        LEAVE verifier_ecolage_eleve;
    END IF;
    
    SET p_peut_passer = 1;
    SET p_message = CONCAT('✅ ', v_nom_eleve, ' : Autorisé.');
END$$

-- Fonction : calculer_moyenne_bulletin
CREATE FUNCTION calculer_moyenne_bulletin(
    p_moyenne_interro DECIMAL(5,2),
    p_note_examen DECIMAL(5,2)
)
RETURNS DECIMAL(5,2)
DETERMINISTIC
BEGIN
    DECLARE v_note_bulletin DECIMAL(5,2);
    
    IF p_moyenne_interro IS NOT NULL AND p_note_examen IS NOT NULL THEN
        SET v_note_bulletin = (p_moyenne_interro + (p_note_examen * 2)) / 3;
    ELSEIF p_moyenne_interro IS NOT NULL AND p_note_examen IS NULL THEN
        SET v_note_bulletin = p_moyenne_interro;
    ELSEIF p_moyenne_interro IS NULL AND p_note_examen IS NOT NULL THEN
        SET v_note_bulletin = p_note_examen;
    ELSE
        SET v_note_bulletin = NULL;
    END IF;
    
    RETURN ROUND(v_note_bulletin, 2);
END$$

-- Procédure : generer_bulletin_annuel
CREATE PROCEDURE generer_bulletin_annuel(
    IN p_eleve_id BIGINT,
    IN p_annee_id BIGINT
)
BEGIN
    DECLARE v_moyenne_annuelle DECIMAL(5,2);
    DECLARE v_rang_annuel INT;
    DECLARE v_decision_finale VARCHAR(50);
    DECLARE v_bloque_par_impaye TINYINT DEFAULT 0;
    DECLARE v_total_impaye DECIMAL(10,2);
    DECLARE v_nb_bulletins_valides INT;
    
    SELECT ROUND(AVG(moyenne_generale), 2)
    INTO v_moyenne_annuelle
    FROM bulletins
    WHERE eleve_id = p_eleve_id
      AND annee_scolaire_id = p_annee_id
      AND statut = 'valide';
    
    SELECT COUNT(*)
    INTO v_nb_bulletins_valides
    FROM bulletins
    WHERE eleve_id = p_eleve_id
      AND annee_scolaire_id = p_annee_id
      AND statut = 'valide';
    
    SELECT COALESCE(SUM(montant_restant), 0)
    INTO v_total_impaye
    FROM echeanciers_ecolages
    WHERE eleve_id = p_eleve_id
      AND annee_scolaire_id = p_annee_id
      AND statut IN ('retard', 'partiellement_paye', 'impaye_exclu', 'exclusion');
    
    IF v_total_impaye > 0 THEN
        SET v_bloque_par_impaye = 1;
    END IF;
    
    IF v_nb_bulletins_valides >= 3 AND v_bloque_par_impaye = 0 THEN
        IF v_moyenne_annuelle >= 10 THEN
            SET v_decision_finale = 'admis';
        ELSE
            SET v_decision_finale = 'redouble';
        END IF;
    ELSE
        SET v_decision_finale = 'en_attente';
    END IF;
    
    SELECT COUNT(*) + 1
    INTO v_rang_annuel
    FROM (
        SELECT b.eleve_id, AVG(b.moyenne_generale) as moy_annuelle
        FROM bulletins b
        INNER JOIN inscriptions i ON b.eleve_id = i.eleve_id AND b.annee_scolaire_id = i.annee_scolaire_id
        WHERE b.annee_scolaire_id = p_annee_id
          AND i.classe_id = (SELECT classe_id FROM inscriptions WHERE eleve_id = p_eleve_id AND annee_scolaire_id = p_annee_id)
          AND b.statut = 'valide'
        GROUP BY b.eleve_id
        HAVING AVG(b.moyenne_generale) > v_moyenne_annuelle
    ) AS classement;
    
    INSERT INTO bulletins_annuels (
        eleve_id, annee_scolaire_id, moyenne_annuelle, rang_annuel,
        decision_finale, bloque_par_impaye, date_generation
    )
    VALUES (
        p_eleve_id, p_annee_id, v_moyenne_annuelle, v_rang_annuel,
        v_decision_finale, v_bloque_par_impaye, NOW()
    )
    ON DUPLICATE KEY UPDATE
        moyenne_annuelle = v_moyenne_annuelle,
        rang_annuel = v_rang_annuel,
        decision_finale = v_decision_finale,
        bloque_par_impaye = v_bloque_par_impaye,
        date_generation = NOW();
END$$

DELIMITER ;

SELECT '✅ Procédures et fonctions installées' as status;

-- ============================================================================
-- ÉTAPE 3 : Tests de validation
-- ============================================================================

SELECT '' as message;
SELECT '🧪 ÉTAPE 3/4 : Tests de validation...' as message;

-- Test fonction calculer_moyenne_bulletin
SELECT 
    'Test calcul moyenne' as test,
    calculer_moyenne_bulletin(13.00, 15.00) as resultat,
    '14.33' as attendu,
    CASE 
        WHEN ABS(calculer_moyenne_bulletin(13.00, 15.00) - 14.33) < 0.01 THEN '✅ PASS'
        ELSE '❌ FAIL'
    END as status;

-- Vérifier les objets créés
SELECT 
    'Objets SQL créés' as test,
    CONCAT(
        (SELECT COUNT(*) FROM information_schema.ROUTINES 
         WHERE ROUTINE_NAME = 'verifier_ecolage_eleve' AND ROUTINE_TYPE = 'PROCEDURE'),
        ' + ',
        (SELECT COUNT(*) FROM information_schema.ROUTINES 
         WHERE ROUTINE_NAME = 'calculer_moyenne_bulletin' AND ROUTINE_TYPE = 'FUNCTION'),
        ' + ',
        (SELECT COUNT(*) FROM information_schema.ROUTINES 
         WHERE ROUTINE_NAME = 'generer_bulletin_annuel' AND ROUTINE_TYPE = 'PROCEDURE')
    ) as resultat,
    '3 objets' as attendu,
    CASE 
        WHEN (SELECT COUNT(*) FROM information_schema.ROUTINES 
              WHERE ROUTINE_NAME IN ('verifier_ecolage_eleve', 'calculer_moyenne_bulletin', 'generer_bulletin_annuel')) = 3
        THEN '✅ PASS'
        ELSE '❌ FAIL'
    END as status;

-- ============================================================================
-- ÉTAPE 4 : Résumé final
-- ============================================================================

SELECT '' as message;
SELECT '📊 ÉTAPE 4/4 : Résumé de l\'installation' as message;
SELECT '========================================' as message;

SELECT 
    '✅ Installation terminée avec succès !' as message
UNION ALL
SELECT 
    CONCAT('Procédures créées : ', 
           (SELECT COUNT(*) FROM information_schema.ROUTINES 
            WHERE ROUTINE_TYPE = 'PROCEDURE' 
            AND ROUTINE_NAME IN ('verifier_ecolage_eleve', 'generer_bulletin_annuel')))
UNION ALL
SELECT 
    CONCAT('Fonctions créées : ', 
           (SELECT COUNT(*) FROM information_schema.ROUTINES 
            WHERE ROUTINE_TYPE = 'FUNCTION' 
            AND ROUTINE_NAME = 'calculer_moyenne_bulletin'))
UNION ALL
SELECT '========================================'
UNION ALL
SELECT '📚 Prochaines étapes :'
UNION ALL
SELECT '1. Consulter : database/GUIDE_IMPLEMENTATION.md'
UNION ALL
SELECT '2. Tester : database/migrations/TEST_PHASE_2_COMPLET.sql'
UNION ALL
SELECT '3. Implémenter les interfaces utilisateur'
UNION ALL
SELECT '========================================';

-- ============================================================================
-- FIN DE L'INSTALLATION
-- ============================================================================
