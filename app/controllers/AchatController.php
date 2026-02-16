<?php
namespace app\controllers;

use app\models\AchatModel;
use app\models\ConfigModel;
use app\models\BesoinModel;
use app\models\VilleModel;
use Flight;

class AchatController
{
    /**
     * Liste des achats
     */
    public function list()
    {
        $achatModel = new AchatModel(Flight::db());
        $achats = $achatModel->getAllAchats();
        
        $villeModel = new VilleModel(Flight::db());
        $villes = $villeModel->getAll();
        
        $success = $_SESSION['success_message'] ?? '';
        $error = $_SESSION['error_message'] ?? '';
        unset($_SESSION['success_message'], $_SESSION['error_message']);
        
        Flight::render('achat/list', [
            'achats' => $achats,
            'villes' => $villes,
            'success' => $success,
            'error' => $error
        ]);
    }

    /**
     * Liste des achats filtrée par ville
     */
    public function listByVille($villeId)
    {
        $achatModel = new AchatModel(Flight::db());
        $achats = $achatModel->getAchatsByVille($villeId);
        
        $villeModel = new VilleModel(Flight::db());
        $villes = $villeModel->getAll();
        $villeSelectionnee = $villeModel->getById($villeId);
        
        Flight::render('achat/list', [
            'achats' => $achats,
            'villes' => $villes,
            'villeSelectionnee' => $villeSelectionnee
        ]);
    }

    /**
     * Formulaire d'achat (depuis page besoins restants)
     */
    public function addForm()
    {
        $achatModel = new AchatModel(Flight::db());
        $configModel = new ConfigModel(Flight::db());
        $besoinModel = new BesoinModel(Flight::db());
        
        // Dons en argent disponibles
        $donsArgent = $achatModel->getDonsArgentDisponibles();
        
        // Articles achetables (nature + materiaux)
        $articles = $achatModel->getArticlesAchetables();
        
        // Besoins restants
        $besoinsRestants = $besoinModel->getReste_besoin();
        
        // Frais actuels
        $fraisPourcent = $configModel->getFraisAchatPourcent();
        
        $success = $_SESSION['success_message'] ?? '';
        $error = $_SESSION['error_message'] ?? '';
        unset($_SESSION['success_message'], $_SESSION['error_message']);
        
        Flight::render('achat/ajouter', [
            'dons_argent' => $donsArgent,
            'articles' => $articles,
            'besoins_restants' => $besoinsRestants,
            'frais_pourcent' => $fraisPourcent,
            'success' => $success,
            'error' => $error
        ]);
    }

    /**
     * Traitement de l'achat
     */
    public function add()
    {
        $idDonArticle = isset($_POST['id_don_article']) ? (int)$_POST['id_don_article'] : 0;
        $idArticle = isset($_POST['id_article']) ? (int)$_POST['id_article'] : 0;
        $quantite = isset($_POST['quantite']) ? floatval($_POST['quantite']) : 0;
        
        // Validation basique
        if ($idDonArticle <= 0 || $idArticle <= 0 || $quantite <= 0) {
            $_SESSION['error_message'] = "Veuillez remplir tous les champs correctement.";
            Flight::redirect('/achats/ajouter');
            return;
        }
        
        $achatModel = new AchatModel(Flight::db());
        $configModel = new ConfigModel(Flight::db());
        $fraisPourcent = $configModel->getFraisAchatPourcent();
        
        try {
            $achatId = $achatModel->effectuerAchat($idDonArticle, $idArticle, $quantite, $fraisPourcent);
            $_SESSION['success_message'] = "✅ Achat effectué avec succès ! (ID: " . $achatId . ")";
            Flight::redirect('/achats');
        } catch (\Exception $e) {
            $_SESSION['error_message'] = "❌ " . $e->getMessage();
            Flight::redirect('/achats/ajouter');
        }
    }

    /**
     * Supprimer un achat
     */
    public function delete($achatId)
    {
        $achatModel = new AchatModel(Flight::db());
        
        try {
            $achatModel->deleteAchat($achatId);
            $_SESSION['success_message'] = "✅ Achat supprimé.";
        } catch (\Exception $e) {
            $_SESSION['error_message'] = "❌ Erreur: " . $e->getMessage();
        }
        
        Flight::redirect('/achats');
    }

    /**
     * API: Vérifier si un article existe dans les dons restants
     */
    public function checkArticleApi()
    {
        $idArticle = isset($_GET['id_article']) ? (int)$_GET['id_article'] : 0;
        
        $achatModel = new AchatModel(Flight::db());
        $existe = $achatModel->articleExisteDansDonsRestants($idArticle);
        
        Flight::json([
            'success' => true,
            'existe_dans_dons' => $existe,
            'message' => $existe 
                ? "⚠️ Cet article existe encore dans les dons restants. Utilisez d'abord les dons existants."
                : "✅ Article disponible pour achat."
        ]);
    }

    /**
     * API: Calculer le montant avec frais
     */
    public function calculerMontantApi()
    {
        $idArticle = isset($_GET['id_article']) ? (int)$_GET['id_article'] : 0;
        $quantite = isset($_GET['quantite']) ? floatval($_GET['quantite']) : 0;
        
        // Récupérer le prix unitaire
        $db = Flight::db();
        $sql = $db->prepare("SELECT prix_unitaire FROM article WHERE id = ?");
        $sql->execute([$idArticle]);
        $article = $sql->fetch();
        
        if (!$article) {
            Flight::json(['success' => false, 'message' => 'Article introuvable'], 404);
            return;
        }
        
        $configModel = new ConfigModel($db);
        $fraisPourcent = $configModel->getFraisAchatPourcent();
        
        $prixUnitaire = floatval($article['prix_unitaire']);
        $montantHT = $quantite * $prixUnitaire;
        $montantFrais = $montantHT * ($fraisPourcent / 100);
        $montantTotal = $montantHT + $montantFrais;
        
        Flight::json([
            'success' => true,
            'prix_unitaire' => $prixUnitaire,
            'quantite' => $quantite,
            'montant_ht' => $montantHT,
            'frais_pourcent' => $fraisPourcent,
            'montant_frais' => $montantFrais,
            'montant_total' => $montantTotal
        ]);
    }

    /**
     * API: Récupérer le solde argent disponible
     */
    public function getSoldeApi()
    {
        $idDonArticle = isset($_GET['id_don_article']) ? (int)$_GET['id_don_article'] : 0;
        
        $achatModel = new AchatModel(Flight::db());
        $solde = $achatModel->getSoldeArgentDisponible($idDonArticle);
        
        Flight::json([
            'success' => true,
            'solde_disponible' => $solde
        ]);
    }
}
