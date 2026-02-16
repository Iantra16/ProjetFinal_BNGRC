<?php

namespace app\controllers;

use app\models\VilleModel;
use app\models\BesoinModel;
use app\models\DistributionModel;
use app\models\DonModel;
use Flight;
use flight\util\Json;

class DistributoinController
{


    public function distributions() {

        $villeId = isset($_GET['ville']) ? (int)$_GET['ville'] : null;
        
        // Récupérer les distributions avec détails
        $db = Flight::db();
        $distributionsModel = new DistributionModel($db);

        if ($villeId) {
            // Récupérer les informations de la ville
            $villeModel = new \app\models\VilleModel($db);
            $ville = $villeModel->getById($villeId);
            
            if (!$ville) {
                Flight::redirect('/');
                return;
            }
            
            // Récupérer les distributions pour cette ville
            $distributions = $distributionsModel->Distributoin_VIlle($villeId);
            
            Flight::render('distribution/distributions', [
                'ville' => $ville,
                'distributions' => $distributions,
                'villeFilter' => $villeId
            ]);
        } else {

            // Récupérer toutes les distributions
            $distributions = $distributionsModel->GetAllDistributoin();
            
            Flight::render('distribution/distributions', [
                'ville' => null,
                'distributions' => $distributions,
                'villeFilter' => null
            ]);
        }
    }

    public function Simulateur() {
        $db = Flight::db();
        
        $resteModel = new DonModel($db);
        $restdons = $resteModel->Get_Reste_dons_disponibles();
        
        Flight::render('distribution/simulateur', [
            'restdons' => $restdons
        ]);
    }

    // public function Simulatoin_Distributoin() {
    //     $db = Flight::db();
    //     $distributionsModel = new DistributionModel($db);
    //     $distributions = $distributionsModel->GetAllDistributoin();
        
    //     return Json::encode($distributions);
    // }
    public function Simulatoin_Distributoin() {
        $db = Flight::db();
        
        $donModel = new DonModel($db);
        $stockDisponible = $donModel->Get_Reste_dons_disponibles();
        
        $distributionsModel = new DistributionModel($db);
        $simulation = $distributionsModel->DistributoinDons($stockDisponible);
        
        Flight::json($simulation);
    }
}
