<?php
/**
 * Contrôleur ArticlesController
 * Gestion des articles scolaires optionnels
 */

require_once __DIR__ . '/BaseController.php';
require_once APP_PATH . '/Models/Article.php';
require_once APP_PATH . '/Models/TarifArticle.php';
require_once APP_PATH . '/Models/AnneeScolaire.php';

class ArticlesController extends BaseController {
    
    /**
     * Liste des articles
     */
    public function liste() {
        $this->requireAuth();
        
        $articleModel = new Article();
        $anneeModel = new AnneeScolaire();
        
        // Récupérer l'année active
        $anneeActive = $anneeModel->getActive();
        $anneeId = $_GET['annee_id'] ?? ($anneeActive['id'] ?? null);
        
        // Récupérer tous les articles avec leurs tarifs
        $articles = $articleModel->getAllWithTarifs($anneeId);
        
        // Récupérer toutes les années pour le filtre
        $annees = $anneeModel->all([], 'date_debut DESC');
        
        $this->view('articles/liste', [
            'articles' => $articles,
            'annees' => $annees,
            'selectedAnnee' => $anneeId
        ]);
    }
    
    /**
     * Formulaire de création
     */
    public function nouveau() {
        $this->requireAuth();
        
        $anneeModel = new AnneeScolaire();
        $annees = $anneeModel->all(['actif' => 1], 'date_debut DESC');
        
        $this->view('articles/formulaire', [
            'article' => null,
            'annees' => $annees
        ]);
    }
    
