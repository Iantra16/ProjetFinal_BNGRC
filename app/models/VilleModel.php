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
        $sql = $this->db->prepare("SELECT v.id, v.nom, v.id_region, COALESCE(r.nom, 'Non assignée') as region_nom FROM ville v LEFT JOIN region r ON v.id_region = r.id ORDER BY v.id DESC");
        $sql->execute();
        return $sql->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getVilleById($idVille) {
        $sql = $this->db->prepare("SELECT v.id, v.nom, v.id_region, r.nom as region_nom FROM ville v LEFT JOIN region r ON v.id_region = r.id WHERE v.idVille = ?");
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

    public function getRegions() {
        $sql = $this->db->prepare("SELECT * FROM region");
        $sql->execute();
        return $sql->fetchAll();
    }

}
