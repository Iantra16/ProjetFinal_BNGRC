<?php

namespace app\models;

use Flight;

class DonModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    // ===================== DON =====================

    /**
     * Récupérer tous les dons
     */
    public function getAllDons()
    {
        $sql = $this->db->prepare(
            "SELECT * FROM don ORDER BY date_don DESC"
        );
        $sql->execute();
        $dons = $sql->fetchAll();

        foreach ($dons as &$don) {
            $don['articles'] = $this->getArticlesForDon($don['id']);
        }

        return $dons;
    }

    /**
     * Récupérer un don par ID
     */
    public function getDonById($donId)
    {
        $sql = $this->db->prepare(
            "SELECT * FROM don WHERE id = ?"
        );
        $sql->execute([$donId]);
        $don = $sql->fetch();

        if ($don) {
            $don['articles'] = $this->getArticlesForDon($don['id']);
        }

        return $don;
    }

    /**
     * Récupérer les dons par donateur
     */
    public function getDonsByDonateur($donateur)
    {
        $sql = $this->db->prepare(
            "SELECT * FROM don WHERE donateur LIKE ? ORDER BY date_don DESC"
        );
        $sql->execute(['%' . $donateur . '%']);
        $dons = $sql->fetchAll();

        foreach ($dons as &$don) {
            $don['articles'] = $this->getArticlesForDon($don['id']);
        }

        return $dons;
    }

    /**
     * Créer un nouveau don
     */
    public function createDon($donateur = null)
    {
        $sql = $this->db->prepare(
            "INSERT INTO don (donateur) VALUES (?)"
        );
        $sql->execute([$donateur]);

        return $this->db->lastInsertId();
    }

    /**
     * Mettre à jour un don
     */
    public function updateDon($donId, $donateur)
    {
        $sql = $this->db->prepare(
            "UPDATE don SET donateur = ? WHERE id = ?"
        );
        return $sql->execute([$donateur, $donId]);
    }

    /**
     * Supprimer un don (et ses articles liés)
     */
    public function deleteDon($donId)
    {
        // Supprimer d'abord les articles liés
        $this->deleteAllArticlesFromDon($donId);

        $sql = $this->db->prepare("DELETE FROM don WHERE id = ?");
        return $sql->execute([$donId]);
    }

    // ===================== DON_ARTICLE =====================

    /**
     * Récupérer les articles d'un don
     */
    public function getArticlesForDon($donId)
    {
        $sql = $this->db->prepare(
            "SELECT da.*, a.nom AS article_nom, a.prix_unitaire, a.unite, t.libelle AS type_besoin
            FROM don_article da
            JOIN article a ON da.id_article = a.id
            JOIN type_besoin t ON a.id_type_besoin = t.id
            WHERE da.id_don = ?"
        );
        $sql->execute([$donId]);
        return $sql->fetchAll();
    }

    /**
     * Ajouter un article à un don
     */
    public function addArticleToDon($donId, $articleId, $quantite)
    {
        $sql = $this->db->prepare(
            "INSERT INTO don_article (id_don, id_article, quantite) 
            VALUES (?, ?, ?)"
        );
        $sql->execute([$donId, $articleId, $quantite]);

        return $this->db->lastInsertId();
    }

    /**
     * Mettre à jour un article dans un don
     */
    public function updateDonArticle($donArticleId, $quantite)
    {
        $sql = $this->db->prepare(
            "UPDATE don_article SET quantite = ? WHERE id = ?"
        );
        return $sql->execute([$quantite, $donArticleId]);
    }

    /**
     * Supprimer un article d'un don
     */
    public function deleteDonArticle($donArticleId)
    {
        $sql = $this->db->prepare("DELETE FROM don_article WHERE id = ?");
        return $sql->execute([$donArticleId]);
    }

    /**
     * Supprimer tous les articles d'un don
     */
    public function deleteAllArticlesFromDon($donId)
    {
        $sql = $this->db->prepare("DELETE FROM don_article WHERE id_don = ?");
        return $sql->execute([$donId]);
    }

    // ===================== METHODES UTILITAIRES =====================

    /**
     * Créer un don complet avec ses articles
     */
    public function createDonWithArticles($donateur, $articles)
    {
        $this->db->beginTransaction();

        try {
            // Créer le don
            $donId = $this->createDon($donateur);

            // Ajouter les articles
            foreach ($articles as $article) {
                $this->addArticleToDon($donId, $article['id_article'], $article['quantite']);
            }

            $this->db->commit();
            return $donId;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Calculer le montant total d'un don
     */
    public function getTotalMontantDon($donId)
    {
        $sql = $this->db->prepare(
            "SELECT SUM(da.quantite * a.prix_unitaire) AS total
            FROM don_article da
            JOIN article a ON da.id_article = a.id
            WHERE da.id_don = ?"
        );
        $sql->execute([$donId]);
        $result = $sql->fetch();

        return $result['total'] ?? 0;
    }

    /**
     * Récupérer la quantité restante non distribuée d'un don_article
     */
    public function getQuantiteRestante($donArticleId)
    {
        $sql = $this->db->prepare(
            "SELECT da.quantite - COALESCE(SUM(d.quantite_attribuee), 0) AS reste
            FROM don_article da
            LEFT JOIN distribution d ON d.id_don_article = da.id
            WHERE da.id = ?
            GROUP BY da.id, da.quantite"
        );
        $sql->execute([$donArticleId]);
        $result = $sql->fetch();

        return $result['reste'] ?? 0;
    }

    /**
     * Récupérer les dons non distribués (avec quantité restante > 0)
     */
    public function getDonsNonDistribues()
    {
        $sql = $this->db->prepare(
            "SELECT da.*, d.donateur, d.date_don, a.nom AS article_nom, a.unite,
                    da.quantite - COALESCE(SUM(dist.quantite_attribuee), 0) AS quantite_restante
            FROM don_article da
            JOIN don d ON da.id_don = d.id
            JOIN article a ON da.id_article = a.id
            LEFT JOIN distribution dist ON dist.id_don_article = da.id
            GROUP BY da.id, d.donateur, d.date_don, a.nom, a.unite, da.quantite
            HAVING da.quantite - COALESCE(SUM(dist.quantite_attribuee), 0) > 0
            ORDER BY d.date_don ASC"
        );
        $sql->execute();
        return $sql->fetchAll();
    }
}
