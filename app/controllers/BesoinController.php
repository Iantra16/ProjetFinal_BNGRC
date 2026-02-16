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

    public function ajouterSubmit()
    {
        $db = Flight::db();
        $villeModel = new VilleModel($db);
        $besoinModel = new BesoinModel($db);
        $villes = $villeModel->getAll();
        $types_besoin = $besoinModel->getAllTypeBesoin();
        $articles = $besoinModel->getAllArticle();

        $idVille = isset($_POST['id_ville']) ? (int)$_POST['id_ville'] : 0;
        $modeArticle = $_POST['mode_article'] ?? 'existant';
        $quantite = isset($_POST['quantite']) ? (float)$_POST['quantite'] : 0;

        if ($idVille <= 0 || $quantite <= 0) {
            Flight::render('besoin/ajouter_besoin', [
                'villes' => $villes,
                'types_besoin' => $types_besoin,
                'articles' => $articles,
                'error' => "Ville et quantite sont obligatoires."
            ]);
            return;
        }

        if ($modeArticle === 'nouveau') {
            $idArticleNouveau = isset($_POST['id_article_nouveau']) ? (int)$_POST['id_article_nouveau'] : 0;
            if ($idArticleNouveau > 0) {
                $idArticle = $idArticleNouveau;
            } else {
                $nom = trim($_POST['nouveau_nom'] ?? '');
                $idTypeBesoin = isset($_POST['id_type_besoin_nouveau']) ? (int)$_POST['id_type_besoin_nouveau'] : 0;
                $prix = isset($_POST['nouveau_prix']) ? (float)$_POST['nouveau_prix'] : 0;
                $unite = trim($_POST['nouveau_unite'] ?? '');

                if ($nom === '' || $idTypeBesoin <= 0 || $prix <= 0 || $unite === '') {
                    Flight::render('besoin/ajouter_besoin', [
                        'villes' => $villes,
                        'types_besoin' => $types_besoin,
                        'articles' => $articles,
                        'error' => "Tous les champs du nouvel article sont obligatoires."
                    ]);
                    return;
                }

                $idArticle = (int)$besoinModel->createArticle($nom, $idTypeBesoin, $prix, $unite);
            }
        } else {
            $idArticle = isset($_POST['id_article_existant']) ? (int)$_POST['id_article_existant'] : 0;
            if ($idArticle <= 0) {
                Flight::render('besoin/ajouter_besoin', [
                    'villes' => $villes,
                    'types_besoin' => $types_besoin,
                    'articles' => $articles,
                    'error' => "Veuillez selectionner un article existant."
                ]);
                return;
            }
        }

        $besoinId = $besoinModel->createBesoin($idVille);
        $besoinModel->addArticleToBesoin($besoinId, $idArticle, $quantite);

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