    /**
     * Enregistrer un nouvel article
     */
    public function creer() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/articles/nouveau');
            return;
        }
        
        try {
            $articleModel = new Article();
            $tarifModel = new TarifArticle();
            
            // Validation
            if (empty($_POST['code']) || empty($_POST['libelle'])) {
                throw new Exception("Le code et le libellé sont obligatoires");
            }
            
            // Vérifier si le code existe déjà
            if ($articleModel->codeExists($_POST['code'])) {
                throw new Exception("Ce code article existe déjà");
            }
            
            // Créer l'article
            $articleData = [
                'code' => strtoupper(trim($_POST['code'])),
                'libelle' => trim($_POST['libelle']),
                'type_article' => $_POST['type_article'] ?? 'autre',
                'obligatoire' => isset($_POST['obligatoire']) ? 1 : 0,
                'actif' => isset($_POST['actif']) ? 1 : 0
            ];
            
            $articleId = $articleModel->create($articleData);
            
            // Créer le tarif si un prix est fourni
            if (!empty($_POST['prix_unitaire']) && !empty($_POST['annee_scolaire_id'])) {
                $tarifData = [
                    'article_id' => $articleId,
                    'annee_scolaire_id' => $_POST['annee_scolaire_id'],
                    'prix_unitaire' => floatval($_POST['prix_unitaire']),
                    'taille' => !empty($_POST['taille']) ? $_POST['taille'] : null
                ];
                
                $tarifModel->create($tarifData);
            }
            
            $_SESSION['success'] = "Article créé avec succès";
            $this->redirect('/articles/liste');
            
        } catch (Exception $e) {
            $_SESSION['error'] = "Erreur : " . $e->getMessage();
            $this->redirect('/articles/nouveau');
        }
    }
    
    /**
     * Formulaire de modification
     */
    public function modifier($id) {
        $this->requireAuth();
        
        $articleModel = new Article();
        $article = $articleModel->find($id);
        
        if (!$article) {
            $_SESSION['error'] = "Article introuvable";
            $this->redirect('/articles/liste');
            return;
        }
        
        $anneeModel = new AnneeScolaire();
        $annees = $anneeModel->all(['actif' => 1], 'date_debut DESC');
        
        // Récupérer le tarif pour l'année active
        $anneeActive = $anneeModel->getActive();
        $tarif = null;
        if ($anneeActive) {
            $tarifModel = new TarifArticle();
            $tarif = $tarifModel->getByArticleAndAnnee($id, $anneeActive['id']);
        }
        
        $this->view('articles/formulaire', [
            'article' => $article,
            'tarif' => $tarif,
            'annees' => $annees
        ]);
    }
    
    /**
     * Mettre à jour un article
     */
    public function mettreAJour($id) {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/articles/modifier/' . $id);
            return;
        }
        
        try {
            $articleModel = new Article();
            $tarifModel = new TarifArticle();
            
            // Validation
            if (empty($_POST['code']) || empty($_POST['libelle'])) {
                throw new Exception("Le code et le libellé sont obligatoires");
            }
            
            // Vérifier si le code existe déjà (sauf pour cet article)
            if ($articleModel->codeExists($_POST['code'], $id)) {
                throw new Exception("Ce code article existe déjà");
            }
            
            // Mettre à jour l'article
            $articleData = [
                'code' => strtoupper(trim($_POST['code'])),
                'libelle' => trim($_POST['libelle']),
                'type_article' => $_POST['type_article'] ?? 'autre',
                'obligatoire' => isset($_POST['obligatoire']) ? 1 : 0,
                'actif' => isset($_POST['actif']) ? 1 : 0
            ];
            
            $articleModel->update($id, $articleData);
            
            // Créer ou mettre à jour le tarif
            if (!empty($_POST['prix_unitaire']) && !empty($_POST['annee_scolaire_id'])) {
                $tarifData = [
                    'article_id' => $id,
                    'annee_scolaire_id' => $_POST['annee_scolaire_id'],
                    'prix_unitaire' => floatval($_POST['prix_unitaire']),
                    'taille' => !empty($_POST['taille']) ? $_POST['taille'] : null
                ];
                
                $tarifModel->createOrUpdate($tarifData);
            }
            
            $_SESSION['success'] = "Article modifié avec succès";
            $this->redirect('/articles/liste');
            
        } catch (Exception $e) {
            $_SESSION['error'] = "Erreur : " . $e->getMessage();
            $this->redirect('/articles/modifier/' . $id);
        }
    }
    
    /**
     * Supprimer un article (désactivation)
     */
    public function supprimer($id) {
        $this->requireAuth();
        
        try {
            $articleModel = new Article();
            
            // Vérifier si l'article est utilisé dans des inscriptions
            $usage = $articleModel->queryOne(
                "SELECT COUNT(*) as count FROM inscriptions_articles WHERE article_id = ?",
                [$id]
            );
            
            if ($usage['count'] > 0) {
                // Désactiver au lieu de supprimer
                $articleModel->update($id, ['actif' => 0]);
                $_SESSION['success'] = "Article désactivé (utilisé dans " . $usage['count'] . " inscription(s))";
            } else {
                // Supprimer complètement
                $articleModel->delete($id);
                $_SESSION['success'] = "Article supprimé";
            }
            
        } catch (Exception $e) {
            $_SESSION['error'] = "Erreur : " . $e->getMessage();
        }
        
        $this->redirect('/articles/liste');
    }
    
    /**
     * Gestion des tarifs pour une année donnée
     */
    public function tarifs() {
        $this->requireAuth();
        
        $anneeModel = new AnneeScolaire();
        $anneeActive = $anneeModel->getActive();
        $anneeId = $_GET['annee_id'] ?? ($anneeActive['id'] ?? null);
        
        if (!$anneeId) {
            $_SESSION['error'] = "Aucune année scolaire active";
            $this->redirect('/articles/liste');
            return;
        }
        
        $tarifModel = new TarifArticle();
        $tarifs = $tarifModel->getByAnnee($anneeId);
        
        $annees = $anneeModel->all([], 'date_debut DESC');
        
        $this->view('articles/tarifs', [
            'tarifs' => $tarifs,
            'annees' => $annees,
            'selectedAnnee' => $anneeId
        ]);
    }
}
