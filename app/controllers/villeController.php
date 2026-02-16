<?php
namespace app\controllers;
use app\models\VilleModel;


use Flight;

class VilleController {
        
    public function add() {
        $vM = new VilleModel(Flight::db());
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = $_POST['nom'];
            $idRegion = $_POST['id_region'];
            $vM->insert($nom, $idRegion);
            Flight::render('/ville/list');
        } else {
            Flight::render('/ville/form');
        }
    }

    public function list() {
        $vM = new VilleModel(Flight::db());
        $villes = $vM->getAll();
        Flight::render('/ville/list', ['villes' => $villes]);
    }

}