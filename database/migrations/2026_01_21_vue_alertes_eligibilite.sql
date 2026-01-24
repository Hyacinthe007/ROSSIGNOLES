-- ============================================================================
-- Vue : vue_alertes_eligibilite_evaluations
-- Description : Affiche le statut d'éligibilité de chaque élève pour passer
--               des évaluations (basé sur le statut financier)
-- Date : 2026-01-21
-- ============================================================================

-- Sélectionner la base de données
USE abonnements_transport;

CREATE OR REPLACE VIEW vue_alertes_eligibilite_evaluations AS
SELECT 
    e.id as eleve_id,
    e.matricule,
    CONCAT(e.nom, ' ', e.prenom) as eleve,
    c.nom as classe,
    c.code as classe_code,
    a.libelle as annee_scolaire,
    i.statut as statut_inscription,
    i.bloquee,
    i.date_inscription,
    COALESCE(see.peut_suivre_cours, 1) as peut_suivre_cours,
    see.statut as statut_ecolage_mois,
    see.date_exclusion,
    ech.mois as mois_impaye,
    ech.montant_restant,
    ech.jours_retard,
    ech.date_limite_grace,
    -- Déterminer le statut d'éligibilité
    CASE 
        WHEN i.statut != 'validee' THEN 'Inscription non validée'
        WHEN i.bloquee = 1 THEN 'Inscription bloquée (paiement initial)'
        WHEN COALESCE(see.peut_suivre_cours, 1) = 0 THEN 'Exclu pour impayé'
        ELSE 'Éligible'
    END as statut_eligibilite,
    -- Indicateur binaire : peut passer des évaluations ?
    CASE 
        WHEN i.statut != 'validee' OR i.bloquee = 1 OR 
             COALESCE(see.peut_suivre_cours, 1) = 0 
        THEN 0 
        ELSE 1 
    END as peut_passer_evaluations,
    -- Priorité de l'alerte (pour tri)
    CASE 
        WHEN COALESCE(see.peut_suivre_cours, 1) = 0 THEN 1  -- Urgence maximale
        WHEN i.bloquee = 1 THEN 2                           -- Haute priorité
        WHEN i.statut != 'validee' THEN 3                   -- Moyenne priorité
        ELSE 4                                               -- Pas d'alerte
    END as priorite_alerte,
    -- Contact parent
    (SELECT GROUP_CONCAT(DISTINCT p.telephone SEPARATOR ', ')
     FROM eleves_parents ep
     INNER JOIN parents p ON ep.parent_id = p.id
     WHERE ep.eleve_id = e.id AND p.telephone IS NOT NULL
     LIMIT 1
    ) as tel_parents
FROM eleves e
INNER JOIN inscriptions i ON e.id = i.eleve_id
INNER JOIN classes c ON i.classe_id = c.id
INNER JOIN annees_scolaires a ON i.annee_scolaire_id = a.id
LEFT JOIN statuts_eleves_ecolage see ON (
    see.eleve_id = e.id 
    AND see.annee_scolaire_id = i.annee_scolaire_id
    AND see.mois = MONTH(CURDATE())
    AND see.annee = YEAR(CURDATE())
)
LEFT JOIN echeanciers_ecolages ech ON (
    ech.eleve_id = e.id
    AND ech.annee_scolaire_id = i.annee_scolaire_id
    AND ech.mois = MONTH(CURDATE())
    AND ech.annee = YEAR(CURDATE())
)
WHERE a.actif = 1
ORDER BY priorite_alerte ASC, c.nom ASC, e.nom ASC, e.prenom ASC;

-- ============================================================================
-- Vérification : Afficher les statistiques
-- ============================================================================
SELECT 
    'Total élèves inscrits' as categorie,
    COUNT(*) as nombre
FROM vue_alertes_eligibilite_evaluations

UNION ALL

SELECT 
    'Élèves éligibles' as categorie,
    COUNT(*) as nombre
FROM vue_alertes_eligibilite_evaluations
WHERE peut_passer_evaluations = 1

UNION ALL

SELECT 
    'Élèves BLOQUÉS' as categorie,
    COUNT(*) as nombre
FROM vue_alertes_eligibilite_evaluations
WHERE peut_passer_evaluations = 0

UNION ALL

SELECT 
    statut_eligibilite as categorie,
    COUNT(*) as nombre
FROM vue_alertes_eligibilite_evaluations
WHERE peut_passer_evaluations = 0
GROUP BY statut_eligibilite;

-- ============================================================================
-- Fin de la création de la vue
-- ============================================================================
