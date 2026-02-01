<?php
/**
 * Routes web de l'application
 */

$routes = [
    // Authentification
    ['pattern' => 'auth/login', 'method' => 'GET', 'handler' => 'AuthController@login'],
    ['pattern' => 'auth/login', 'method' => 'POST', 'handler' => 'AuthController@login'],
    ['pattern' => 'auth/logout', 'method' => 'GET', 'handler' => 'AuthController@logout'],
    ['pattern' => 'auth/password-reset', 'method' => 'GET', 'handler' => 'AuthController@passwordReset'],
    ['pattern' => 'auth/password-reset', 'method' => 'POST', 'handler' => 'AuthController@passwordReset'],
    
    // Dashboard
    ['pattern' => 'dashboard', 'method' => 'GET', 'handler' => 'DashboardController@index'],
    
    // Élèves
    ['pattern' => 'eleves/list', 'method' => 'GET', 'handler' => 'ElevesController@list'],
    ['pattern' => 'eleves/add', 'method' => 'GET', 'handler' => 'ElevesController@add'],
    ['pattern' => 'eleves/add', 'method' => 'POST', 'handler' => 'ElevesController@add'],
    ['pattern' => 'eleves/edit/{id}', 'method' => 'GET', 'handler' => 'ElevesController@edit'],
    ['pattern' => 'eleves/edit/{id}', 'method' => 'POST', 'handler' => 'ElevesController@edit'],
    ['pattern' => 'eleves/details/{id}', 'method' => 'GET', 'handler' => 'ElevesController@details'],
    ['pattern' => 'eleves/parcours/{id}', 'method' => 'GET', 'handler' => 'ElevesController@parcours'],
    ['pattern' => 'eleves/parcours/pdf/{id}', 'method' => 'GET', 'handler' => 'ElevesController@exportParcoursPdf'],
    ['pattern' => 'eleves/inscription', 'method' => 'GET', 'handler' => 'ElevesController@inscription'],
    ['pattern' => 'eleves/inscription', 'method' => 'POST', 'handler' => 'ElevesController@inscription'],
    ['pattern' => 'eleves/export-pdf', 'method' => 'GET', 'handler' => 'ElevesController@exportPdf'],
    ['pattern' => 'eleves/export-excel', 'method' => 'GET', 'handler' => 'ElevesController@exportExcel'],
    ['pattern' => 'eleves/certificat/{id}', 'method' => 'GET', 'handler' => 'ElevesController@certificatScolaire'],
    
    // Parents
    ['pattern' => 'parents/list', 'method' => 'GET', 'handler' => 'ParentsController@list'],
    ['pattern' => 'parents/add', 'method' => 'GET', 'handler' => 'ParentsController@add'],
    ['pattern' => 'parents/add', 'method' => 'POST', 'handler' => 'ParentsController@add'],
    ['pattern' => 'parents/edit/{id}', 'method' => 'GET', 'handler' => 'ParentsController@edit'],
    ['pattern' => 'parents/edit/{id}', 'method' => 'POST', 'handler' => 'ParentsController@edit'],
    ['pattern' => 'parents/details/{id}', 'method' => 'GET', 'handler' => 'ParentsController@details'],
    ['pattern' => 'parents/delete/{id}', 'method' => 'GET', 'handler' => 'ParentsController@delete'],
    ['pattern' => 'parents/delete/{id}', 'method' => 'POST', 'handler' => 'ParentsController@delete'],
    
    
    // Inscriptions (module unifié pour inscriptions et réinscriptions)
    ['pattern' => 'inscriptions/liste', 'method' => 'GET', 'handler' => 'InscriptionsController@liste'],
    ['pattern' => 'inscriptions/nouveau', 'method' => 'GET', 'handler' => 'InscriptionsController@nouveau'],
    ['pattern' => 'inscriptions/nouveau', 'method' => 'POST', 'handler' => 'InscriptionsController@nouveau'],
    ['pattern' => 'inscriptions/inscrire-enfant/{id}', 'method' => 'GET', 'handler' => 'InscriptionsController@parParent'],
    ['pattern' => 'inscriptions/enregistrer', 'method' => 'POST', 'handler' => 'InscriptionsController@enregistrer'],
    ['pattern' => 'inscriptions/details/{id}', 'method' => 'GET', 'handler' => 'InscriptionsController@details'],
    ['pattern' => 'inscriptions/ajouter-paiement/{id}', 'method' => 'GET', 'handler' => 'InscriptionsController@ajouterPaiement'],
    ['pattern' => 'inscriptions/ajouter-paiement/{id}', 'method' => 'POST', 'handler' => 'InscriptionsController@ajouterPaiement'],
    ['pattern' => 'inscriptions/terminer/{id}', 'method' => 'GET', 'handler' => 'InscriptionsController@terminer'],
    ['pattern' => 'inscriptions/terminer/{id}', 'method' => 'POST', 'handler' => 'InscriptionsController@terminer'],
    ['pattern' => 'inscriptions/modifier/{id}', 'method' => 'GET', 'handler' => 'InscriptionsController@modifier'],
    ['pattern' => 'inscriptions/update', 'method' => 'POST', 'handler' => 'InscriptionsController@mettreAJour'],
    ['pattern' => 'inscriptions/recu/{id}', 'method' => 'GET', 'handler' => 'InscriptionsController@recuPaiement'],
    ['pattern' => 'inscriptions/documents/{id}', 'method' => 'GET', 'handler' => 'InscriptionsController@documents'],
    ['pattern' => 'inscriptions/documents/{id}', 'method' => 'POST', 'handler' => 'InscriptionsController@documents'],
    
    // Années Scolaires
    ['pattern' => 'annees-scolaires/list', 'method' => 'GET', 'handler' => 'AnneesScolairesController@list'],
    ['pattern' => 'annees-scolaires/add', 'method' => 'GET', 'handler' => 'AnneesScolairesController@add'],
    ['pattern' => 'annees-scolaires/add', 'method' => 'POST', 'handler' => 'AnneesScolairesController@add'],
    ['pattern' => 'annees-scolaires/edit/{id}', 'method' => 'GET', 'handler' => 'AnneesScolairesController@edit'],
    ['pattern' => 'annees-scolaires/edit/{id}', 'method' => 'POST', 'handler' => 'AnneesScolairesController@edit'],
    ['pattern' => 'annees-scolaires/details/{id}', 'method' => 'GET', 'handler' => 'AnneesScolairesController@details'],
    ['pattern' => 'annees-scolaires/activate/{id}', 'method' => 'GET', 'handler' => 'AnneesScolairesController@activate'],
    
    // Périodes
    ['pattern' => 'periodes/list', 'method' => 'GET', 'handler' => 'PeriodesController@list'],
    ['pattern' => 'periodes/add', 'method' => 'GET', 'handler' => 'PeriodesController@add'],
    ['pattern' => 'periodes/add', 'method' => 'POST', 'handler' => 'PeriodesController@add'],
    ['pattern' => 'periodes/edit/{id}', 'method' => 'GET', 'handler' => 'PeriodesController@edit'],
    ['pattern' => 'periodes/edit/{id}', 'method' => 'POST', 'handler' => 'PeriodesController@edit'],
    ['pattern' => 'periodes/delete/{id}', 'method' => 'GET', 'handler' => 'PeriodesController@delete'],
    
    // Calendrier Scolaire (Vacances & Fériés)
    ['pattern' => 'calendrier/list', 'method' => 'GET', 'handler' => 'CalendrierController@list'],
    ['pattern' => 'calendrier/add', 'method' => 'GET', 'handler' => 'CalendrierController@add'],
    ['pattern' => 'calendrier/add', 'method' => 'POST', 'handler' => 'CalendrierController@add'],
    ['pattern' => 'calendrier/edit/{id}', 'method' => 'GET', 'handler' => 'CalendrierController@edit'],
    ['pattern' => 'calendrier/edit/{id}', 'method' => 'POST', 'handler' => 'CalendrierController@edit'],
    ['pattern' => 'calendrier/delete/{id}', 'method' => 'GET', 'handler' => 'CalendrierController@delete'],
    
    // Classes
    ['pattern' => 'classes/list', 'method' => 'GET', 'handler' => 'ClassesController@list'],
    ['pattern' => 'classes/add', 'method' => 'GET', 'handler' => 'ClassesController@add'],
    ['pattern' => 'classes/add', 'method' => 'POST', 'handler' => 'ClassesController@add'],
    ['pattern' => 'classes/edit/{id}', 'method' => 'GET', 'handler' => 'ClassesController@edit'],
    ['pattern' => 'classes/edit/{id}', 'method' => 'POST', 'handler' => 'ClassesController@edit'],
    ['pattern' => 'classes/details/{id}', 'method' => 'GET', 'handler' => 'ClassesController@details'],
    ['pattern' => 'classes/associer', 'method' => 'GET', 'handler' => 'ClassesController@associer'],
    ['pattern' => 'classes/associer', 'method' => 'POST', 'handler' => 'ClassesController@associer'],
    ['pattern' => 'classes/associer/update', 'method' => 'POST', 'handler' => 'ClassesController@updateAssociation'],
    ['pattern' => 'classes/associer/bulk-update', 'method' => 'POST', 'handler' => 'ClassesController@bulkUpdateAssociations'],
    ['pattern' => 'classes/associer/stats', 'method' => 'GET', 'handler' => 'ClassesController@getAssociationStats'],
    ['pattern' => 'classes/eleves', 'method' => 'GET', 'handler' => 'ClassesController@eleves'],
    
    ['pattern' => 'classes/series-by-niveau/{id}', 'method' => 'GET', 'handler' => 'ClassesController@getSeriesByNiveau'],
    
    // Pédagogie (Niveaux, Cycles, Séries, Enseignements, Emplois du temps)
    ['pattern' => 'pedagogie/niveaux', 'method' => 'GET', 'handler' => 'PedagogieController@niveaux'],
    ['pattern' => 'pedagogie/cycles', 'method' => 'GET', 'handler' => 'PedagogieController@cycles'],
    
    ['pattern' => 'pedagogie/series', 'method' => 'GET', 'handler' => 'PedagogieController@series'],
    ['pattern' => 'pedagogie/series/add', 'method' => 'POST', 'handler' => 'PedagogieController@addSerie'],
    ['pattern' => 'pedagogie/series/edit/{id}', 'method' => 'POST', 'handler' => 'PedagogieController@editSerie'],
    ['pattern' => 'pedagogie/series/delete/{id}', 'method' => 'GET', 'handler' => 'PedagogieController@deleteSerie'],
    ['pattern' => 'pedagogie/series/toggle/{id}', 'method' => 'GET', 'handler' => 'PedagogieController@toggleSerie'],
    
    ['pattern' => 'pedagogie/enseignements', 'method' => 'GET', 'handler' => 'PedagogieController@enseignements'],
    ['pattern' => 'pedagogie/enseignements/add', 'method' => 'GET', 'handler' => 'PedagogieController@addEnseignement'],
    ['pattern' => 'pedagogie/enseignements/add', 'method' => 'POST', 'handler' => 'PedagogieController@addEnseignement'],
    ['pattern' => 'pedagogie/enseignements/edit/{id}', 'method' => 'GET', 'handler' => 'PedagogieController@editEnseignement'],
    ['pattern' => 'pedagogie/enseignements/edit/{id}', 'method' => 'POST', 'handler' => 'PedagogieController@editEnseignement'],
    ['pattern' => 'pedagogie/enseignements/delete/{id}', 'method' => 'GET', 'handler' => 'PedagogieController@deleteEnseignement'],
    ['pattern' => 'pedagogie/emplois-temps', 'method' => 'GET', 'handler' => 'PedagogieController@emploisTemps'],
    ['pattern' => 'pedagogie/emplois-temps/add', 'method' => 'GET', 'handler' => 'PedagogieController@addEmploiTemps'],
    ['pattern' => 'pedagogie/emplois-temps/add', 'method' => 'POST', 'handler' => 'PedagogieController@addEmploiTemps'],
    ['pattern' => 'pedagogie/emplois-temps/edit/{id}', 'method' => 'GET', 'handler' => 'PedagogieController@editEmploiTemps'],
    ['pattern' => 'pedagogie/emplois-temps/edit/{id}', 'method' => 'POST', 'handler' => 'PedagogieController@editEmploiTemps'],
    
    // Gestion des Coefficients
    ['pattern' => 'pedagogie/series/coefficients/{id}', 'method' => 'GET', 'handler' => 'PedagogieController@coefficients'],
    ['pattern' => 'pedagogie/series/update-coefficients', 'method' => 'POST', 'handler' => 'PedagogieController@updateCoefficients'],
    ['pattern' => 'pedagogie/niveaux/coefficients/{id}', 'method' => 'GET', 'handler' => 'PedagogieController@coefficientsNiveau'],
    ['pattern' => 'pedagogie/niveaux/update-coefficients', 'method' => 'POST', 'handler' => 'PedagogieController@updateCoefficientsNiveau'],
    ['pattern' => 'pedagogie/classes/coefficients/{id}', 'method' => 'GET', 'handler' => 'PedagogieController@coefficientsClasse'],
    ['pattern' => 'pedagogie/classes/update-coefficients', 'method' => 'POST', 'handler' => 'PedagogieController@updateCoefficientsClasse'],
    
    // Matières
    ['pattern' => 'matieres/list', 'method' => 'GET', 'handler' => 'MatieresController@list'],
    ['pattern' => 'matieres/add', 'method' => 'GET', 'handler' => 'MatieresController@add'],
    ['pattern' => 'matieres/add', 'method' => 'POST', 'handler' => 'MatieresController@add'],
    ['pattern' => 'matieres/edit/{id}', 'method' => 'GET', 'handler' => 'MatieresController@edit'],
    ['pattern' => 'matieres/edit/{id}', 'method' => 'POST', 'handler' => 'MatieresController@edit'],
    ['pattern' => 'matieres/details/{id}', 'method' => 'GET', 'handler' => 'MatieresController@details'],
    ['pattern' => 'matieres/delete/{id}', 'method' => 'GET', 'handler' => 'MatieresController@delete'],
    ['pattern' => 'matieres/delete/{id}', 'method' => 'POST', 'handler' => 'MatieresController@delete'],
    
    // Enseignants
    ['pattern' => 'enseignants/list', 'method' => 'GET', 'handler' => 'EnseignantsController@list'],
    ['pattern' => 'enseignants/add', 'method' => 'GET', 'handler' => 'EnseignantsController@add'],
    ['pattern' => 'enseignants/add', 'method' => 'POST', 'handler' => 'EnseignantsController@add'],
    ['pattern' => 'enseignants/edit/{id}', 'method' => 'GET', 'handler' => 'EnseignantsController@edit'],
    ['pattern' => 'enseignants/edit/{id}', 'method' => 'POST', 'handler' => 'EnseignantsController@edit'],
    ['pattern' => 'enseignants/details/{id}', 'method' => 'GET', 'handler' => 'EnseignantsController@details'],
    ['pattern' => 'enseignants/delete/{id}', 'method' => 'GET', 'handler' => 'EnseignantsController@delete'],
    ['pattern' => 'enseignants/delete/{id}', 'method' => 'POST', 'handler' => 'EnseignantsController@delete'],
    
    // Personnel
    ['pattern' => 'personnel/list', 'method' => 'GET', 'handler' => 'PersonnelController@list'],
    ['pattern' => 'personnel/add', 'method' => 'GET', 'handler' => 'PersonnelController@add'],
    ['pattern' => 'personnel/add', 'method' => 'POST', 'handler' => 'PersonnelController@add'],
    ['pattern' => 'personnel/edit/{id}', 'method' => 'GET', 'handler' => 'PersonnelController@edit'],
    ['pattern' => 'personnel/edit/{id}', 'method' => 'POST', 'handler' => 'PersonnelController@edit'],
    ['pattern' => 'personnel/details/{id}', 'method' => 'GET', 'handler' => 'PersonnelController@details'],
    ['pattern' => 'personnel/delete/{id}', 'method' => 'GET', 'handler' => 'PersonnelController@delete'],
    ['pattern' => 'personnel/delete/{id}', 'method' => 'POST', 'handler' => 'PersonnelController@delete'],
    ['pattern' => 'personnel/certificat/{id}', 'method' => 'GET', 'handler' => 'PersonnelController@certificatTravail'],
    
    // Interrogations
    ['pattern' => 'interrogations/list', 'method' => 'GET', 'handler' => 'InterrogationsController@list'],
    ['pattern' => 'interrogations/add', 'method' => 'GET', 'handler' => 'InterrogationsController@add'],
    ['pattern' => 'interrogations/add', 'method' => 'POST', 'handler' => 'InterrogationsController@add'],
    ['pattern' => 'interrogations/edit/{id}', 'method' => 'GET', 'handler' => 'InterrogationsController@edit'],
    ['pattern' => 'interrogations/edit/{id}', 'method' => 'POST', 'handler' => 'InterrogationsController@edit'],
    ['pattern' => 'interrogations/delete/{id}', 'method' => 'GET', 'handler' => 'InterrogationsController@delete'],
    ['pattern' => 'interrogations/delete/{id}', 'method' => 'POST', 'handler' => 'InterrogationsController@delete'],
    
    // Examens
    ['pattern' => 'examens/list', 'method' => 'GET', 'handler' => 'ExamensController@list'],
    ['pattern' => 'examens/add', 'method' => 'GET', 'handler' => 'ExamensController@add'],
    ['pattern' => 'examens/add', 'method' => 'POST', 'handler' => 'ExamensController@add'],
    ['pattern' => 'examens/edit/{id}', 'method' => 'GET', 'handler' => 'ExamensController@edit'],
    ['pattern' => 'examens/edit/{id}', 'method' => 'POST', 'handler' => 'ExamensController@edit'],
    ['pattern' => 'examens/delete/{id}', 'method' => 'GET', 'handler' => 'ExamensController@delete'],
    ['pattern' => 'examens/delete/{id}', 'method' => 'POST', 'handler' => 'ExamensController@delete'],
    
    // Evaluations (Unifiées)
    ['pattern' => 'evaluations', 'method' => 'GET', 'handler' => 'EvaluationsController@index'],
    ['pattern' => 'evaluations/add', 'method' => 'GET', 'handler' => 'EvaluationsController@index'],
    
    // Notes
    ['pattern' => 'notes/list', 'method' => 'GET', 'handler' => 'NotesController@list'],
    ['pattern' => 'notes/saisie', 'method' => 'GET', 'handler' => 'NotesController@saisie'],
    ['pattern' => 'notes/saisie', 'method' => 'POST', 'handler' => 'NotesController@saisie'],
    // Saisie en masse (interface optimisée)
    ['pattern' => 'notes/saisie-masse', 'method' => 'GET', 'handler' => 'NotesController@saisieMasse'],
    ['pattern' => 'notes/saisie-masse/save', 'method' => 'POST', 'handler' => 'NotesController@saveAjax'],
    ['pattern' => 'notes/saisie-masse/import', 'method' => 'POST', 'handler' => 'NotesController@importExcel'],
    ['pattern' => 'notes/download-template', 'method' => 'GET', 'handler' => 'NotesController@downloadTemplate'],
    ['pattern' => 'notes/moyennes', 'method' => 'GET', 'handler' => 'NotesController@moyennes'],
    
    // Bulletins
    ['pattern' => 'bulletins/list', 'method' => 'GET', 'handler' => 'BulletinsController@list'],
    ['pattern' => 'bulletins/generer', 'method' => 'GET', 'handler' => 'BulletinsController@generer'],
    ['pattern' => 'bulletins/generer', 'method' => 'POST', 'handler' => 'BulletinsController@generer'],
    ['pattern' => 'bulletins/api/eleves-count', 'method' => 'GET', 'handler' => 'BulletinsController@getElevesCount'],
    ['pattern' => 'bulletins/generate/{eleveId}/{periodeId}', 'method' => 'GET', 'handler' => 'BulletinsController@generate'],
    ['pattern' => 'bulletins/pdf/{id}', 'method' => 'GET', 'handler' => 'BulletinsController@pdf'],
    ['pattern' => 'bulletins/valider/{id}', 'method' => 'GET', 'handler' => 'BulletinsController@valider'],
    ['pattern' => 'bulletins/valider-tout', 'method' => 'POST', 'handler' => 'BulletinsController@validerTout'],
    
    // Présences (redirigées vers Absences pour compatibilité)
    ['pattern' => 'presences/list', 'method' => 'GET', 'handler' => 'AbsencesController@list'],
    ['pattern' => 'presences/saisie', 'method' => 'GET', 'handler' => 'AbsencesController@add'],
    ['pattern' => 'presences/saisie', 'method' => 'POST', 'handler' => 'AbsencesController@add'],
    
    // Absences
    ['pattern' => 'absences/list', 'method' => 'GET', 'handler' => 'AbsencesController@list'],
    ['pattern' => 'absences/add', 'method' => 'GET', 'handler' => 'AbsencesController@add'],
    ['pattern' => 'absences/add', 'method' => 'POST', 'handler' => 'AbsencesController@add'],
    ['pattern' => 'absences/search-eleves', 'method' => 'GET', 'handler' => 'AbsencesController@searchEleves'],
    ['pattern' => 'absences/get-eleves-classe', 'method' => 'GET', 'handler' => 'AbsencesController@getElevesClasse'],
    ['pattern' => 'absences/get-emplois-temps', 'method' => 'GET', 'handler' => 'AbsencesController@getEmploisTemps'],
    ['pattern' => 'absences/get-absences-recentes', 'method' => 'GET', 'handler' => 'AbsencesController@getAbsencesRecentes'],
    ['pattern' => 'absences/edit/{id}', 'method' => 'GET', 'handler' => 'AbsencesController@edit'],
    ['pattern' => 'absences/edit/{id}', 'method' => 'POST', 'handler' => 'AbsencesController@edit'],
    ['pattern' => 'absences/details/{id}', 'method' => 'GET', 'handler' => 'AbsencesController@details'],
    ['pattern' => 'absences/delete/{id}', 'method' => 'GET', 'handler' => 'AbsencesController@delete'],
    ['pattern' => 'absences/delete/{id}', 'method' => 'POST', 'handler' => 'AbsencesController@delete'],
    
    // Retards (redirigés vers Absences avec type='retard')
    ['pattern' => 'retards/list', 'method' => 'GET', 'handler' => 'AbsencesController@list'], // Utilisera ?type=retard
    ['pattern' => 'retards/add', 'method' => 'GET', 'handler' => 'AbsencesController@add'],
    ['pattern' => 'retards/add', 'method' => 'POST', 'handler' => 'AbsencesController@add'],
    ['pattern' => 'retards/edit/{id}', 'method' => 'GET', 'handler' => 'AbsencesController@edit'],
    ['pattern' => 'retards/edit/{id}', 'method' => 'POST', 'handler' => 'AbsencesController@edit'],
    ['pattern' => 'retards/details/{id}', 'method' => 'GET', 'handler' => 'AbsencesController@details'],
    ['pattern' => 'retards/delete/{id}', 'method' => 'GET', 'handler' => 'AbsencesController@delete'],
    ['pattern' => 'retards/delete/{id}', 'method' => 'POST', 'handler' => 'AbsencesController@delete'],
    
    // Sanctions
    ['pattern' => 'sanctions/list', 'method' => 'GET', 'handler' => 'SanctionsController@list'],
    ['pattern' => 'sanctions/add', 'method' => 'GET', 'handler' => 'SanctionsController@add'],
    ['pattern' => 'sanctions/add', 'method' => 'POST', 'handler' => 'SanctionsController@add'],
    ['pattern' => 'sanctions/edit/{id}', 'method' => 'GET', 'handler' => 'SanctionsController@edit'],
    ['pattern' => 'sanctions/edit/{id}', 'method' => 'POST', 'handler' => 'SanctionsController@edit'],
    ['pattern' => 'sanctions/details/{id}', 'method' => 'GET', 'handler' => 'SanctionsController@details'],
    ['pattern' => 'sanctions/delete/{id}', 'method' => 'GET', 'handler' => 'SanctionsController@delete'],
    ['pattern' => 'sanctions/delete/{id}', 'method' => 'POST', 'handler' => 'SanctionsController@delete'],
    
     
    // ============================================================================
    // FINANCE - Dashboard et Suivi
    // ============================================================================
    ['pattern' => 'finance/dashboard', 'method' => 'GET', 'handler' => 'FinanceController@dashboard'],
    ['pattern' => 'finance/ecolage', 'method' => 'GET', 'handler' => 'FinanceController@listeEcolage'],
    ['pattern' => 'finance/echeanciers', 'method' => 'GET', 'handler' => 'FinanceController@echeanciers'],
    ['pattern' => 'finance/ecolage/payer/{id}', 'method' => 'GET', 'handler' => 'FinanceController@payerEcolage'],
    ['pattern' => 'finance/ecolage/payer/{id}', 'method' => 'POST', 'handler' => 'FinanceController@payerEcolage'],
    ['pattern' => 'finance/echeanciers/sms/{id}', 'method' => 'GET', 'handler' => 'FinanceController@envoyerSmsRelance'],
    ['pattern' => 'finance/echeanciers/sms-all', 'method' => 'POST', 'handler' => 'FinanceController@envoyerSmsRelanceTous'],
    
    // Finance - Types de frais
    ['pattern' => 'finance/types-frais', 'method' => 'GET', 'handler' => 'FinanceController@typesFrais'],
    ['pattern' => 'finance/types-frais/add', 'method' => 'GET', 'handler' => 'FinanceController@addTypeFrais'],
    ['pattern' => 'finance/types-frais/add', 'method' => 'POST', 'handler' => 'FinanceController@addTypeFrais'],
    ['pattern' => 'finance/types-frais/edit/{id}', 'method' => 'GET', 'handler' => 'FinanceController@editTypeFrais'],
    ['pattern' => 'finance/types-frais/edit/{id}', 'method' => 'POST', 'handler' => 'FinanceController@editTypeFrais'],
    ['pattern' => 'finance/types-frais/delete/{id}', 'method' => 'POST', 'handler' => 'FinanceController@deleteTypeFrais'],

    // Finance Recus (Pour l'instant via FinanceController)
    ['pattern' => 'finance/recus', 'method' => 'GET', 'handler' => 'FinanceController@recus'],
    ['pattern' => 'finance/recus/export-excel', 'method' => 'GET', 'handler' => 'FinanceController@exportRecusExcel'],
    ['pattern' => 'finance/export-recu/{id}', 'method' => 'GET', 'handler' => 'FinanceController@exportRecuPdf'],
    
    // ============================================================================
    // PAIEMENT MENSUEL D'ÉCOLAGE (Interface simplifiée)
    // ============================================================================
    ['pattern' => 'finance/paiement-mensuel', 'method' => 'GET', 'handler' => 'PaiementMensuelController@index'],
    ['pattern' => 'finance/paiement-mensuel/saisir', 'method' => 'GET', 'handler' => 'PaiementMensuelController@saisir'],
    ['pattern' => 'finance/paiement-mensuel/generer', 'method' => 'GET', 'handler' => 'PaiementMensuelController@generer'],
    ['pattern' => 'finance/paiement-mensuel/enregistrer', 'method' => 'POST', 'handler' => 'PaiementMensuelController@enregistrer'],
    
   
    // Notifications
    ['pattern' => 'notifications/messagerie', 'method' => 'GET', 'handler' => 'NotificationsController@messagerie'],
    ['pattern' => 'notifications/messagerie/envoyer', 'method' => 'POST', 'handler' => 'NotificationsController@envoyerMessage'],
    ['pattern' => 'notifications/list', 'method' => 'GET', 'handler' => 'NotificationsController@list'],
    ['pattern' => 'notifications/add', 'method' => 'GET', 'handler' => 'NotificationsController@add'],
    ['pattern' => 'notifications/add', 'method' => 'POST', 'handler' => 'NotificationsController@add'],
    ['pattern' => 'notifications/details/{id}', 'method' => 'GET', 'handler' => 'NotificationsController@details'],
    ['pattern' => 'notifications/modeles', 'method' => 'GET', 'handler' => 'NotificationsController@modeles'],
    ['pattern' => 'notifications/modeles/add', 'method' => 'GET', 'handler' => 'NotificationsController@addModele'],
    ['pattern' => 'notifications/modeles/add', 'method' => 'POST', 'handler' => 'NotificationsController@addModele'],
    ['pattern' => 'notifications/modeles/edit/{id}', 'method' => 'GET', 'handler' => 'NotificationsController@editModele'],
    ['pattern' => 'notifications/modeles/edit/{id}', 'method' => 'POST', 'handler' => 'NotificationsController@editModele'],
    ['pattern' => 'notifications/modeles/delete/{id}', 'method' => 'GET', 'handler' => 'NotificationsController@deleteModele'],
    
    // Système

    ['pattern' => 'systeme/config', 'method' => 'GET', 'handler' => 'SystemeController@config'],
    ['pattern' => 'systeme/config', 'method' => 'POST', 'handler' => 'SystemeController@config'],
    ['pattern' => 'systeme/utilisateurs', 'method' => 'GET', 'handler' => 'SystemeController@utilisateurs'],
    ['pattern' => 'systeme/utilisateurs/add', 'method' => 'GET', 'handler' => 'SystemeController@addUtilisateur'],
    ['pattern' => 'systeme/utilisateurs/add', 'method' => 'POST', 'handler' => 'SystemeController@addUtilisateur'],
    ['pattern' => 'systeme/utilisateurs/edit/{id}', 'method' => 'GET', 'handler' => 'SystemeController@editUtilisateur'],
    ['pattern' => 'systeme/utilisateurs/edit/{id}', 'method' => 'POST', 'handler' => 'SystemeController@editUtilisateur'],
    ['pattern' => 'systeme/utilisateurs/delete/{id}', 'method' => 'POST', 'handler' => 'SystemeController@deleteUtilisateur'],
    ['pattern' => 'systeme/utilisateurs/toggle-status/{id}', 'method' => 'POST', 'handler' => 'SystemeController@toggleStatus'],
    ['pattern' => 'systeme/utilisateurs/sync-parents', 'method' => 'POST', 'handler' => 'SystemeController@syncParentsGroup'],
    
    // Groupes
    ['pattern' => 'systeme/groupes', 'method' => 'GET', 'handler' => 'SystemeController@groupes'],
    ['pattern' => 'systeme/groupes/add', 'method' => 'GET', 'handler' => 'SystemeController@addGroup'],
    ['pattern' => 'systeme/groupes/add', 'method' => 'POST', 'handler' => 'SystemeController@addGroup'],
    ['pattern' => 'systeme/groupes/edit/{id}', 'method' => 'GET', 'handler' => 'SystemeController@editGroup'],
    ['pattern' => 'systeme/groupes/edit/{id}', 'method' => 'POST', 'handler' => 'SystemeController@editGroup'],
    ['pattern' => 'systeme/groupes/delete/{id}', 'method' => 'POST', 'handler' => 'SystemeController@deleteGroup'],
    
    ['pattern' => 'systeme/logs', 'method' => 'GET', 'handler' => 'SystemeController@logs'],
    
    // Personnel - Affectations
    ['pattern' => 'personnel/affectations', 'method' => 'GET', 'handler' => 'PersonnelController@affectations'],

    // Liste Unifiee du Personnel
    ['pattern' => 'liste-personnel', 'method' => 'GET', 'handler' => 'ListePersonnelController@index'],
    ['pattern' => 'liste-personnel/search', 'method' => 'GET', 'handler' => 'ListePersonnelController@search'],
    ['pattern' => 'liste-personnel/export-excel', 'method' => 'GET', 'handler' => 'ListePersonnelController@exportExcel'],
    ['pattern' => 'liste-personnel/export-pdf', 'method' => 'GET', 'handler' => 'ListePersonnelController@exportPdf'],
    
    // Finance Caisse Consolidée
    ['pattern' => 'finance-caisse', 'method' => 'GET', 'handler' => 'FinanceCaisseController@index'],
    ['pattern' => 'finance-caisse/journal', 'method' => 'GET', 'handler' => 'FinanceCaisseController@journal'],
    
    // Personnel - Creation Workflow
    ['pattern' => 'personnel/nouveau', 'method' => 'GET', 'handler' => 'PersonnelController@nouveau'],
    ['pattern' => 'personnel/nouveau', 'method' => 'POST', 'handler' => 'PersonnelController@nouveau'],
    ['pattern' => 'personnel/enregistrer', 'method' => 'POST', 'handler' => 'PersonnelController@enregistrer'],

    // Parcours Scolaires
    ['pattern' => 'parcours/list', 'method' => 'GET', 'handler' => 'ParcoursController@list'],
    ['pattern' => 'parcours/details/{id}', 'method' => 'GET', 'handler' => 'ParcoursController@details'],

    // Conseils de Classe
    ['pattern' => 'conseils/list', 'method' => 'GET', 'handler' => 'ConseilsController@list'],
    ['pattern' => 'conseils/add', 'method' => 'GET', 'handler' => 'ConseilsController@add'],
    ['pattern' => 'conseils/add', 'method' => 'POST', 'handler' => 'ConseilsController@add'],
    ['pattern' => 'conseils/edit/{id}', 'method' => 'GET', 'handler' => 'ConseilsController@edit'],
    ['pattern' => 'conseils/edit/{id}', 'method' => 'POST', 'handler' => 'ConseilsController@edit'],
    ['pattern' => 'conseils/details/{id}', 'method' => 'GET', 'handler' => 'ConseilsController@details'],
    ['pattern' => 'conseils/delete/{id}', 'method' => 'GET', 'handler' => 'ConseilsController@delete'],

    // Annonces
    ['pattern' => 'annonces/list', 'method' => 'GET', 'handler' => 'AnnoncesController@list'],
    ['pattern' => 'annonces/add', 'method' => 'GET', 'handler' => 'AnnoncesController@add'],
    ['pattern' => 'annonces/add', 'method' => 'POST', 'handler' => 'AnnoncesController@add'],
    ['pattern' => 'annonces/edit/{id}', 'method' => 'GET', 'handler' => 'AnnoncesController@edit'],
    ['pattern' => 'annonces/edit/{id}', 'method' => 'POST', 'handler' => 'AnnoncesController@edit'],
    ['pattern' => 'annonces/delete/{id}', 'method' => 'GET', 'handler' => 'AnnoncesController@delete'],

    // Rôles et Permissions
    ['pattern' => 'roles/list', 'method' => 'GET', 'handler' => 'RolesController@list'],
    ['pattern' => 'roles/add', 'method' => 'GET', 'handler' => 'RolesController@add'],
    ['pattern' => 'roles/add', 'method' => 'POST', 'handler' => 'RolesController@add'],
    ['pattern' => 'roles/edit/{id}', 'method' => 'GET', 'handler' => 'RolesController@edit'],
    ['pattern' => 'roles/edit/{id}', 'method' => 'POST', 'handler' => 'RolesController@edit'],
    ['pattern' => 'roles/delete/{id}', 'method' => 'GET', 'handler' => 'RolesController@delete'],
    
    // Absences du Personnel
    ['pattern' => 'absences_personnel/list', 'method' => 'GET', 'handler' => 'AbsencesPersonnelController@list'],
    ['pattern' => 'absences_personnel/add', 'method' => 'GET', 'handler' => 'AbsencesPersonnelController@add'],
    ['pattern' => 'absences_personnel/add', 'method' => 'POST', 'handler' => 'AbsencesPersonnelController@add'],
    ['pattern' => 'absences_personnel/edit/{id}', 'method' => 'GET', 'handler' => 'AbsencesPersonnelController@edit'],
    ['pattern' => 'absences_personnel/edit/{id}', 'method' => 'POST', 'handler' => 'AbsencesPersonnelController@edit'],
    ['pattern' => 'absences_personnel/details/{id}', 'method' => 'GET', 'handler' => 'AbsencesPersonnelController@details'],
    ['pattern' => 'absences_personnel/delete/{id}', 'method' => 'GET', 'handler' => 'AbsencesPersonnelController@delete'],
    ['pattern' => 'absences_personnel/delete/{id}', 'method' => 'POST', 'handler' => 'AbsencesPersonnelController@delete'],
    ['pattern' => 'absences_personnel/valider/{id}', 'method' => 'POST', 'handler' => 'AbsencesPersonnelController@valider'],
    ['pattern' => 'absences_personnel/refuser/{id}', 'method' => 'POST', 'handler' => 'AbsencesPersonnelController@refuser'],
    // ============================================================================
    // GESTION DES TARIFS (Inscription, Écolage, etc.)
    // ============================================================================
    ['pattern' => 'tarifs/liste', 'method' => 'GET', 'handler' => 'TarifController@liste'],
    ['pattern' => 'tarifs/nouveau', 'method' => 'GET', 'handler' => 'TarifController@nouveau'],
    ['pattern' => 'tarifs/creer', 'method' => 'POST', 'handler' => 'TarifController@creer'],
    ['pattern' => 'tarifs/modifier/{id}', 'method' => 'GET', 'handler' => 'TarifController@modifier'],
    ['pattern' => 'tarifs/mettre-a-jour/{id}', 'method' => 'POST', 'handler' => 'TarifController@mettreAJour'],
    ['pattern' => 'tarifs/activer/{id}', 'method' => 'GET', 'handler' => 'TarifController@activer'],
    
    // ============================================================================
    // GESTION DES ÉCHÉANCIERS D'ÉCOLAGE
    // ============================================================================
    ['pattern' => 'echeancier/show', 'method' => 'GET', 'handler' => 'EcheancierController@show'],
    ['pattern' => 'echeancier/retards', 'method' => 'GET', 'handler' => 'EcheancierController@retards'],
    ['pattern' => 'echeancier/generer', 'method' => 'GET', 'handler' => 'EcheancierController@generer'],
    ['pattern' => 'echeancier/generer', 'method' => 'POST', 'handler' => 'EcheancierController@generer'],
    ['pattern' => 'echeancier/supprimer', 'method' => 'POST', 'handler' => 'EcheancierController@supprimer'],
    ['pattern' => 'echeancier/export-pdf', 'method' => 'GET', 'handler' => 'EcheancierController@exportPdf'],
    ['pattern' => 'echeancier/api', 'method' => 'GET', 'handler' => 'EcheancierController@api'],

    // ============================================================================
    // GESTION DES ARTICLES SCOLAIRES
    // ============================================================================
    ['pattern' => 'articles/liste', 'method' => 'GET', 'handler' => 'ArticlesController@liste'],
    ['pattern' => 'articles/nouveau', 'method' => 'GET', 'handler' => 'ArticlesController@nouveau'],
    ['pattern' => 'articles/creer', 'method' => 'POST', 'handler' => 'ArticlesController@creer'],
    ['pattern' => 'articles/modifier/{id}', 'method' => 'GET', 'handler' => 'ArticlesController@modifier'],
    ['pattern' => 'articles/mettre-a-jour/{id}', 'method' => 'POST', 'handler' => 'ArticlesController@mettreAJour'],
    ['pattern' => 'articles/supprimer/{id}', 'method' => 'GET', 'handler' => 'ArticlesController@supprimer'],
    ['pattern' => 'articles/tarifs', 'method' => 'GET', 'handler' => 'ArticlesController@tarifs']
];

return $routes;