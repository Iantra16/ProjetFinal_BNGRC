<?php

use app\controllers\BngrcController;
use app\controllers\VilleController;
use app\controllers\DashboardController;
use app\controllers\BesoinController;
use app\controllers\ArticleController;
use app\controllers\DonController;
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
    });



});
