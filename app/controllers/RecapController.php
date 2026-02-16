<?php
namespace app\controllers;

use app\models\RecapModel;
use Flight;

class RecapController
{
    /**
     * Page de récapitulation
     */
    public function index()
    {
        $recapModel = new RecapModel(Flight::db());
        
        $recap = $recapModel->getRecapitulatif();
        $recapParVille = $recapModel->getRecapitulatifParVille();
        $recapParType = $recapModel->getRecapitulatifParType();
        $recapDons = $recapModel->getRecapitulatifDons();
        
        Flight::render('recap/index', [
            'recap' => $recap,
            'recap_par_ville' => $recapParVille,
            'recap_par_type' => $recapParType,
            'recap_dons' => $recapDons
        ]);
    }

    /**
     * API: Récapitulatif général (pour Ajax)
     */
    public function getRecapApi()
    {
        $recapModel = new RecapModel(Flight::db());
        $recap = $recapModel->getRecapitulatif();
        
        Flight::json([
            'success' => true,
            'data' => $recap
        ]);
    }

    /**
     * API: Récapitulatif par ville (pour Ajax)
     */
    public function getRecapParVilleApi()
    {
        $recapModel = new RecapModel(Flight::db());
        $data = $recapModel->getRecapitulatifParVille();
        
        Flight::json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * API: Récapitulatif par type (pour Ajax)
     */
    public function getRecapParTypeApi()
    {
        $recapModel = new RecapModel(Flight::db());
        $data = $recapModel->getRecapitulatifParType();
        
        Flight::json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * API: Récapitulatif des dons (pour Ajax)
     */
    public function getRecapDonsApi()
    {
        $recapModel = new RecapModel(Flight::db());
        $data = $recapModel->getRecapitulatifDons();
        
        Flight::json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * API: Tout le dashboard (pour Ajax - bouton actualiser)
     */
    public function getDashboardApi()
    {
        $recapModel = new RecapModel(Flight::db());
        $data = $recapModel->getDashboardData();
        
        Flight::json([
            'success' => true,
            'data' => $data
        ]);
    }
}
