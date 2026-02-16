<?php

namespace app\controllers;

use app\models\VilleModel;
use app\models\BesoinModel;
use app\models\DonModel;
use Flight;

class DashboardController
{
    public function dashboard() {
        $db = Flight::db();
        
        // Récupérer les villes avec informations région
        $vM = new VilleModel($db);
        $villes = $vM->getAll();
        $totalVilles = count($villes);
        
        // Récupérer tous les besoins avec leurs articles
        $bM = new BesoinModel($db);
        $besoins = $bM->getAllBesoins();
        
        // Calculer la valeur totale de chaque besoin
        foreach ($besoins as &$besoin) {
            $besoin['valeur_totale'] = 0;
            if (isset($besoin['articles']) && is_array($besoin['articles'])) {
                foreach ($besoin['articles'] as $article) {
                    $besoin['valeur_totale'] += $article['quantite'] * $article['prix_unitaire'];
                }
            }
        }
        
        // Récupérer tous les dons avec leurs articles
        $dM = new DonModel($db);
        $dons = $dM->getAllDons();
        
        // Récupérer les distributions avec informations complètes
        $distributions = $this->getDistributionsWithDetails($db);
        
        Flight::render('dashboard', [
            'totalVilles' => $totalVilles,
            'villes' => $villes,
            'besoins' => $besoins,
            'dons' => $dons,
            'distributions' => $distributions
        ]);
    }
    
    /**
     * Récupérer les distributions avec détails ville, article, quantité et valeur
     */
    private function getDistributionsWithDetails($db) {
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
                (d.quantite_attribuee * a.prix_unitaire) AS valeur_totale
            FROM distribution d
            JOIN besoin_article ba ON d.id_besoin_article = ba.id
            JOIN besoin b ON ba.id_besoin = b.id
            JOIN ville v ON b.id_ville = v.id
            JOIN don_article da ON d.id_don_article = da.id
            JOIN article a ON da.id_article = a.id
            ORDER BY d.date_distribution DESC"
        );
        $sql->execute();
        return $sql->fetchAll(\PDO::FETCH_ASSOC);
    }
}
