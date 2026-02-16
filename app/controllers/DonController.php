<?php
namespace app\controllers;
use app\models\DonModel;
use app\models\ArticleModel;

use Flight;

class DonController {
    
    public function list(){
        $donModel = new DonModel(Flight::db());
        $dons = $donModel->getAllDons();
        
        Flight::render('don/dons', [
            'dons' => $dons
        ]);
    }
        
    public function addForm(){
        $articleModel = new ArticleModel(Flight::db());
        $categories = $articleModel->getAllTypeBesoin();
        $articles = $articleModel->getAll();
        
        // Organiser les articles par type de besoin
        $articlesByType = [];
        foreach ($articles as $article) {
            $typeId = $article['id_type_besoin'];
            if (!isset($articlesByType[$typeId])) {
                $articlesByType[$typeId] = [];
            }
            $articlesByType[$typeId][] = $article;
        }
        
        Flight::render('don/ajouter_don', [
            'categories' => $categories,
            'articlesByType' => $articlesByType
        ]);
    }

    public function add(){
        // Si c'est GET, afficher le formulaire
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->addForm();
            return;
        }

        // Si c'est POST, traiter les données
        $donateur = $_POST['donateur'] ?? '';
        $articlesData = $_POST['articles'] ?? [];

        // Validation
        if (empty($donateur) || empty($articlesData)) {
            Flight::redirect('/dons/ajouter?error=missing_data');
            return;
        }

        // Préparer les articles pour insertion
        $articlesForDon = [];
        foreach ($articlesData as $articleLine) {
            if (!empty($articleLine['article_id'])) {
                $articlesForDon[] = [
                    'id_article' => $articleLine['article_id'],
                    'quantite' => $articleLine['quantite'] ?? 1
                ];
            }
        }

        if (empty($articlesForDon)) {
            Flight::redirect('/dons/ajouter?error=no_articles');
            return;
        }

        // Créer le don avec ses articles
        try {
            $donModel = new DonModel(Flight::db());
            $donId = $donModel->createDonWithArticles($donateur, $articlesForDon);
            
            // Rediriger vers la liste des dons avec message de succès
            Flight::redirect('/dons?success=don_added');
        } catch (\Exception $e) {
            Flight::redirect('/dons/ajouter?error=database_error');
        }
    }

}