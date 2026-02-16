<?php

namespace app\controllers;

class BngrcController
{
    private static $villes = [
        ['id' => 1, 'nom' => 'Antananarivo', 'region' => 'Analamanga'],
        ['id' => 2, 'nom' => 'Antsirabe', 'region' => 'Vakinankaratra'],
        ['id' => 3, 'nom' => 'Fianarantsoa', 'region' => 'Haute Matsiatra'],
        ['id' => 4, 'nom' => 'Toamasina', 'region' => 'Atsinanana'],
        ['id' => 5, 'nom' => 'Mahajanga', 'region' => 'Boeny'],
        ['id' => 6, 'nom' => 'Toliara', 'region' => 'Atsimo-Andrefana'],
    ];

    private static $categories = [
        ['id' => 1, 'nom' => 'Nature', 'items' => [
            ['nom' => 'Riz', 'unite' => 'kg', 'prix_unitaire' => 2500],
            ['nom' => 'Huile', 'unite' => 'L', 'prix_unitaire' => 8000],
            ['nom' => 'Haricots', 'unite' => 'kg', 'prix_unitaire' => 3000],
            ['nom' => 'Sucre', 'unite' => 'kg', 'prix_unitaire' => 4000],
        ]],
        ['id' => 2, 'nom' => 'Matériaux', 'items' => [
            ['nom' => 'Tôle', 'unite' => 'pièce', 'prix_unitaire' => 45000],
            ['nom' => 'Clou', 'unite' => 'kg', 'prix_unitaire' => 6000],
            ['nom' => 'Bois', 'unite' => 'm3', 'prix_unitaire' => 120000],
            ['nom' => 'Ciment', 'unite' => 'sac', 'prix_unitaire' => 32000],
        ]],
        ['id' => 3, 'nom' => 'Argent', 'items' => [
            ['nom' => 'Argent liquide', 'unite' => 'Ar', 'prix_unitaire' => 1],
        ]],
    ];

    private static $besoins = [];
    private static $dons = [];
    private static $distributions = [];

    public function __construct()
    {
        // Initialiser les données de session si nécessaire
        if (!isset($_SESSION['besoins'])) {
            $_SESSION['besoins'] = [
                [
                    'id' => 1,
                    'ville_id' => 1,
                    'item' => 'Riz',
                    'quantite' => 500,
                    'prix_unitaire' => 2500,
                    'date_saisie' => '2026-02-15 10:30:00'
                ],
                [
                    'id' => 2,
                    'ville_id' => 1,
                    'item' => 'Tôle',
                    'quantite' => 100,
                    'prix_unitaire' => 45000,
                    'date_saisie' => '2026-02-15 11:00:00'
                ],
                [
                    'id' => 3,
                    'ville_id' => 2,
                    'item' => 'Huile',
                    'quantite' => 200,
                    'prix_unitaire' => 8000,
                    'date_saisie' => '2026-02-15 14:20:00'
                ],
                [
                    'id' => 4,
                    'ville_id' => 3,
                    'item' => 'Ciment',
                    'quantite' => 50,
                    'prix_unitaire' => 32000,
                    'date_saisie' => '2026-02-16 08:15:00'
                ],
            ];
        }

        if (!isset($_SESSION['dons'])) {
            $_SESSION['dons'] = [
                [
                    'id' => 1,
                    'donateur' => 'Association Humanitaire A',
                    'item' => 'Riz',
                    'quantite' => 300,
                    'valeur_unitaire' => 2500,
                    'date_don' => '2026-02-15 16:00:00',
                    'statut' => 'disponible'
                ],
                [
                    'id' => 2,
                    'donateur' => 'Entreprise B',
                    'item' => 'Tôle',
                    'quantite' => 80,
                    'valeur_unitaire' => 45000,
                    'date_don' => '2026-02-16 09:30:00',
                    'statut' => 'disponible'
                ],
                [
                    'id' => 3,
                    'donateur' => 'ONG International C',
                    'item' => 'Argent liquide',
                    'quantite' => 5000000,
                    'valeur_unitaire' => 1,
                    'date_don' => '2026-02-16 11:45:00',
                    'statut' => 'disponible'
                ],
            ];
        }

        if (!isset($_SESSION['distributions'])) {
            $_SESSION['distributions'] = [];
        }
    }

    public function dashboard()
    {
        $data = $this->preparerDonneesDashboard();
        
        \Flight::render('dashboard', [
            'title' => 'Tableau de Bord - BNGRC',
            'villes' => self::$villes,
            'besoins' => $_SESSION['besoins'],
            'dons' => $_SESSION['dons'],
            'distributions' => $_SESSION['distributions'],
            'data' => $data
        ]);
    }

    public function besoins()
    {
        \Flight::render('besoin/besoins', [
            'title' => 'Gestion des Besoins',
            'besoins' => $_SESSION['besoins'],
            'villes' => self::$villes,
            'categories' => self::$categories
        ]);
    }

    public function ajouterBesoin()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nouveauBesoin = [
                'id' => count($_SESSION['besoins']) + 1,
                'ville_id' => (int)$_POST['ville_id'],
                'item' => $_POST['item'],
                'quantite' => (int)$_POST['quantite'],
                'prix_unitaire' => (int)$_POST['prix_unitaire'],
                'date_saisie' => date('Y-m-d H:i:s')
            ];
            
