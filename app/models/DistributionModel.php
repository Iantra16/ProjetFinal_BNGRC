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

            $ListeDistributoin = array_merge(
                $ListeDistributoin,
                $this->DistributeDonProportionnel($dons, $besoins)
            );
        }
        return $ListeDistributoin;
    }

    private function DistributeDonProportionnel($don, $besoins) {
        $liste = [];
        $stock_restant = $don['stock_restant'];

        // On boucle tant qu'il reste du stock et que les besoins ne sont pas vides
        while ($stock_restant > 0 && !empty($besoins)) {
            $total_besoins = 0;
            foreach ($besoins as $besoin) {
                $total_besoins += $besoin['reste_a_combler'];
            }

            if ($total_besoins <= 0) {
                break;
            }

            $total_distribue = 0;

            foreach ($besoins as $index => $besoin) {
                if ($stock_restant <= 0) {
                    break;
                }

                $proportion = ($besoin['reste_a_combler'] * $stock_restant) / $total_besoins;
                $distribuer = (int) floor($proportion);

                if ($distribuer > 0) {
                    $distribuer = min($distribuer, $stock_restant, $besoin['reste_a_combler']);
                    $stock_restant -= $distribuer;
                    $besoins[$index]['reste_a_combler'] -= $distribuer;
                    $total_distribue += $distribuer;

                    $liste[] = [
                        'id_don_article' => $don['id_don_article'],
                        'id_besoin_article' => $besoin['id_besoin_article'],
                        'quantite_attribuee' => $distribuer,
                        'article_nom' => $don['article'],
                        'ville_nom' => $besoin['ville'],
                        'unite' => $don['unite']
                    ];
                }
            }

            // Si aucune distribution n'a ete possible, on stoppe pour eviter une boucle infinie
            if ($total_distribue === 0) {
                break;
            }

            // Retirer les besoins deja combles
            $besoins = array_values(array_filter($besoins, function($besoin) {
                return $besoin['reste_a_combler'] > 0;
            }));
        }

        return $liste;
    }

    public function DistributoinDons_Proportionnelle_Avancee($dons_ville_detaille) { 
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

            $stock_dons = $dons['stock_restant'];

            // Étape 1 : Calcul de la distribution proportionnelle arrondie inférieur + stockage des décimales
            $distributions = [];
            $total_distribue = 0;

            foreach ($besoins as $index => $besoin) {
                $proportion = ($besoin['reste_a_combler'] * $stock_dons) / $total_besoins;
                $distribue = (int) floor($proportion);
                $decimal = $proportion - $distribue;

                $distributions[] = [
                    'index' => $index,
                    'besoin' => $besoin,
                    'distribue' => $distribue,
                    'decimal' => $decimal,
                    'article_nom' => $dons['article'],
                    'ville_nom' => $besoin['ville'],
                    'unite' => $dons['unite'],
                    'id_don_article' => $dons['id_don_article'],
                    'id_besoin_article' => $besoin['id_besoin_article']
                ];

                $total_distribue += $distribue;
            }

            // Étape 2 : Calcul du reste
            $reste = $stock_dons - $total_distribue;

            // Étape 3 : Tri des distributions par décimale décroissante (l'ordre original en cas d'égalité est maintenu grâce à l'index)
            if ($reste > 0) {
                usort($distributions, function($a, $b) {
                    // Comparer les décimales en ordre décroissant
                    if (abs($a['decimal'] - $b['decimal']) > 0.0001) {
                        return $b['decimal'] <=> $a['decimal'];
                    }
                    // En cas d'égalité, garder l'ordre original
                    return $a['index'] <=> $b['index'];
                });

                // Étape 4 : Distribution du reste
                for ($i = 0; $i < $reste && $i < count($distributions); $i++) {
                    $distributions[$i]['distribue'] += 1;
                }

                // Re-trier par index pour garder l'ordre original
                usort($distributions, function($a, $b) {
                    return $a['index'] <=> $b['index'];
                });
            }

            // Étape 5 : Créer la liste de distribution
            foreach ($distributions as $dist) {
                if ($dist['distribue'] > 0) {
                    $ListeDistributoin[] = [
                        'id_don_article' => $dist['id_don_article'],
                        'id_besoin_article' => $dist['id_besoin_article'],
                        'quantite_attribuee' => $dist['distribue'],
                        'article_nom' => $dist['article_nom'],
                        'ville_nom' => $dist['ville_nom'],
                        'unite' => $dist['unite']
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
