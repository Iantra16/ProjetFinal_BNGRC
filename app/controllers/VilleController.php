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
            // Redirection après insertion
            Flight::redirect('/villes/');
        } else {
            Flight::render('ville/ajouter_ville', ['regions' => $vM->getRegions()]);
        }
    }

    public function list() {
        $vM = new VilleModel(Flight::db());
        $villes = $vM->getAll();
        Flight::render('ville/villes', ['villes' => $villes]);
    }

    public function dashboard() {
        $vM = new VilleModel(Flight::db());
        $totalVilles = $vM->count();
        Flight::render('dashboard', ['totalVilles' => $totalVilles]);
    }

}