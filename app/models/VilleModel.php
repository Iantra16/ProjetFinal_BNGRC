<?php

namespace app\models;

use Flight;

class VilleModel
{

    private $db;

    public function __construct($db){
        $this->db = $db;
    }

    public function insert($nom, $id_region){
        $sql = $this->db->prepare("INSERT INTO ville (nom, id_region) VALUES (?, ?)");
        $req = $sql->execute([$nom, $id_region]);
        return $req;
    }

    public function getAll(){
        $sql = $this->db->prepare("SELECT * FROM ville");
        $sql->execute();
        return $sql->fetchAll();
    }

    public function getVilleById($idVille) {
        $sql = $this->db->prepare("SELECT * FROM ville WHERE idVille = ?");
        $sql->execute([$idVille]);
        return $sql->fetch();
    }

    public function updateVille($id) {
        $sql = $this->db->prepare("UPDATE ville SET nom = ?, id_region = ? WHERE idVille = ?");
        $req = $sql->execute([$_POST['nom'], $_POST['id_region'], $id]);
        return $req;
    }

    public function deleteVille($id) {
        $sql = $this->db->prepare("DELETE FROM ville WHERE idVille = ?");
        $req = $sql->execute([$id]);
        return $req;
    }
}
