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

    public function distributions() {
        $villeId = isset($_GET['ville']) ? (int)$_GET['ville'] : null;
        
        // Récupérer les distributions avec détails
        $db = Flight::db();
        
        if ($villeId) {
            // Récupérer les informations de la ville
            $villeModel = new \app\models\VilleModel($db);
            $ville = $villeModel->getById($villeId);
            
            if (!$ville) {
                Flight::redirect('/');
                return;
            }
            
            // Récupérer les distributions pour cette ville
            $sql = $db->prepare(
                "SELECT 
                    d.id,
                    d.quantite_attribuee,
                    d.date_distribution,
                    v.id AS ville_id,
                    v.nom AS ville_nom,
                    a.nom AS article_nom,
                    a.prix_unitaire,
                    a.unite,
                    don.donateur,
                    (d.quantite_attribuee * a.prix_unitaire) AS valeur_totale
                FROM distribution d
                JOIN besoin_article ba ON d.id_besoin_article = ba.id
                JOIN besoin b ON ba.id_besoin = b.id
                JOIN ville v ON b.id_ville = v.id
                JOIN don_article da ON d.id_don_article = da.id
                JOIN article a ON da.id_article = a.id
                JOIN don ON da.id_don = don.id
                WHERE v.id = ?
                ORDER BY d.date_distribution DESC"
            );
            $sql->execute([$villeId]);
            $distributions = $sql->fetchAll(\PDO::FETCH_ASSOC);
            
            Flight::render('distribution/distributions', [
                'ville' => $ville,
                'distributions' => $distributions,
                'villeFilter' => $villeId
            ]);
        } else {
            // Récupérer toutes les distributions
            $sql = $db->prepare(
                "SELECT 
                    d.id,
                    d.quantite_attribuee,
                    d.date_distribution,
                    v.id AS ville_id,
                    v.nom AS ville_nom,
                    a.nom AS article_nom,
                    a.prix_unitaire,
                    a.unite,
                    don.donateur,
                    (d.quantite_attribuee * a.prix_unitaire) AS valeur_totale
                FROM distribution d
                JOIN besoin_article ba ON d.id_besoin_article = ba.id
                JOIN besoin b ON ba.id_besoin = b.id
                JOIN ville v ON b.id_ville = v.id
                JOIN don_article da ON d.id_don_article = da.id
                JOIN article a ON da.id_article = a.id
                JOIN don ON da.id_don = don.id
                ORDER BY d.date_distribution DESC"
            );
            $sql->execute();
            $distributions = $sql->fetchAll(\PDO::FETCH_ASSOC);
            
            Flight::render('distribution/distributions', [
                'ville' => null,
                'distributions' => $distributions,
                'villeFilter' => null
            ]);
        }
    }

}