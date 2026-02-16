<?php

namespace app\models;

use Flight;

class DistributionModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    function DistributoinDons($dons_ville_detaille) { 
        foreach ($dons_ville_detaille as $dons) {
            
            // $sql = $this->db->prepare(
            //     "INSERT INTO distribution_dons (id_besoin, id_don, quantite_distribuee) VALUES (?, ?, ?)"
            // );
            // $sql->execute([$detaille['id_besoin'], $detaille['id_don'], $detaille['quantite_distribuee']]);
        }
    }

}
