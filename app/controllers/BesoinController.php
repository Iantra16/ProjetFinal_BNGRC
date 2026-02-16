<?php
namespace app\controllers;

use app\models\VilleModel;
use app\models\BesoinModel;


use Flight;

class BesoinController
{

    public function ajouterForm($villeId = null)
    {
        $villeModel = new VilleModel(Flight::db());
        $villes = $villeModel->getAll();
        $besoinModel = new BesoinModel(Flight::db());
        $types_besoin = $besoinModel->getAllTypeBesoin();
        $articles = $besoinModel->getAllArticle();
        
        // Pré-remplir la ville si elle est fournie
        $villeSelectionnee = null;
        if ($villeId !== null) {
            $villeSelectionnee = $villeModel->getById((int)$villeId);
        }
        
        // Récupérer les messages de la session s'ils existent
        $success = '';
        $error = '';
        if (isset($_SESSION['success_message'])) {
            $success = $_SESSION['success_message'];
            unset($_SESSION['success_message']);
        }
        if (isset($_SESSION['error_message'])) {
            $error = $_SESSION['error_message'];
            unset($_SESSION['error_message']);
        }
        
        Flight::render('besoin/ajouter_besoin', [
            'villes' => $villes,
            'types_besoin' => $types_besoin,
            'articles' => $articles,
            'success' => $success,
            'error' => $error,
            'villeSelectionnee' => $villeSelectionnee
        ]);
    }

    public function ajouterSubmit()
    {
        $db = Flight::db();
        $villeModel = new VilleModel($db);
        $besoinModel = new BesoinModel($db);
        
        $idVille = isset($_POST['id_ville']) ? (int)$_POST['id_ville'] : 0;
        $articlesData = $_POST['articles'] ?? [];

        // Validation de la ville
        if ($idVille <= 0) {
            $_SESSION['error_message'] = "Veuillez sélectionner une ville.";
            Flight::redirect('/besoins');
            return;
        }

        // Validation des articles
        if (empty($articlesData)) {
            $_SESSION['error_message'] = "Veuillez ajouter au moins un article.";
            Flight::redirect('/besoins');
            return;
        }

        // Préparer les articles pour insertion
        $articlesForBesoin = [];
        foreach ($articlesData as $articleLine) {
            if (!empty($articleLine['article_id']) && !empty($articleLine['quantite'])) {
                $articlesForBesoin[] = [
                    'id_article' => (int)$articleLine['article_id'],
                    'quantite' => (float)$articleLine['quantite']
                ];
            }
        }

        if (empty($articlesForBesoin)) {
            $_SESSION['error_message'] = "Aucun article valide n'a été ajouté.";
            Flight::redirect('/besoins');
            return;
        }

        // Créer le besoin avec ses articles
        try {
            $db->beginTransaction();
            
            // Créer le besoin
            $besoinId = $besoinModel->createBesoin($idVille);
            
            // Ajouter les articles au besoin
            foreach ($articlesForBesoin as $article) {
                $besoinModel->addArticleToBesoin($besoinId, $article['id_article'], $article['quantite']);
            }
            
            $db->commit();
            
            // Récupérer le nom de la ville pour le message
            $ville = $villeModel->getById($idVille);
            $villeName = $ville['nom'] ?? 'la ville';
            
            $_SESSION['success_message'] = "✅ Le besoin pour " . $villeName . " a été enregistré avec succès ! (" . count($articlesForBesoin) . " article(s) ajouté(s))";
            Flight::redirect('/besoins');
            
        } catch (\Exception $e) {
            $db->rollBack();
            $_SESSION['error_message'] = "Erreur lors de l'enregistrement : " . $e->getMessage();
            Flight::redirect('/besoins');
        }
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