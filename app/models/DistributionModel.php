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
            "INSERT INTO distribution (id_don_article, id_besoin_article , quantite_attribuee ) VALUES (?, ? , ?)"
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

    public function DistributoinDons_By_Date($dons_restant) { 
        $ListeDistributoin = [];
        foreach ($dons_restant as $dons) {
            $besoinModel = new BesoinModel($this->db);
            $besoins = $besoinModel->getReste_besoin_by_article_Date($dons['id_article']);
            $nb_besoin = count($besoins);

            // Correction : Éviter la division par zéro si aucun besoin n'existe pour cet article
            if ($nb_besoin === 0) {
                continue;
            }

            foreach ($besoins as $besoin) {
                if ($dons['stock_restant'] <= 0) break;
                
                // Logique de distribution
                $distribuer = 0;

                if ($dons['stock_restant'] >= $besoin['reste_a_combler']) {
                    // Si le stock moyen suffit pour combler le besoin
                    $distribuer = $besoin['reste_a_combler'];
                }
                else {
                    $distribuer = $dons['stock_restant'];
                }
                
                // On n'ajoute que si on distribue effectivement quelque chose
                if ($distribuer > 0) {
                    $dons['stock_restant'] -= $distribuer;
                    $ListeDistributoin[] = [
                        'id_don_article' => $dons['id_don_article'],
                        'id_besoin_article' => $besoin['id_besoin_article'],
                        'quantite_attribuee' => $distribuer,
                        'article_nom' => $dons['article'],
                        'ville_nom' => $besoin['ville'],
                        'unite' => $dons['unite']
                    ];
                }

            }
        }
        return $ListeDistributoin;
    }

    public function DistributoinDons_By_croissant($dons_ville_detaille) { 
        $ListeDistributoin = [];
        foreach ($dons_ville_detaille as $dons) {
            $besoinModel = new BesoinModel($this->db);
            $besoins = $besoinModel->getReste_besoin_by_article_croisant($dons['id_article']);
            $nb_ville = count($besoins);

            // Correction : Éviter la division par zéro si aucun besoin n'existe pour cet article
            if ($nb_ville === 0) {
                continue;
            }

            $moyen = floor($dons['stock_restant'] / $nb_ville);
            $id = 1;

            foreach ($besoins as $besoin) {
                if ($dons['stock_restant'] <= 0) break;

                // Logique de distribution
                $distribuer = 0;

                if ($dons['stock_restant'] >= $besoin['reste_a_combler']) {
                    // Si le stock moyen suffit pour combler le besoin
                    $distribuer = $besoin['reste_a_combler'];
                }
                else {
                    $distribuer = $dons['stock_restant'];
                }
                
                
                // On n'ajoute que si on distribue effectivement quelque chose
                if ($distribuer > 0) {
                    $dons['stock_restant'] -= $distribuer;
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
    
    public function DistributoinDons_Moyenne($dons_ville_detaille) { 
        $ListeDistributoin = [];
        foreach ($dons_ville_detaille as $dons) {
            $besoinModel = new BesoinModel($this->db);
            $besoins = $besoinModel->getReste_besoin_by_article_croisant($dons['id_article']);
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

    public function DistributoinDons_Proportionnelle($dons_ville_detaille) { 
        $ListeDistributoin = [];
        
        foreach ($dons_ville_detaille as $dons) {
            $besoinModel = new BesoinModel($this->db);
            $besoins = $besoinModel->getReste_besoin_by_article_Date($dons['id_article']);
            
            // Éviter la division par zéro si aucun besoin n'existe pour cet article
            if (empty($besoins)) {
                continue;
            }

            // Calcul du total des besoins pour cet article
            $total_besoins = 0;
            foreach ($besoins as $besoin) {
                $total_besoins += $besoin['reste_a_combler'];
            }

            // Éviter la division par zéro
            if ($total_besoins <= 0) {
                continue;
            }

            // Calcul du coefficient de proportion
            $coefficient = $dons['stock_restant'] / $total_besoins;

            // Trier les besoins par ordre croissant (on donne d'abord ce qui demande moins)
            usort($besoins, function($a, $b) {
                return $a['reste_a_combler'] <=> $b['reste_a_combler'];
            });

            // Distribution proportionnelle
            foreach ($besoins as $besoin) {
                // Calcul de la proportion à distribuer
                $proportion = $besoin['reste_a_combler'] * $coefficient;
                
                // Arrondir avec int (tronquer la partie décimale)
                $distribuer = (int) $proportion;
                
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
            }
        }
        return $ListeDistributoin;
    }

    public function DistributeDons($dons_disponibles, $type) {
        switch ($type) {
            case 'Niv1':
                return $this->DistributoinDons_By_Date($dons_disponibles);
            case 'Niv2':
                return $this->DistributoinDons_By_croissant($dons_disponibles);
            case 'Niv3':
                return $this->DistributoinDons_Proportionnelle($dons_disponibles);
            default:
                return $this->DistributoinDons_By_Date($dons_disponibles);
        }
    }


}
