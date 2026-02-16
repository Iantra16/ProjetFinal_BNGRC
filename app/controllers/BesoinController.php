<?php
namespace app\controllers;
use app\models\VilleModel;
use app\models\BesoinModel;


use Flight;

class BesoinController {

    public function ajouterForm() {
        $villeModel = new VilleModel(Flight::db());
        $villes = $villeModel->getAll();
        $besoinModel = new BesoinModel(Flight::db());
        $types_besoin = $besoinModel->getAllTypeBesoin();
        $articles = $besoinModel->getAllArticle();
        Flight::render('besoin/ajouter_besoin', [
            'villes' => $villes,
            'types_besoin' => $types_besoin,
            'articles' => $articles
        ]);
    }

 
}