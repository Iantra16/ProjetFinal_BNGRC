<?php

namespace app\models;

use Flight;

class AchatModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    // ===================== ACHAT =====================

    /**
     * Récupérer tous les achats
     */
    public function getAllAchats()
    {
        $sql = $this->db->prepare(
            "SELECT ac.*, a.nom AS article_nom, a.unite,
                    da.id_don, d.donateur
            FROM achat ac
            JOIN article a ON ac.id_article = a.id
            JOIN don_article da ON ac.id_don_article = da.id
            JOIN don d ON da.id_don = d.id
            ORDER BY ac.date_achat DESC"
        );
        $sql->execute();
        return $sql->fetchAll();
    }

    /**
     * Récupérer un achat par ID
     */
    public function getAchatById($achatId)
    {
        $sql = $this->db->prepare(
            "SELECT ac.*, a.nom AS article_nom, a.unite,
                    da.id_don, d.donateur
            FROM achat ac
            JOIN article a ON ac.id_article = a.id
            JOIN don_article da ON ac.id_don_article = da.id
            JOIN don d ON da.id_don = d.id
            WHERE ac.id = ?"
        );
        $sql->execute([$achatId]);
        return $sql->fetch();
    }

    /**
     * Récupérer les achats par ville (via besoin_article distribué)
     */
    public function getAchatsByVille($villeId)
    {
        $sql = $this->db->prepare(
            "SELECT ac.*, a.nom AS article_nom, a.unite,
                    d.donateur, v.nom AS ville_nom
            FROM achat ac
            JOIN article a ON ac.id_article = a.id
            JOIN don_article da ON ac.id_don_article = da.id
            JOIN don d ON da.id_don = d.id
            JOIN distribution dist ON dist.id_achat = ac.id
            JOIN besoin_article ba ON dist.id_besoin_article = ba.id
            JOIN besoin b ON ba.id_besoin = b.id
            JOIN ville v ON b.id_ville = v.id
            WHERE v.id = ?
            ORDER BY ac.date_achat DESC"
        );
        $sql->execute([$villeId]);
        return $sql->fetchAll();
    }

    /**
     * Créer un achat
     */
    public function createAchat($idDonArticle, $idArticle, $quantite, $prixUnitaire, $fraisPourcent)
    {
        $montantHT = $quantite * $prixUnitaire;
        $montantTotal = $montantHT * (1 + $fraisPourcent / 100);

        $sql = $this->db->prepare(
            "INSERT INTO achat (id_don_article, id_article, quantite, prix_unitaire, frais_pourcent, montant_total) 
            VALUES (?, ?, ?, ?, ?, ?)"
        );
        $sql->execute([$idDonArticle, $idArticle, $quantite, $prixUnitaire, $fraisPourcent, $montantTotal]);

        return $this->db->lastInsertId();
    }

    /**
     * Supprimer un achat
     */
    public function deleteAchat($achatId)
    {
        $sql = $this->db->prepare("DELETE FROM achat WHERE id = ?");
        return $sql->execute([$achatId]);
    }

    // ===================== VALIDATIONS =====================

    /**
     * Vérifier si l'article existe dans les dons restants (non argent)
     * Retourne true si l'article existe déjà dans les dons
     */
    public function articleExisteDansDonsRestants($idArticle)
    {
        $sql = $this->db->prepare(
            "SELECT COUNT(*) AS nb FROM v_reste_dons_disponibles WHERE id_article = ?"
        );
        $sql->execute([$idArticle]);
        $result = $sql->fetch();
        return $result['nb'] > 0;
    }

    /**
     * Récupérer le solde argent disponible d'un don_article (argent)
     */
    public function getSoldeArgentDisponible($idDonArticle)
    {
        $sql = $this->db->prepare(
            "SELECT da.quantite - COALESCE(SUM(ac.montant_total), 0) AS solde
            FROM don_article da
            LEFT JOIN achat ac ON ac.id_don_article = da.id
            WHERE da.id = ?
            GROUP BY da.id, da.quantite"
        );
        $sql->execute([$idDonArticle]);
        $result = $sql->fetch();
        return $result ? floatval($result['solde']) : 0;
    }

    /**
     * Récupérer tous les dons en argent avec solde disponible
     */
    public function getDonsArgentDisponibles()
    {
        $sql = $this->db->prepare(
            "SELECT da.id AS id_don_article, da.quantite AS montant_initial,
                    d.donateur, d.date_don,
                    (da.quantite - COALESCE((
                        SELECT SUM(ac.montant_total) FROM achat ac WHERE ac.id_don_article = da.id
                    ), 0)) AS solde_disponible
            FROM don_article da
            JOIN don d ON da.id_don = d.id
            JOIN article a ON da.id_article = a.id
            JOIN type_besoin t ON a.id_type_besoin = t.id
            WHERE t.libelle = 'Argent'
            HAVING solde_disponible > 0
            ORDER BY d.date_don ASC"
        );
        $sql->execute();
        return $sql->fetchAll();
    }

    /**
     * Récupérer les articles achetables (nature + materiaux)
     */
    public function getArticlesAchetables()
    {
        $sql = $this->db->prepare(
            "SELECT a.*, t.libelle AS type_besoin
            FROM article a
            JOIN type_besoin t ON a.id_type_besoin = t.id
            WHERE t.libelle IN ('Nature', 'Materiaux')
            ORDER BY a.nom"
        );
        $sql->execute();
        return $sql->fetchAll();
    }

    // ===================== METHODES UTILITAIRES =====================

    /**
     * Effectuer un achat complet avec validation
     */
    public function effectuerAchat($idDonArticle, $idArticle, $quantite, $fraisPourcent)
    {
        // Vérifier si l'article existe déjà dans les dons restants
        if ($this->articleExisteDansDonsRestants($idArticle)) {
            throw new \Exception("Cet article existe encore dans les dons restants. Utilisez d'abord les dons existants.");
        }

        // Récupérer le prix unitaire de l'article
        $sql = $this->db->prepare("SELECT prix_unitaire FROM article WHERE id = ?");
        $sql->execute([$idArticle]);
        $article = $sql->fetch();
        
        if (!$article) {
            throw new \Exception("Article introuvable.");
        }

        $prixUnitaire = $article['prix_unitaire'];
        $montantHT = $quantite * $prixUnitaire;
        $montantTotal = $montantHT * (1 + $fraisPourcent / 100);

        // Vérifier le solde disponible
        $solde = $this->getSoldeArgentDisponible($idDonArticle);
        if ($solde < $montantTotal) {
            throw new \Exception("Solde insuffisant. Disponible: " . number_format($solde, 2) . " Ar, Requis: " . number_format($montantTotal, 2) . " Ar");
        }

        // Créer l'achat
        return $this->createAchat($idDonArticle, $idArticle, $quantite, $prixUnitaire, $fraisPourcent);
    }

    /**
     * Récupérer le stock disponible d'un achat (pour distribution)
     */
    public function getStockAchatDisponible($achatId)
    {
        $sql = $this->db->prepare(
            "SELECT ac.quantite - COALESCE(SUM(dist.quantite_attribuee), 0) AS stock
            FROM achat ac
            LEFT JOIN distribution dist ON dist.id_achat = ac.id
            WHERE ac.id = ?
            GROUP BY ac.id, ac.quantite"
        );
        $sql->execute([$achatId]);
        $result = $sql->fetch();
        return $result ? floatval($result['stock']) : 0;
    }

    /**
     * Récupérer tous les achats avec stock disponible (pour distribution)
     */
    public function getAchatsDisponiblesPourDistribution()
    {
        $sql = $this->db->prepare(
            "SELECT ac.*, a.nom AS article_nom, a.unite,
                    (ac.quantite - COALESCE((
                        SELECT SUM(dist.quantite_attribuee) FROM distribution dist WHERE dist.id_achat = ac.id
                    ), 0)) AS stock_disponible
            FROM achat ac
            JOIN article a ON ac.id_article = a.id
            HAVING stock_disponible > 0
            ORDER BY ac.date_achat ASC"
        );
        $sql->execute();
        return $sql->fetchAll();
    }
}
