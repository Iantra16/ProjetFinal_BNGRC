<?php

namespace app\models;

use Flight;

class BesoinModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }


    public function getAllTypeBesoin() {
        $sql = $this->db->prepare("SELECT * FROM type_besoin");
        $sql->execute();
        return $sql->fetchAll();
    }

    public function getAllArticle(){
        $sql = $this->db->prepare("SELECT a.*, t.libelle AS type_besoin FROM article a JOIN type_besoin t ON a.id_type_besoin = t.id");
        $sql->execute();
        return $sql->fetchAll();
    }

    /**
     * Récupérer tous les besoins avec infos ville
     */
    public function getAllBesoins()
    {
        $sql = $this->db->prepare(
            "SELECT b.*, v.nom AS ville_nom, r.nom AS region_nom
            FROM besoin b
            JOIN ville v ON b.id_ville = v.id
            JOIN region r ON v.id_region = r.id
            ORDER BY b.date_saisie DESC"
        );
        $sql->execute();
        $besoins = $sql->fetchAll();

        foreach ($besoins as &$besoin) {
            $besoin['articles'] = $this->getArticlesForBesoin($besoin['id']);
        }

        return $besoins;
    }

    /**
     * Récupérer un besoin par ID
     */
    public function getBesoinById($besoinId)
    {
        $sql = $this->db->prepare(
            "SELECT b.*, v.nom AS ville_nom, r.nom AS region_nom
            FROM besoin b
            JOIN ville v ON b.id_ville = v.id
            JOIN region r ON v.id_region = r.id
            WHERE b.id = ?"
        );
        $sql->execute([$besoinId]);
        $besoin = $sql->fetch();

        if ($besoin) {
            $besoin['articles'] = $this->getArticlesForBesoin($besoin['id']);
        }

        return $besoin;
    }

    /**
     * Récupérer les besoins par ville
     */
    public function getBesoinsByVille($villeId)
    {
        $sql = $this->db->prepare(
            "SELECT b.*, v.nom AS ville_nom
            FROM besoin b
            JOIN ville v ON b.id_ville = v.id
            WHERE b.id_ville = ?
            ORDER BY b.date_saisie DESC"
        );
        $sql->execute([$villeId]);
        $besoins = $sql->fetchAll();

        foreach ($besoins as &$besoin) {
            $besoin['articles'] = $this->getArticlesForBesoin($besoin['id']);
        }

        return $besoins;
    }

    /**
     * Créer un nouveau besoin
     */
    public function createBesoin($idVille)
    {
        $sql = $this->db->prepare(
            "INSERT INTO besoin (id_ville) VALUES (?)"
        );
        $sql->execute([$idVille]);

        return $this->db->lastInsertId();
    }

    /**
     * Mettre à jour un besoin
     */
    public function updateBesoin($besoinId, $idVille)
    {
        $sql = $this->db->prepare(
            "UPDATE besoin SET id_ville = ? WHERE id = ?"
        );
        return $sql->execute([$idVille, $besoinId]);
    }

    /**
     * Supprimer un besoin (et ses articles liés)
     */
    public function deleteBesoin($besoinId)
    {
        // Supprimer d'abord les articles liés
        $this->deleteAllArticlesFromBesoin($besoinId);

        $sql = $this->db->prepare("DELETE FROM besoin WHERE id = ?");
        return $sql->execute([$besoinId]);
    }

    // ===================== BESOIN_ARTICLE =====================

    /**
     * Récupérer les articles d'un besoin
     */
    public function getArticlesForBesoin($besoinId)
    {
        $sql = $this->db->prepare(
            "SELECT ba.*, a.nom AS article_nom, a.prix_unitaire, a.unite, t.libelle AS type_besoin
            FROM besoin_article ba
            JOIN article a ON ba.id_article = a.id
            JOIN type_besoin t ON a.id_type_besoin = t.id
            WHERE ba.id_besoin = ?"
        );
        $sql->execute([$besoinId]);
        return $sql->fetchAll();
    }

    /**
     * Ajouter un article à un besoin
     */
    public function addArticleToBesoin($besoinId, $articleId, $quantite)
    {
        $sql = $this->db->prepare(
            "INSERT INTO besoin_article (id_besoin, id_article, quantite) 
            VALUES (?, ?, ?)"
        );
        $sql->execute([$besoinId, $articleId, $quantite]);

        return $this->db->lastInsertId();
    }

    /**
     * Mettre à jour un article dans un besoin
     */
    public function updateBesoinArticle($besoinArticleId, $quantite)
    {
        $sql = $this->db->prepare(
            "UPDATE besoin_article SET quantite = ? WHERE id = ?"
        );
        return $sql->execute([$quantite, $besoinArticleId]);
    }

    /**
     * Supprimer un article d'un besoin
     */
    public function deleteBesoinArticle($besoinArticleId)
    {
        $sql = $this->db->prepare("DELETE FROM besoin_article WHERE id = ?");
        return $sql->execute([$besoinArticleId]);
    }

    /**
     * Supprimer tous les articles d'un besoin
     */
    public function deleteAllArticlesFromBesoin($besoinId)
    {
        $sql = $this->db->prepare("DELETE FROM besoin_article WHERE id_besoin = ?");
        return $sql->execute([$besoinId]);
    }

    // ===================== METHODES UTILITAIRES =====================

    /**
     * Créer un besoin complet avec ses articles
     */
    public function createBesoinWithArticles($idVille, $articles)
    {
        $this->db->beginTransaction();

        try {
            // Créer le besoin
            $besoinId = $this->createBesoin($idVille);

            // Ajouter les articles
            foreach ($articles as $article) {
                $this->addArticleToBesoin($besoinId, $article['id_article'], $article['quantite']);
            }

            $this->db->commit();
            return $besoinId;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Calculer le montant total d'un besoin
     */
    public function getTotalMontantBesoin($besoinId)
    {
        $sql = $this->db->prepare(
            "SELECT SUM(ba.quantite * a.prix_unitaire) AS total
            FROM besoin_article ba
            JOIN article a ON ba.id_article = a.id
            WHERE ba.id_besoin = ?"
        );
        $sql->execute([$besoinId]);
        $result = $sql->fetch();

        return $result['total'] ?? 0;
    }
}
