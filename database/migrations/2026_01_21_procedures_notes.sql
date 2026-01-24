-- ============================================================================
-- Migration : Procédures stockées pour gestion des notes et bulletins
-- Date : 2026-01-21
-- Description : Crée les procédures et fonctions pour :
--               - Vérifier l'éligibilité d'un élève aux évaluations
--               - Calculer automatiquement les moyennes de bulletin
--               - Gérer les bulletins annuels
-- ============================================================================

USE abonnements_transport;

-- ============================================================================
-- PROCÉDURE 1 : verifier_ecolage_eleve
-- Description : Vérifie si un élève peut passer une évaluation
-- Paramètres :
--   - p_eleve_id : ID de l'élève
--   - p_annee_id : ID de l'année scolaire
--   - p_mois : Mois de vérification (1-12)
-- Sorties :
--   - @peut_passer : 1 si autorisé, 0 si bloqué
--   - @message : Message explicatif
-- ============================================================================

DROP PROCEDURE IF EXISTS verifier_ecolage_eleve;

DELIMITER $$

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
    
    -- Initialiser les valeurs par défaut
    SET p_peut_passer = 0;
    SET p_message = '';
    
    -- Récupérer le nom de l'élève
    SELECT CONCAT(nom, ' ', prenom) INTO v_nom_eleve
    FROM eleves
    WHERE id = p_eleve_id;
    
    -- Vérifier l'inscription
    SELECT 
        CASE WHEN statut = 'validee' THEN 1 ELSE 0 END,
        bloquee
    INTO v_inscription_validee, v_inscription_bloquee
    FROM inscriptions
    WHERE eleve_id = p_eleve_id 
      AND annee_scolaire_id = p_annee_id
    LIMIT 1;
    
    -- Test 1 : Inscription non validée
    IF v_inscription_validee = 0 THEN
        SET p_peut_passer = 0;
        SET p_message = CONCAT('❌ ', v_nom_eleve, ' : Inscription non validée. Veuillez finaliser le dossier d\'inscription.');
        LEAVE verifier_ecolage_eleve;
    END IF;
    
    -- Test 2 : Inscription bloquée (frais d'inscription ou 1er mois impayé)
    IF v_inscription_bloquee = 1 THEN
        SET p_peut_passer = 0;
        SET p_message = CONCAT('❌ ', v_nom_eleve, ' : Inscription bloquée. Les frais d\'inscription et le premier mois d\'écolage doivent être payés.');
        LEAVE verifier_ecolage_eleve;
    END IF;
    
    -- Test 3 : Vérifier le statut de l'écolage du mois
    SELECT 
        COALESCE(peut_suivre_cours, 1),
        statut,
        COALESCE(montant_restant, 0)
    INTO v_peut_suivre_cours, v_statut_ecolage, v_montant_restant
    FROM (
        SELECT 
            see.peut_suivre_cours,
            ech.statut,
            ech.montant_restant
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
    
    -- Test 4 : Élève exclu pour impayé
    IF v_peut_suivre_cours = 0 THEN
        SET p_peut_passer = 0;
        SET p_message = CONCAT('❌ ', v_nom_eleve, ' : Exclu pour impayé. Montant dû : ', 
                              CAST(v_montant_restant AS CHAR), ' Ar. Veuillez régulariser la situation.');
        LEAVE verifier_ecolage_eleve;
    END IF;
    
    -- Test 5 : Retard de paiement (statut retard mais pas encore exclu)
    IF v_statut_ecolage IN ('retard', 'partiellement_paye') THEN
        SET p_peut_passer = 0;
        SET p_message = CONCAT('⚠️ ', v_nom_eleve, ' : Retard de paiement. Montant restant : ', 
                              CAST(v_montant_restant AS CHAR), ' Ar. Paiement requis avant évaluation.');
        LEAVE verifier_ecolage_eleve;
    END IF;
    
    -- ✅ Tous les tests passés
    SET p_peut_passer = 1;
    SET p_message = CONCAT('✅ ', v_nom_eleve, ' : Autorisé à passer l\'évaluation.');
    
END$$

DELIMITER ;

-- ============================================================================
-- FONCTION 2 : calculer_moyenne_bulletin
-- Description : Calcule la note de bulletin selon la formule :
--               note_bulletin = (moyenne_interrogations + note_examen × 2) / 3
-- Paramètres :
--   - p_moyenne_interro : Moyenne des interrogations
--   - p_note_examen : Note de l'examen final
-- Retour : Note du bulletin (DECIMAL)
-- ============================================================================

DROP FUNCTION IF EXISTS calculer_moyenne_bulletin;

DELIMITER $$

CREATE FUNCTION calculer_moyenne_bulletin(
    p_moyenne_interro DECIMAL(5,2),
    p_note_examen DECIMAL(5,2)
)
RETURNS DECIMAL(5,2)
DETERMINISTIC
BEGIN
    DECLARE v_note_bulletin DECIMAL(5,2);
    
    -- Cas 1 : Les deux notes sont disponibles
    IF p_moyenne_interro IS NOT NULL AND p_note_examen IS NOT NULL THEN
        SET v_note_bulletin = (p_moyenne_interro + (p_note_examen * 2)) / 3;
    
    -- Cas 2 : Seulement la moyenne des interrogations
    ELSEIF p_moyenne_interro IS NOT NULL AND p_note_examen IS NULL THEN
        SET v_note_bulletin = p_moyenne_interro;
    
    -- Cas 3 : Seulement la note d'examen
    ELSEIF p_moyenne_interro IS NULL AND p_note_examen IS NOT NULL THEN
        SET v_note_bulletin = p_note_examen;
    
    -- Cas 4 : Aucune note disponible
    ELSE
        SET v_note_bulletin = NULL;
    END IF;
    
    -- Arrondir à 2 décimales
    RETURN ROUND(v_note_bulletin, 2);
END$$

DELIMITER ;

-- ============================================================================
-- PROCÉDURE 3 : generer_bulletin_annuel
-- Description : Génère ou met à jour le bulletin annuel d'un élève
-- Paramètres :
--   - p_eleve_id : ID de l'élève
--   - p_annee_id : ID de l'année scolaire
-- ============================================================================

DROP PROCEDURE IF EXISTS generer_bulletin_annuel;

DELIMITER $$

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
    
    -- Calculer la moyenne annuelle (moyenne des 3 trimestres)
    SELECT 
        ROUND(AVG(moyenne_generale), 2)
    INTO v_moyenne_annuelle
    FROM bulletins
    WHERE eleve_id = p_eleve_id
      AND annee_scolaire_id = p_annee_id
      AND statut = 'valide';
    
    -- Compter le nombre de bulletins validés
    SELECT COUNT(*)
    INTO v_nb_bulletins_valides
    FROM bulletins
    WHERE eleve_id = p_eleve_id
      AND annee_scolaire_id = p_annee_id
      AND statut = 'valide';
    
    -- Vérifier les impayés
    SELECT COALESCE(SUM(montant_restant), 0)
    INTO v_total_impaye
    FROM echeanciers_ecolages
    WHERE eleve_id = p_eleve_id
      AND annee_scolaire_id = p_annee_id
      AND statut IN ('retard', 'partiellement_paye', 'impaye_exclu', 'exclusion');
    
    -- Bloquer si impayé
    IF v_total_impaye > 0 THEN
        SET v_bloque_par_impaye = 1;
    END IF;
    
    -- Déterminer la décision finale (seulement si tous les bulletins sont validés)
    IF v_nb_bulletins_valides >= 3 AND v_bloque_par_impaye = 0 THEN
        IF v_moyenne_annuelle >= 10 THEN
            SET v_decision_finale = 'admis';
        ELSE
            SET v_decision_finale = 'redouble';
        END IF;
    ELSE
        SET v_decision_finale = 'en_attente';
    END IF;
    
    -- Calculer le rang annuel (parmi les élèves de la même classe)
    SELECT COUNT(*) + 1
    INTO v_rang_annuel
    FROM (
        SELECT 
            b.eleve_id,
            AVG(b.moyenne_generale) as moy_annuelle
        FROM bulletins b
        INNER JOIN inscriptions i ON b.eleve_id = i.eleve_id AND b.annee_scolaire_id = i.annee_scolaire_id
        WHERE b.annee_scolaire_id = p_annee_id
          AND i.classe_id = (
              SELECT classe_id 
              FROM inscriptions 
              WHERE eleve_id = p_eleve_id AND annee_scolaire_id = p_annee_id
          )
          AND b.statut = 'valide'
        GROUP BY b.eleve_id
        HAVING AVG(b.moyenne_generale) > v_moyenne_annuelle
    ) AS classement;
    
    -- Insérer ou mettre à jour le bulletin annuel
    INSERT INTO bulletins_annuels (
        eleve_id,
        annee_scolaire_id,
        moyenne_annuelle,
        rang_annuel,
        decision_finale,
        bloque_par_impaye,
        date_generation
    )
    VALUES (
        p_eleve_id,
        p_annee_id,
        v_moyenne_annuelle,
        v_rang_annuel,
        v_decision_finale,
        v_bloque_par_impaye,
        NOW()
    )
    ON DUPLICATE KEY UPDATE
        moyenne_annuelle = v_moyenne_annuelle,
        rang_annuel = v_rang_annuel,
        decision_finale = v_decision_finale,
        bloque_par_impaye = v_bloque_par_impaye,
        date_generation = NOW();
        
END$$

DELIMITER ;

-- ============================================================================
-- Tests de vérification
-- ============================================================================

-- Test de la procédure verifier_ecolage_eleve
-- Exemple d'utilisation :
-- CALL verifier_ecolage_eleve(1, 2, 1, @peut, @msg);
-- SELECT @peut as peut_passer, @msg as message;

-- Test de la fonction calculer_moyenne_bulletin
SELECT 
    'Test calcul moyenne' as test,
    calculer_moyenne_bulletin(13.00, 15.00) as resultat_attendu_14_33,
    calculer_moyenne_bulletin(12.00, NULL) as resultat_si_pas_examen_12,
    calculer_moyenne_bulletin(NULL, 15.00) as resultat_si_pas_interro_15;

-- ============================================================================
-- Fin de la migration
-- ============================================================================
