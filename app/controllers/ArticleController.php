<?php
namespace app\controllers;
use app\models\ArticleModel;
use app\models\BesoinModel;


use Flight;

class ArticleController {
        
    public function add() {
        $aM = new ArticleModel(Flight::db());
        
        // Initialiser les variables
        $success = '';
        $error = '';
        $nomArticle = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $nom = $_POST['nom'] ?? '';
                $prixU = $_POST['prix_unitaire'] ?? '';
                $unite = $_POST['unite'] ?? '';
                $idTypeBesoin = $_POST['id_type_besoin'] ?? '';
                
                // Validation des champs
                if (empty($nom) || empty($prixU) || empty($unite) || empty($idTypeBesoin)) {
                    $error = "Tous les champs sont obligatoires.";
                    $nomArticle = $nom; // Garder la saisie en cas d'erreur
                } else {
                    // Insertion de l'article
                    $aM->insert($nom, $prixU, $unite, $idTypeBesoin);
                    $success = "✅ L'article \"" . htmlspecialchars($nom) . "\" a été enregistré avec succès !";
                    // Vider le formulaire après succès
                    $_POST = [];
                }
            } catch (\Exception $e) {
                $error = "Une erreur s'est produite lors de l'enregistrement : " . $e->getMessage();
                $nomArticle = $_POST['nom'] ?? '';
            }
        }
        
        Flight::render('articles/form', [
            'types_besoin' => $aM->getAllTypeBesoin(),
            'success' => $success,
            'error' => $error
        ]);
    }

    //    public function ajouterForm() {
    //     $villeModel = new VilleModel(Flight::db());
    //     $villes = $villeModel->getAll();
    //     $besoinModel = new BesoinModel(Flight::db());
    //     $types_besoin = $besoinModel->getAllTypeBesoin();
    //     $articles = $besoinModel->getAllArticle();
    //     Flight::render('besoin/ajouter_besoin', [
    //         'villes' => $villes,
    //         'types_besoin' => $types_besoin,
    //         'articles' => $articles
    //     ]);
    // }

    public function list() {
        $aM = new ArticleModel(Flight::db());
        $articles = $aM->getAll();
        Flight::render('article/articles', ['articles' => $articles]);
    }

}