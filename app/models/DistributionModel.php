<?php

namespace app\models;

use Flight;
use app\models\BesoinModel;

class DistributionModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function create($id_don_article , $id_besoin_article , $quantite_attribuee) {
        $sql = $this->db->prepare(
            "INSERT INTO distribution_dons (id_don_article, id_besoin_article , quantite_attribuee ) VALUES (?, ? , ?)"
        );
        $sql->execute([$id_don_article, $id_besoin_article , $quantite_attribuee]);
        return $this->db->lastInsertId();
    }

    public function DistributoinDons($dons_ville_detaille) { 
        $ListeDistributoin = [];
        foreach ($dons_ville_detaille as $dons) {
            $besoinModel = new BesoinModel($this->db);
            $besoins = $besoinModel->getReste_besoin_by_article($dons['id_article']);
            $nb_ville = count($besoins);
            $moyen = floor($dons['stock_restant'] / $nb_ville);
            $id = 1;

            foreach ($besoins as $besoin) {
                // Logique de distribution
                $distribuer = 0;

                if ($moyen >= $besoin['reste_a_combler']) {
                    // Si oui, on peut enregistrer la distribution dans la base de données
                    $distribuer = $besoin['reste_a_combler'];
                    $Q1 = $moyen - $besoin['reste_a_combler'];
                    $moyen = $moyen + ($Q1/($nb_ville - $id));
                    
                }
                else {
                    $distribuer = $moyen;
                    $Q1 = 0;
                }
                
                    $distribution[] = [
                        'id_don_article' => $dons['id_don_article'],
                        'id_besoin_article' => $besoin['id_besoin_article'],
                        'quantite_attribuee' => $distribuer
                    ];

                    $ListeDistributoin[] = $distribution;
                    
                    // si inserena anaty base de données
                    // $this->create($dons['id_don_article'], $besoin['id_besoin_article'], $distribuer);

                $id ++;
            }
        }
        return $ListeDistributoin;
    }

}
