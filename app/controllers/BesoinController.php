<?php
namespace app\controllers;

use app\models\VilleModel;
use app\models\BesoinModel;


use Flight;

class BesoinController
{

    public function ajouterForm()
    {
        $villeModel = new VilleModel(Flight::db());
        $villes = $villeModel->getAll();
        $besoinModel = new BesoinModel(Flight::db());
        $types_besoin = $besoinModel->getAllTypeBesoin();
        $articles = $besoinModel->getAllArticle();
        
        // Récupérer le message de succès de la session s'il existe
        $success = '';
        if (isset($_SESSION['success_message'])) {
            $success = $_SESSION['success_message'];
            unset($_SESSION['success_message']);
        }
        
        Flight::render('besoin/ajouter_besoin', [
            'villes' => $villes,
            'types_besoin' => $types_besoin,
            'articles' => $articles,
            'success' => $success
        ]);
    }

    public function ajouterSubmit()
    {
        $db = Flight::db();
        $villeModel = new VilleModel($db);
        $besoinModel = new BesoinModel($db);
        $villes = $villeModel->getAll();
        $types_besoin = $besoinModel->getAllTypeBesoin();
        $articles = $besoinModel->getAllArticle();

        $idVille = isset($_POST['id_ville']) ? (int)$_POST['id_ville'] : 0;
        $idTypeBesoin = isset($_POST['type_besoin_principal']) ? (int)$_POST['type_besoin_principal'] : 0;

        // Trouver le libellé du type sélectionné
        $typeLibelle = '';
        foreach ($types_besoin as $tb) {
            if ($tb['id'] == $idTypeBesoin) {
                $typeLibelle = strtolower($tb['libelle']);
                break;
            }
        }

        if ($idVille <= 0 || $idTypeBesoin <= 0) {
            Flight::render('besoin/ajouter_besoin', [
                'villes' => $villes,
                'types_besoin' => $types_besoin,
                'articles' => $articles,
                'error' => "Veuillez sélectionner une ville et un type de besoin."
            ]);
            return;
        }

        $idArticle = 0;
        $quantite = 0;

        if ($typeLibelle === 'argent') {
            $montant = isset($_POST['somme_argent']) ? (float)$_POST['somme_argent'] : 0;
            if ($montant <= 0) {
                Flight::render('besoin/ajouter_besoin', [
                    'villes' => $villes,
                    'types_besoin' => $types_besoin,
                    'articles' => $articles,
                    'error' => "Veuillez entrer une somme d'argent valide."
                ]);
                return;
            }

            // Gestion de l'article "Argent"
            // On cherche s'il existe déjà un article "Argent" pour ce type
            // Sinon on le crée
            $articleArgent = null;
            foreach ($articles as $art) {
                if ($art['id_type_besoin'] == $idTypeBesoin && strtolower($art['nom']) === 'argent') {
                    $articleArgent = $art;
                    break;
                }
            }

            if ($articleArgent) {
                $idArticle = $articleArgent['id'];
            }
            else {
                // Création automatique de l'article Argent
                $idArticle = $besoinModel->createArticle('Argent', $idTypeBesoin, 1, 'Ar');
            }
            $quantite = $montant;

        }
        else {
            // Nature ou Matériaux
            $idArticle = isset($_POST['id_article_existant']) ? (int)$_POST['id_article_existant'] : 0;
            $quantite = isset($_POST['quantite']) ? (float)$_POST['quantite'] : 0;

            if ($idArticle <= 0 || $quantite <= 0) {
                Flight::render('besoin/ajouter_besoin', [
                    'villes' => $villes,
                    'types_besoin' => $types_besoin,
                    'articles' => $articles,
                    'error' => "Tous les champs sont obligatoires."
                ]);
                return;
            }
        }

        $besoinId = $besoinModel->createBesoin($idVille);
        $besoinModel->addArticleToBesoin($besoinId, $idArticle, $quantite);

        // Déterminer le message de succès selon le type
        if ($typeLibelle === 'argent') {
            $_SESSION['success_message'] = "✅ Le besoin en argent de " . number_format($quantite, 0, ',', ' ') . " Ar a été enregistré avec succès !";
        } else {
            $villeData = array_filter($villes, fn($v) => $v['id'] == $idVille);
            $villeName = array_pop($villeData)['nom'] ?? 'la ville';
            $_SESSION['success_message'] = "✅ Le besoin pour " . $villeName . " a été enregistré avec succès !";
        }

        Flight::redirect('/besoins');
    }

    public function ajouterArticleAjax()
    {
        $nom = trim($_POST['nom'] ?? '');
        $idTypeBesoin = isset($_POST['id_type_besoin']) ? (int)$_POST['id_type_besoin'] : 0;
        $prix = isset($_POST['prix_unitaire']) ? (float)$_POST['prix_unitaire'] : 0;
        $unite = trim($_POST['unite'] ?? '');

        if ($nom === '' || $idTypeBesoin <= 0 || $prix <= 0 || $unite === '') {
            Flight::json([
                'success' => false,
                'message' => 'Champs invalides pour le nouvel article.'
            ], 400);
            return;
        }

        $besoinModel = new BesoinModel(Flight::db());
        $idArticle = (int)$besoinModel->createArticle($nom, $idTypeBesoin, $prix, $unite);

        Flight::json([
            'success' => true,
            'article' => [
                'id' => $idArticle,
                'nom' => $nom,
                'id_type_besoin' => $idTypeBesoin,
                'prix_unitaire' => $prix,
                'unite' => $unite
            ]
        ]);
    }


}