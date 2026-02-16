<?php

namespace app\models;

use Flight;

class RecapModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Récupérer le montant total des besoins
     */
    public function getMontantTotalBesoins()
    {
        $sql = $this->db->prepare(
            "SELECT COALESCE(SUM(ba.quantite * a.prix_unitaire), 0) AS total
            FROM besoin_article ba
            JOIN article a ON ba.id_article = a.id"
        );
        $sql->execute();
        $result = $sql->fetch();
        return floatval($result['total']);
    }

    /**
     * Récupérer le montant des besoins satisfaits (distribués)
     */
    public function getMontantBesoinsSatisfaits()
    {
        $sql = $this->db->prepare(
            "SELECT COALESCE(SUM(dist.quantite_attribuee * a.prix_unitaire), 0) AS total
            FROM distribution dist
            JOIN besoin_article ba ON dist.id_besoin_article = ba.id
            JOIN article a ON ba.id_article = a.id"
        );
        $sql->execute();
        $result = $sql->fetch();
        return floatval($result['total']);
    }

    /**
     * Récupérer le montant des besoins restants
     */
    public function getMontantBesoinsRestants()
    {
        $total = $this->getMontantTotalBesoins();
        $satisfaits = $this->getMontantBesoinsSatisfaits();
        return $total - $satisfaits;
    }

    /**
     * Récupérer le récapitulatif complet
     */
    public function getRecapitulatif()
    {
        $total = $this->getMontantTotalBesoins();
        $satisfaits = $this->getMontantBesoinsSatisfaits();
        $restants = $total - $satisfaits;
        
        $pourcentageSatisfait = $total > 0 ? ($satisfaits / $total) * 100 : 0;

        return [
            'montant_total_besoins' => $total,
            'montant_besoins_satisfaits' => $satisfaits,
            'montant_besoins_restants' => $restants,
            'pourcentage_satisfait' => round($pourcentageSatisfait, 2)
        ];
    }

    /**
     * Récapitulatif par ville
     */
    public function getRecapitulatifParVille()
    {
        $sql = $this->db->prepare(
            "SELECT 
                v.id AS ville_id,
                v.nom AS ville_nom,
                r.nom AS region_nom,
                COALESCE(SUM(ba.quantite * a.prix_unitaire), 0) AS montant_total,
                COALESCE((
                    SELECT SUM(dist.quantite_attribuee * a2.prix_unitaire)
                    FROM distribution dist
                    JOIN besoin_article ba2 ON dist.id_besoin_article = ba2.id
                    JOIN article a2 ON ba2.id_article = a2.id
                    JOIN besoin b2 ON ba2.id_besoin = b2.id
                    WHERE b2.id_ville = v.id
                ), 0) AS montant_satisfait
            FROM ville v
            JOIN region r ON v.id_region = r.id
            LEFT JOIN besoin b ON b.id_ville = v.id
            LEFT JOIN besoin_article ba ON ba.id_besoin = b.id
            LEFT JOIN article a ON ba.id_article = a.id
            GROUP BY v.id, v.nom, r.nom
            HAVING montant_total > 0
            ORDER BY v.nom"
        );
        $sql->execute();
        $villes = $sql->fetchAll();

        // Calculer le reste pour chaque ville
        foreach ($villes as &$ville) {
            $ville['montant_restant'] = $ville['montant_total'] - $ville['montant_satisfait'];
            $ville['pourcentage_satisfait'] = $ville['montant_total'] > 0 
                ? round(($ville['montant_satisfait'] / $ville['montant_total']) * 100, 2) 
                : 0;
        }

        return $villes;
    }

    /**
     * Récapitulatif par type de besoin
     */
    public function getRecapitulatifParType()
    {
        $sql = $this->db->prepare(
            "SELECT 
                t.id AS type_id,
                t.libelle AS type_libelle,
                COALESCE(SUM(ba.quantite * a.prix_unitaire), 0) AS montant_total,
                COALESCE((
                    SELECT SUM(dist.quantite_attribuee * a2.prix_unitaire)
                    FROM distribution dist
                    JOIN besoin_article ba2 ON dist.id_besoin_article = ba2.id
                    JOIN article a2 ON ba2.id_article = a2.id
                    WHERE a2.id_type_besoin = t.id
                ), 0) AS montant_satisfait
            FROM type_besoin t
            LEFT JOIN article a ON a.id_type_besoin = t.id
            LEFT JOIN besoin_article ba ON ba.id_article = a.id
            GROUP BY t.id, t.libelle
            ORDER BY t.libelle"
        );
        $sql->execute();
        $types = $sql->fetchAll();

        foreach ($types as &$type) {
            $type['montant_restant'] = $type['montant_total'] - $type['montant_satisfait'];
            $type['pourcentage_satisfait'] = $type['montant_total'] > 0 
                ? round(($type['montant_satisfait'] / $type['montant_total']) * 100, 2) 
                : 0;
        }

        return $types;
    }

    /**
     * Récapitulatif des dons
     */
    public function getRecapitulatifDons()
    {
        // Total des dons reçus
        $sql = $this->db->prepare(
            "SELECT COALESCE(SUM(da.quantite * a.prix_unitaire), 0) AS total
            FROM don_article da
            JOIN article a ON da.id_article = a.id"
        );
        $sql->execute();
        $totalDons = floatval($sql->fetch()['total']);

        // Total distribué
        $sql = $this->db->prepare(
            "SELECT COALESCE(SUM(dist.quantite_attribuee * a.prix_unitaire), 0) AS total
            FROM distribution dist
            JOIN don_article da ON dist.id_don_article = da.id
            JOIN article a ON da.id_article = a.id"
        );
        $sql->execute();
        $totalDistribue = floatval($sql->fetch()['total']);

        // Total achats
        $sql = $this->db->prepare(
            "SELECT COALESCE(SUM(montant_total), 0) AS total FROM achat"
        );
        $sql->execute();
        $totalAchats = floatval($sql->fetch()['total']);

        return [
            'total_dons_recus' => $totalDons,
            'total_distribue' => $totalDistribue,
            'total_achats' => $totalAchats,
            'reste_disponible' => $totalDons - $totalDistribue - $totalAchats
        ];
    }

    /**
     * Données pour le tableau de bord (récap complet)
     */
    public function getDashboardData()
    {
        return [
            'recap_general' => $this->getRecapitulatif(),
            'recap_par_ville' => $this->getRecapitulatifParVille(),
            'recap_par_type' => $this->getRecapitulatifParType(),
            'recap_dons' => $this->getRecapitulatifDons()
        ];
    }
}
