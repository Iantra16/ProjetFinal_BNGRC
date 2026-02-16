<?php

namespace app\models;

use Flight;

class ConfigModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Récupérer une valeur de configuration
     */
    public function get($cle)
    {
        $sql = $this->db->prepare("SELECT valeur FROM config WHERE cle = ?");
        $sql->execute([$cle]);
        $result = $sql->fetch();
        return $result ? $result['valeur'] : null;
    }

    /**
     * Définir une valeur de configuration
     */
    public function set($cle, $valeur)
    {
        // Vérifier si la clé existe
        $existing = $this->get($cle);
        
        if ($existing !== null) {
            $sql = $this->db->prepare("UPDATE config SET valeur = ? WHERE cle = ?");
            return $sql->execute([$valeur, $cle]);
        } else {
            $sql = $this->db->prepare("INSERT INTO config (cle, valeur) VALUES (?, ?)");
            return $sql->execute([$cle, $valeur]);
        }
    }

    /**
     * Récupérer toutes les configurations
     */
    public function getAll()
    {
        $sql = $this->db->prepare("SELECT * FROM config");
        $sql->execute();
        return $sql->fetchAll();
    }

    /**
     * Récupérer le pourcentage de frais d'achat
     */
    public function getFraisAchatPourcent()
    {
        $valeur = $this->get('frais_achat_pourcent');
        return $valeur !== null ? floatval($valeur) : 10; // 10% par défaut
    }

    /**
     * Définir le pourcentage de frais d'achat
     */
    public function setFraisAchatPourcent($pourcent)
    {
        return $this->set('frais_achat_pourcent', $pourcent);
    }

    /**
     * Calculer le montant avec frais
     */
    public function calculerMontantAvecFrais($montant)
    {
        $frais = $this->getFraisAchatPourcent();
        return $montant * (1 + $frais / 100);
    }
}
