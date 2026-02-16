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

    public function GetAllDistributoin() {
        
            // Récupérer toutes les distributions
            $sql = $this->db->prepare(
                "SELECT * FROM v_historique_distributions_villes"
            );
            $sql->execute();
            $distributions = $sql->fetchAll(\PDO::FETCH_ASSOC);
            return $distributions;
    }

    public function Distributoin_VIlle($id_ville) {
        $sql = $this->db->prepare(
            "SELECT * FROM v_historique_distributions_villes WHERE ville_id = ?"
        );
        $sql->execute([$id_ville]);
        return $sql->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function DistributoinDons($dons_ville_detaille) { 
        $ListeDistributoin = [];
        foreach ($dons_ville_detaille as $dons) {
            $besoinModel = new BesoinModel($this->db);
            $besoins = $besoinModel->getReste_besoin_by_article($dons['id_article']);
            $nb_ville = count($besoins);

            // Correction : Éviter la division par zéro si aucun besoin n'existe pour cet article
            if ($nb_ville === 0) {
                continue;
            }

            $moyen = floor($dons['stock_restant'] / $nb_ville);
            $id = 1;

            foreach ($besoins as $besoin) {
                // Logique de distribution
                $distribuer = 0;

                if ($moyen >= $besoin['reste_a_combler']) {
                    // Si le stock moyen suffit pour combler le besoin
                    $distribuer = $besoin['reste_a_combler'];
                    if ($nb_ville - $id > 0) {
                        $Q1 = $moyen - $besoin['reste_a_combler'];
                        $moyen = $moyen + ($Q1 / ($nb_ville - $id));
                    }
                }
                else {
                    // Si le stock moyen ne suffit pas
                    $distribuer = floor($moyen);
                }
                
                // On n'ajoute que si on distribue effectivement quelque chose
                if ($distribuer > 0) {
                    $ListeDistributoin[] = [
                        'id_don_article' => $dons['id_don_article'],
                        'id_besoin_article' => $besoin['id_besoin_article'],
                        'quantite_attribuee' => $distribuer,
                        'article_nom' => $dons['article'],
                        'ville_nom' => $besoin['ville'],
                        'unite' => $dons['unite']
                    ];
                }

                $id++;
            }
        }
        return $ListeDistributoin;
    }

}
