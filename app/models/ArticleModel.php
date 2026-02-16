<?php

namespace app\models;

use Flight;

class ArticleModel
{

    private $db;

    public function __construct($db){
        $this->db = $db;
    }

    public function insert($nom,$prixU,$unite,$idTypeBesoin){
        $sql = $this->db->prepare("INSERT INTO article (nom, prix_unitaire, unite, id_type_besoin) VALUES (?, ?, ?, ?)");
        $req = $sql->execute([$nom, $prixU, $unite, $idTypeBesoin]);
        return $req;
    }

    public function getAll(){
        $sql = $this->db->prepare("SELECT * FROM article");
        $sql->execute();
        return $sql->fetchAll();
    }

    public function getArticleById($idArticle) {
        $sql = $this->db->prepare("SELECT * FROM article WHERE id = ?");
        $sql->execute([$idArticle]);
        return $sql->fetch();
    }

    public function getAllTypeBesoin(){
        $sql = $this->db->prepare("SELECT * FROM type_besoin");
        $sql->execute();
        return $sql->fetchAll();
    }
    
}
