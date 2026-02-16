<?php

use app\controllers\BngrcController;
use app\controllers\VilleController;
use app\controllers\DashboardController;
use app\controllers\BesoinController;
use app\controllers\ArticleController;
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
    
    // // Gestion des besoins par ville
    // $router->get('/besoins', function () use ($app) {
    //     $controller = new BngrcController();
    //     $controller->besoins();
    // });

    // // Ajouter un besoin
    // $router->get('/besoins/ajouter', function () use ($app) {
    //     $controller = new BngrcController();
    //     $controller->ajouterBesoin();
    // });

    // $router->post('/besoins/ajouter', function () use ($app) {
    //     $controller = new BngrcController();
    //     $controller->ajouterBesoin();
    // });

    // Gestion des dons
    $router->get('/dons', function () use ($app) {
        $controller = new BngrcController();
        $controller->dons();
    });

    // Ajouter un don
    $router->get('/dons/ajouter', function () use ($app) {
        $controller = new BngrcController();
        $controller->ajouterDon();
    });

    $router->post('/dons/ajouter', function () use ($app) {
        $controller = new BngrcController();
        $controller->ajouterDon();
    });

    // Simulation des distributions
    $router->get('/distributions', function () use ($app) {
        $controller = new BngrcController();
        $controller->distributions();
    });

    $router->post('/distributions/simuler', function () use ($app) {
        $controller = new BngrcController();
        $controller->simulerDistributions();
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
    });

    // Sélectionner une ville pour gérer ses besoins
    $router->get('/villes/besoins', function () use ($app) {
        $controller = new BngrcController();
        $controller->besoinVille();
    });

    // Afficher les besoins d'une ville spécifique
    $router->get('/villes/@id:[0-9]+/besoins', function ($id) use ($app) {
        $controller = new BngrcController();
        $controller->besoinVille($id);
    });

    // Formulaire d'ajout de besoin pour une ville spécifique
    $router->get('/villes/@id:[0-9]+/besoins/ajouter', function ($id) use ($app) {
        $controller = new BngrcController();
        $controller->ajouterBesoinVille($id);
    });

    // Traitement de l'ajout de besoin pour une ville spécifique
    $router->post('/villes/@id:[0-9]+/besoins/ajouter', function ($id) use ($app) {
        $controller = new BngrcController();
        $controller->ajouterBesoinVille($id);
    });

});
