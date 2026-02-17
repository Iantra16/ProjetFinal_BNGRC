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
            $villeModel = new VilleModel($db);
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
        
        $type = Flight::request()->data->type ?? 'Niv1';
        
        $donModel = new DonModel($db);
        $stockDisponible = $donModel->Get_Reste_dons_disponibles();
        
        $distributionsModel = new DistributionModel($db);
        $simulation = $distributionsModel->DistributeDons($stockDisponible, $type);
        
        Flight::json($simulation);
    }

    /**
     * Valider et enregistrer la distribution (dispatch réel)
     */
    public function Valider_Distribution() {
        $db = Flight::db();
        
        $data = Flight::request()->data;
        $type = $data->type ?? 'Niv1';
        
        $donModel = new DonModel($db);
        $stockDisponible = $donModel->Get_Reste_dons_disponibles();
        
        $distributionsModel = new DistributionModel($db);
        $distributions = $distributionsModel->DistributeDons($stockDisponible, $type);
        
        if (empty($distributions)) {
            Flight::json([
                'success' => false,
                'message' => 'Aucune distribution à effectuer.'
            ]);
            return;
        }
        
        try {
            $db->beginTransaction();
            
            $count = 0;
            foreach ($distributions as $dist) {
                $sql = $db->prepare(
                    "INSERT INTO distribution (id_don_article, id_besoin_article, quantite_attribuee) 
                     VALUES (?, ?, ?)"
                );
                $sql->execute([
                    $dist['id_don_article'],
                    $dist['id_besoin_article'],
                    $dist['quantite_attribuee']
                ]);
                $count++;
            }
            
            $db->commit();
            
            Flight::json([
                'success' => true,
                'message' => '✅ Distribution validée ! ' . $count . ' attribution(s) effectuée(s).',
                'count' => $count
            ]);
            
        } catch (\Exception $e) {
            $db->rollBack();
            Flight::json([
                'success' => false,
                'message' => '❌ Erreur: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Page de simulation avec boutons Simuler et Valider
     */
    public function SimulateurPage() {
        $db = Flight::db();
        
        $donModel = new DonModel($db);
        $restdons = $donModel->Get_Reste_dons_disponibles();
        
        $besoinModel = new BesoinModel($db);
        $besoinsRestants = $besoinModel->getReste_besoin();
        
        $success = $_SESSION['success_message'] ?? '';
        $error = $_SESSION['error_message'] ?? '';
        unset($_SESSION['success_message'], $_SESSION['error_message']);
        
        Flight::render('distribution/simulateur', [
            'restdons' => $restdons,
            'besoins_restants' => $besoinsRestants,
            'success' => $success,
            'error' => $error
        ]);
    }
}