            $_SESSION['besoins'][] = $nouveauBesoin;
            
            \Flight::redirect('/besoins');
            return;
        }

        \Flight::render('besoin/ajouter_besoin', [
            'title' => 'Ajouter un Besoin',
            'villes' => self::$villes,
            'categories' => self::$categories
        ]);
    }

    public function dons()
    {
        \Flight::render('don/dons', [
            'title' => 'Gestion des Dons',
            'dons' => $_SESSION['dons'],
            'categories' => self::$categories
        ]);
    }

    public function ajouterDon()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nouveauDon = [
                'id' => count($_SESSION['dons']) + 1,
                'donateur' => $_POST['donateur'],
                'item' => $_POST['item'],
                'quantite' => (int)$_POST['quantite'],
                'valeur_unitaire' => (int)$_POST['valeur_unitaire'],
                'date_don' => date('Y-m-d H:i:s'),
                'statut' => 'disponible'
            ];
            
            $_SESSION['dons'][] = $nouveauDon;
            
            \Flight::redirect('/dons');
            return;
        }

        \Flight::render('don/ajouter_don', [
            'title' => 'Ajouter un Don',
            'categories' => self::$categories
        ]);
    }

    public function distributions()
    {
        \Flight::render('distribution/distributions', [
            'title' => 'Simulation des Distributions',
            'distributions' => $_SESSION['distributions'],
            'besoins' => $_SESSION['besoins'],
            'dons' => $_SESSION['dons'],
            'villes' => self::$villes
        ]);
    }

    public function simulerDistributions()
    {
        $_SESSION['distributions'] = $this->calculerDistributions();
        \Flight::redirect('/distributions');
    }

    private function preparerDonneesDashboard()
    {
        $villesStats = [];
        
        foreach (self::$villes as $ville) {
            $besoinsVille = array_filter($_SESSION['besoins'], function($b) use ($ville) {
                return $b['ville_id'] == $ville['id'];
            });
            
            $distributionsVille = array_filter($_SESSION['distributions'], function($d) use ($ville) {
                return $d['ville_id'] == $ville['id'];
            });
            
            $totalBesoins = 0;
            $totalDistributions = 0;
            
            foreach ($besoinsVille as $besoin) {
                $totalBesoins += $besoin['quantite'] * $besoin['prix_unitaire'];
            }
            
            foreach ($distributionsVille as $distribution) {
                $totalDistributions += $distribution['valeur_totale'];
            }
            
            $villesStats[] = [
                'ville' => $ville,
                'besoins' => $besoinsVille,
                'distributions' => $distributionsVille,
                'total_besoins' => $totalBesoins,
                'total_distributions' => $totalDistributions,
                'pourcentage_couvert' => $totalBesoins > 0 ? round(($totalDistributions / $totalBesoins) * 100, 1) : 0
            ];
        }
        
        return $villesStats;
    }

    private function calculerDistributions()
    {
        $distributions = [];
        $donsDisponibles = array_filter($_SESSION['dons'], function($d) {
            return $d['statut'] === 'disponible';
        });
        
        // Trier les besoins par date de saisie
        $besoins = $_SESSION['besoins'];
        usort($besoins, function($a, $b) {
            return strtotime($a['date_saisie']) - strtotime($b['date_saisie']);
        });
        
        // Trier les dons par date
        usort($donsDisponibles, function($a, $b) {
            return strtotime($a['date_don']) - strtotime($b['date_don']);
        });
        
        foreach ($besoins as $besoin) {
            $quantiteRestante = $besoin['quantite'];
            
            foreach ($donsDisponibles as &$don) {
                if ($don['item'] === $besoin['item'] && $don['quantite'] > 0 && $quantiteRestante > 0) {
                    $quantiteAttribuee = min($don['quantite'], $quantiteRestante);
                    
                    $distributions[] = [
                        'id' => count($distributions) + 1,
                        'ville_id' => $besoin['ville_id'],
                        'item' => $besoin['item'],
                        'quantite_attribuee' => $quantiteAttribuee,
                        'valeur_unitaire' => $don['valeur_unitaire'],
                        'valeur_totale' => $quantiteAttribuee * $don['valeur_unitaire'],
                        'donateur' => $don['donateur'],
                        'date_distribution' => date('Y-m-d H:i:s')
                    ];
                    
                    $don['quantite'] -= $quantiteAttribuee;
                    $quantiteRestante -= $quantiteAttribuee;
                    
                    if ($quantiteRestante <= 0) break;
                }
            }
        }
        
        // Mettre à jour le statut des dons épuisés
        foreach ($_SESSION['dons'] as &$don) {
            if ($don['quantite'] <= 0) {
                $don['statut'] = 'distribué';
            }
        }
        
        return $distributions;
    }

    public function getVilleNom($villeId)
    {
        foreach (self::$villes as $ville) {
            if ($ville['id'] == $villeId) {
                return $ville['nom'];
            }
        }
        return 'Ville inconnue';
    }
}