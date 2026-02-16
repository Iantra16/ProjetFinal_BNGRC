<?php
$title = "Tableau de bord - BNGRC";
ob_start();
?>

<div class="page-header mb-4">
    <h1 class="page-title">
        <i class="fas fa-tachometer-alt"></i>
        Tableau de bord - Suivi des dons
    </h1>
    <p class="page-subtitle">Vue d'ensemble des besoins et distributions par ville</p>
</div>

<!-- Statistiques générales -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card primary">
            <i class="fas fa-city text-primary"></i>
            <div class="stats-number text-primary"><?= count($villes) ?></div>
            <div class="stats-label">Villes concernées</div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card success">
            <i class="fas fa-list-ul text-success"></i>
            <div class="stats-number text-success"><?= count($besoins) ?></div>
            <div class="stats-label">Besoins enregistrés</div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card warning">
            <i class="fas fa-gift text-warning"></i>
            <div class="stats-number text-warning"><?= count($dons) ?></div>
            <div class="stats-label">Dons reçus</div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card info">
            <i class="fas fa-truck text-info"></i>
            <div class="stats-number text-info"><?= count($distributions) ?></div>
            <div class="stats-label">Distributions</div>
        </div>
    </div>
</div>

<!-- Tableau de bord principal -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-map-marked-alt"></i>
                    Situation par ville
                </h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Ville</th>
                                <th>Région</th>
                                <th>Besoins totaux</th>
                                <th>Valeur des besoins</th>
                                <th>Dons reçus</th>
                                <th>Valeur des dons</th>
                                <th>Statut</th>
                                <th>Progress</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($villes as $ville): ?>
                                <?php
                                // Calculer les besoins pour cette ville
                                $besoinsVille = array_filter($besoins, fn($b) => $b['ville_id'] == $ville['id']);
                                $valeurBesoins = array_sum(array_map(fn($b) => $b['prix_unitaire'] * $b['quantite'], $besoinsVille));
                                
                                // Calculer les dons distribués pour cette ville
                                $distributionsVille = array_filter($distributions, fn($d) => $d['ville_id'] == $ville['id']);
                                $valeurDistributions = array_sum(array_map(fn($d) => $d['valeur_totale'], $distributionsVille));
                                
                                // Calculer le pourcentage de satisfaction
                                $pourcentage = $valeurBesoins > 0 ? min(100, ($valeurDistributions / $valeurBesoins) * 100) : 0;
                                
                                // Déterminer le statut
                                if ($pourcentage >= 100) {
                                    $statut = 'Complet';
                                    $statutClass = 'success';
                                } elseif ($pourcentage >= 50) {
                                    $statut = 'Partiel';
                                    $statutClass = 'warning';
                                } else {
                                    $statut = 'En attente';
                                    $statutClass = 'danger';
                                }
                                ?>
                                <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($ville['nom']) ?></strong>
                                    </td>
                                    <td><?= htmlspecialchars($ville['region']) ?></td>
                                    <td>
                                        <span class="badge bg-info"><?= count($besoinsVille) ?> types</span>
                                    </td>
                                    <td>
                                        <strong><?= number_format($valeurBesoins, 0, ',', ' ') ?> Ar</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-success"><?= count($distributionsVille) ?> livraisons</span>
                                    </td>
                                    <td>
                                        <strong><?= number_format($valeurDistributions, 0, ',', ' ') ?> Ar</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $statutClass ?>"><?= $statut ?></span>
                                    </td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-<?= $statutClass ?>" 
                                                 role="progressbar" 
                                                 style="width: <?= $pourcentage ?>%"
                                                 aria-valuenow="<?= $pourcentage ?>" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100">
                                                <?= round($pourcentage) ?>%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Graphiques et détails -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-pie"></i>
                    Types de besoins les plus demandés
                </h3>
            </div>
            <div class="card-body">
                <?php
                $typesBesoins = [];
                foreach ($besoins as $besoin) {
                    // Déterminer le type basé sur l'item
                    $type = 'autres';
                    $item = strtolower($besoin['item']);
                    if (in_array($item, ['riz', 'huile', 'sucre', 'lait'])) {
                        $type = 'nature';
                    } elseif (in_array($item, ['tôle', 'ciment', 'clou', 'bois'])) {
                        $type = 'matériaux';
                    } elseif ($item == 'argent') {
                        $type = 'argent';
                    }
                    
                    if (!isset($typesBesoins[$type])) {
                        $typesBesoins[$type] = 0;
                    }
                    $typesBesoins[$type] += $besoin['quantite'];
                }
                arsort($typesBesoins);
                $colors = ['primary', 'success', 'warning', 'info', 'danger'];
                $i = 0;
                ?>
                <?php foreach (array_slice($typesBesoins, 0, 5) as $type => $quantite): ?>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span><?= ucfirst(htmlspecialchars($type)) ?></span>
                            <span class="badge bg-<?= $colors[$i % count($colors)] ?>"><?= $quantite ?></span>
                        </div>
                        <div class="progress mt-1">
                            <div class="progress-bar bg-<?= $colors[$i % count($colors)] ?>" 
                                 style="width: <?= ($quantite / max($typesBesoins)) * 100 ?>%"></div>
                        </div>
                    </div>
                    <?php $i++; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-clock"></i>
                    Dernières activités
                </h3>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <?php
                    // Combiner dons et distributions pour la timeline
                    $activites = [];
                    foreach ($dons as $don) {
                        $activites[] = [
                            'type' => 'don',
                            'date' => $don['date_reception'] ?? date('Y-m-d H:i:s'),
                            'description' => "Don de {$don['item']} reçu de {$don['donateur']}",
                            'icon' => 'gift',
                            'color' => 'success'
                        ];
                    }
                    foreach ($distributions as $dist) {
                        // Trouver le nom de la ville
                        $nomVille = 'Ville inconnue';
                        foreach ($villes as $ville) {
                            if ($ville['id'] == $dist['ville_id']) {
                                $nomVille = $ville['nom'];
                                break;
                            }
                        }
                        $activites[] = [
                            'type' => 'distribution',
                            'date' => $dist['date_distribution'] ?? date('Y-m-d H:i:s'),
                            'description' => "Distribution de {$dist['item']} vers {$nomVille}",
                            'icon' => 'truck',
                            'color' => 'info'
                        ];
                    }
                    // Trier par date décroissante
                    usort($activites, fn($a, $b) => strtotime($b['date']) - strtotime($a['date']));
                    ?>
                    
                    <?php foreach (array_slice($activites, 0, 8) as $activite): ?>
                        <div class="timeline-item mb-3">
                            <div class="d-flex">
                                <div class="timeline-icon me-3">
                                    <i class="fas fa-<?= $activite['icon'] ?> text-<?= $activite['color'] ?>"></i>
                                </div>
                                <div class="timeline-content">
                                    <p class="mb-1"><?= htmlspecialchars($activite['description']) ?></p>
                                    <small class="text-muted">
                                        <?= date('d/m/Y H:i', strtotime($activite['date'])) ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include (__DIR__ . '/layout/layout.php');
?>