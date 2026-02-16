<?php

use app\controllers\BngrcController;
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

});
