<?php
namespace app\controllers;

use app\models\ConfigModel;
use Flight;

class ConfigController
{
    /**
     * Afficher la page de configuration
     */
    public function index()
    {
        $configModel = new ConfigModel(Flight::db());
        $fraisPourcent = $configModel->getFraisAchatPourcent();
        
        $success = $_SESSION['success_message'] ?? '';
        $error = $_SESSION['error_message'] ?? '';
        unset($_SESSION['success_message'], $_SESSION['error_message']);
        
        Flight::render('config/index', [
            'frais_pourcent' => $fraisPourcent,
            'success' => $success,
            'error' => $error
        ]);
    }

    /**
     * Mettre à jour les frais d'achat
     */
    public function updateFrais()
    {
        $frais = isset($_POST['frais_pourcent']) ? floatval($_POST['frais_pourcent']) : null;
        
        if ($frais === null || $frais < 0 || $frais > 100) {
            $_SESSION['error_message'] = "Le pourcentage de frais doit être entre 0 et 100.";
            Flight::redirect('/config');
            return;
        }
        
        $configModel = new ConfigModel(Flight::db());
        $configModel->setFraisAchatPourcent($frais);
        
        $_SESSION['success_message'] = "✅ Frais d'achat mis à jour à " . $frais . "%.";
        Flight::redirect('/config');
    }

    /**
     * API: Récupérer les frais actuels (pour Ajax)
     */
    public function getFraisApi()
    {
        $configModel = new ConfigModel(Flight::db());
        Flight::json([
            'success' => true,
            'frais_pourcent' => $configModel->getFraisAchatPourcent()
        ]);
    }
}
