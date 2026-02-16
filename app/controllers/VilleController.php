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

    public function besoins($id) {
        $vM = new VilleModel(Flight::db());
        $ville = $vM->getById($id);
        
        if (!$ville) {
            Flight::redirect('/villes');
            return;
        }
        
        $bM = new \app\models\BesoinModel(Flight::db());
        $besoins = $bM->getBesoinsByVille($id);
        
        Flight::render('ville/besoin_ville', [
            'ville' => $ville,
            'besoins' => $besoins
        ]);
    }


}