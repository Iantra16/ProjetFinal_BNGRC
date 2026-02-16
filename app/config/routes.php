<?php

use app\controllers\BngrcController;
use app\controllers\VilleController;
use app\controllers\DashboardController;
use app\controllers\BesoinController;
use app\controllers\ArticleController;
use app\controllers\DonController;
use app\controllers\DistributoinController;
use app\controllers\ConfigController;
use app\controllers\AchatController;
use app\controllers\RecapController;
use flight\Engine;
use flight\net\Router;

/** 
 * @var Router $router 
 * @var Engine $app
 */

// Routes BNGRC - Suivi des collectes et distributions de dons
$router->group('', function (Router $router) use ($app) {

    // Page d'accueil - Tableau de bord
    $router->get('/', function () use ($app) {
        $controller = new DashboardController();
        $controller->dashboard();
    });

    $router->group('/besoins', function (Router $router) use ($app) {
        $besoin_controller = new BesoinController();

        // Formulaire d'ajout de besoin
        $router->get('/', [$besoin_controller, 'ajouterForm']);

        // Formulaire d'ajout de besoin avec ville pré-remplie
        $router->get('/@villeId', [$besoin_controller, 'ajouterForm']);

        // Enregistrer un besoin
        $router->post('/', [$besoin_controller, 'ajouterSubmit']);

        // Ajouter un nouvel article (AJAX)
        $router->post('/article', [$besoin_controller, 'ajouterArticleAjax']);
        
    });

    $router->group('/articles', function (Router $router) use ($app) {
        $article_controller = new ArticleController();

        // Formulaire d'ajout d'article
        $router->get('/', [$article_controller, 'add']);

        // Enregistrer un article
        $router->post('/', [$article_controller, 'add']);

        
    });

    // Gestion des dons
    $router->group('/dons', function (Router $router) use ($app) {
        $don_controller = new DonController();

        // Liste des dons
        $router->get('/', [$don_controller, 'list']);

        // Formulaire d'ajout de don
        $router->get('/ajouter', [$don_controller, 'add']);

        // Enregistrer un don
        $router->post('/ajouter', [$don_controller, 'add']);
    });

 
    

    // Gestion des villes
    $router->group('/villes', function (Router $router) use ($app) {
        $ville_controller = new VilleController();

        // Liste des villes
        $router->get('/', [$ville_controller, 'list']);

        // Formulaire d'ajout de ville
        $router->get('/ajouter', [$ville_controller, 'add']);

        // Traitement de l'ajout de ville
        $router->post('/ajouter', [$ville_controller, 'add']);

        // Voir les besoins d'une ville
        $router->get('/@id/besoins', [$ville_controller, 'besoins']);
    });

    // Gestion des distributions
    $router->group('/distributions', function (Router $router) use ($app) {
        $distrobutoinControleur = new DistributoinController();

        // Liste des distributions (avec filtre optionnel par ville)
        $router->get('/', [$distrobutoinControleur, 'distributions']);
        
        // Simulateur de distribution
        $router->get('/simulateur', [$distrobutoinControleur, 'SimulateurPage']);
        $router->post('/simuler', [$distrobutoinControleur, 'Simulatoin_Distributoin']);
        
        // Valider la distribution (V2)
        $router->post('/valider', [$distrobutoinControleur, 'Valider_Distribution']);
    });

    // ==================== ROUTES V2 ====================

    // Gestion de la configuration
    $router->group('/config', function (Router $router) use ($app) {
        $config_controller = new ConfigController();

        // Page de configuration
        $router->get('/', [$config_controller, 'index']);
        
        // Mettre à jour les frais
        $router->post('/frais', [$config_controller, 'updateFrais']);
        
        // API: Récupérer les frais (Ajax)
        $router->get('/api/frais', [$config_controller, 'getFraisApi']);
    });

    // Gestion des achats (via dons argent)
    $router->group('/achats', function (Router $router) use ($app) {
        $achat_controller = new AchatController();

        // Liste des achats
        $router->get('/', [$achat_controller, 'list']);
        
        // Liste filtrée par ville
        $router->get('/ville/@villeId', [$achat_controller, 'listByVille']);
        
        // Formulaire d'achat
        $router->get('/ajouter', [$achat_controller, 'addForm']);
        
        // Traitement de l'achat
        $router->post('/ajouter', [$achat_controller, 'add']);
        
        // Supprimer un achat
        $router->post('/supprimer/@achatId', [$achat_controller, 'delete']);
        
        // API: Vérifier si article existe dans dons
        $router->get('/api/check-article', [$achat_controller, 'checkArticleApi']);
        
        // API: Calculer montant avec frais
        $router->get('/api/calculer', [$achat_controller, 'calculerMontantApi']);
        
        // API: Récupérer solde argent
        $router->get('/api/solde', [$achat_controller, 'getSoldeApi']);
    });

    // Page de récapitulation
    $router->group('/recap', function (Router $router) use ($app) {
        $recap_controller = new RecapController();

        // Page principale
        $router->get('/', [$recap_controller, 'index']);
        
        // API: Récupérer récap général (Ajax)
        $router->get('/api/general', [$recap_controller, 'getRecapApi']);
        
        // API: Récap par ville
        $router->get('/api/villes', [$recap_controller, 'getRecapParVilleApi']);
        
        // API: Récap par type
        $router->get('/api/types', [$recap_controller, 'getRecapParTypeApi']);
        
        // API: Récap dons
        $router->get('/api/dons', [$recap_controller, 'getRecapDonsApi']);
        
        // API: Dashboard complet (bouton actualiser)
        $router->get('/api/dashboard', [$recap_controller, 'getDashboardApi']);
    });

});
