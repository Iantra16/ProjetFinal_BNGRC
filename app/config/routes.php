<?php

use app\controllers\AuthController;
use app\controllers\AdminController;
use app\controllers\ObjetController;
use flight\Engine;
use flight\net\Router;

/** 
 * @var Router $router 
 * @var Engine $app
 */

// Group principal
$router->group('', function (Router $router) use ($app) {

    $router->get('/', function () use ($app) {
        $app->render('/dashboard');
    });


});
