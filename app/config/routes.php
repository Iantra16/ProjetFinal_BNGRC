<?php

use app\controllers\BngrcController;
use app\controllers\VilleController;
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
        $controller = new BngrcController();
        $controller->dashboard();
    });

    // Gestion des besoins par ville
    $router->get('/besoins', function () use ($app) {
        $controller = new BngrcController();
        $controller->besoins();
    });

    // Ajouter un besoin
    $router->get('/besoins/ajouter', function () use ($app) {
        $controller = new BngrcController();
        $controller->ajouterBesoin();
    });

    $router->post('/besoins/ajouter', function () use ($app) {
        $controller = new BngrcController();
        $controller->ajouterBesoin();
    });

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
    
    // $router->group('/user', function (Router $router) use ($app) {
    //     $auth_controller = new AuthController();
    //     $objet_controller = new ObjetController();
    //     //login
    //     $router->get('/login', function () use ($app) {
    //         $app->render('front/login');
    //     });
    //     $router->post('/login',[$auth_controller,'loginUser']);

    //     //inscription
    //     $router->get('/inscription', function () use ($app) {
    //         $app->render('front/inscription');
    //     });
    //     $router->post('/inscription',[$auth_controller,'registerUser']);

    //     // accueil: liste objets
    //     $router->get('/', [$objet_controller, 'findOtherObj']);
    //     $router->get('/listobject', [$objet_controller, 'findOtherObj']);

    //     // mes objets
    //     $router->get('/myobject', [$objet_controller, 'showMyObjects']);
    //     // Déconnexion
    //     $router->get('/logout', function () use ($app) {
    //         $auth_controller = new AuthController();
    //         $auth_controller->logout();
    //     });
    // });

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
