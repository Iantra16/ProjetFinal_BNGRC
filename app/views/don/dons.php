<?php 
$title = "Gestion des dons - BNGRC";
ob_start();

function formatMoney($amount) {
    return number_format($amount, 0, ',', ' ') . ' Ar';
}
?>

<div class="page-header mb-4">
    <h1 class="page-title">
        <i class="fas fa-gift"></i>
        Gestion des dons
    </h1>
    <p class="page-subtitle">Suivi et gestion des dons reçus</p>
</div>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3><i class="fas fa-list text-success"></i> Liste des dons</h3>
            <a href="/dons/ajouter" class="btn btn-success">
                <i class="fas fa-plus"></i> Ajouter un Don
            </a>
        </div>

        <?php if (empty($dons)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                Aucun don enregistré. <a href="/dons/ajouter">Commencez par ajouter un don</a>.
            </div>
        <?php else: ?>
            <!-- Statistiques des dons -->
            <div class="row mb-4">
                <?php 
                $totalDons = count($dons);
                $totalValeur = array_sum(array_map(function($don) { 
                    return $don['quantite'] * $don['valeur_unitaire']; 
                }, $dons));
                $donsDisponibles = count(array_filter($dons, function($don) { 
                    return $don['statut'] === 'disponible'; 
                }));
                $donsDistribues = $totalDons - $donsDisponibles;
                ?>
                
                <div class="col-md-3">
                    <div class="card border-success">
                        <div class="card-body text-center">
                            <i class="fas fa-gifts fa-2x text-success mb-2"></i>
                            <h4 class="text-success"><?= $totalDons ?></h4>
                            <small class="text-muted">Total des Dons</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-info">
                        <div class="card-body text-center">
                            <i class="fas fa-warehouse fa-2x text-info mb-2"></i>
                            <h4 class="text-info"><?= $donsDisponibles ?></h4>
                            <small class="text-muted">Disponibles</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-warning">
                        <div class="card-body text-center">
                            <i class="fas fa-truck fa-2x text-warning mb-2"></i>
                            <h4 class="text-warning"><?= $donsDistribues ?></h4>
                            <small class="text-muted">Distribués</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-primary">
                        <div class="card-body text-center">
                            <i class="fas fa-coins fa-2x text-primary mb-2"></i>
                            <h4 class="text-primary"><?= formatMoney($totalValeur) ?></h4>
                            <small class="text-muted">Valeur Totale</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-success bg-opacity-10">
                    <h5 class="mb-0">Liste des Dons Reçus</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th><i class="fas fa-heart"></i> Donateur</th>
                                    <th><i class="fas fa-box"></i> Article</th>
                                    <th><i class="fas fa-calculator"></i> Quantité</th>
                                    <th><i class="fas fa-coins"></i> Valeur Unitaire</th>
                                    <th><i class="fas fa-money-bill-wave"></i> Valeur Totale</th>
                                    <th><i class="fas fa-calendar"></i> Date du Don</th>
                                    <th><i class="fas fa-flag"></i> Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                // Trier les dons par date (plus récent en premier)
                                usort($dons, function($a, $b) {
                                    return strtotime($b['date_don']) - strtotime($a['date_don']);
                                });
                                
                                foreach ($dons as $don): 
                                    $valeurTotale = $don['quantite'] * $don['valeur_unitaire'];
                                    $badgeClass = $don['statut'] === 'disponible' ? 'bg-success' : 'bg-warning';
                                    $badgeText = $don['statut'] === 'disponible' ? 'Disponible' : 'Distribué';
                                ?>
                                    <tr class="<?= $don['statut'] === 'distribué' ? 'table-secondary' : '' ?>">
                                        <td>
                                            <strong><?= htmlspecialchars($don['donateur']) ?></strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary"><?= $don['item'] ?></span>
                                        </td>
                                        <td>
                                            <strong><?= number_format($don['quantite']) ?></strong>
                                        </td>
                                        <td><?= formatMoney($don['valeur_unitaire']) ?></td>
                                        <td>
                                            <strong class="text-success"><?= formatMoney($valeurTotale) ?></strong>
                                        </td>
                                        <td>
                                            <small><?= date('d/m/Y H:i', strtotime($don['date_don'])) ?></small>
                                        </td>
                                        <td>
                                            <span class="badge <?= $badgeClass ?>"><?= $badgeText ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Répartition par type d'article -->
            <div class="row mt-4">
                <div class="col-12">
                    <h4><i class="fas fa-chart-pie"></i> Répartition par Article</h4>
                </div>
                <?php 
                $repartitionArticles = [];
                foreach ($dons as $don) {
                    if (!isset($repartitionArticles[$don['item']])) {
                        $repartitionArticles[$don['item']] = ['quantite' => 0, 'valeur' => 0, 'donateurs' => []];
                    }
                    $repartitionArticles[$don['item']]['quantite'] += $don['quantite'];
                    $repartitionArticles[$don['item']]['valeur'] += $don['quantite'] * $don['valeur_unitaire'];
                    if (!in_array($don['donateur'], $repartitionArticles[$don['item']]['donateurs'])) {
                        $repartitionArticles[$don['item']]['donateurs'][] = $don['donateur'];
                    }
                }
                ?>
                
                <?php foreach ($repartitionArticles as $article => $stats): ?>
                    <div class="col-md-4 mb-3">
                        <div class="card border-success">
                            <div class="card-body">
                                <h5 class="card-title text-success">
                                    <i class="fas fa-box"></i> <?= $article ?>
                                </h5>
                                <p class="card-text">
                                    <strong>Quantité :</strong> <?= number_format($stats['quantite']) ?><br>
                                    <strong>Valeur :</strong> <?= formatMoney($stats['valeur']) ?><br>
                                    <strong>Donateurs :</strong> <?= count($stats['donateurs']) ?>
                                </p>
                                <div class="progress">
                                    <div class="progress-bar bg-success" role="progressbar" 
                                         style="width: <?= ($stats['valeur'] / $totalValeur) * 100 ?>%">
                                        <?= round(($stats['valeur'] / $totalValeur) * 100, 1) ?>%
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Donateurs les plus généreux -->
            <div class="card mt-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-trophy text-warning"></i> Top Donateurs</h5>
                </div>
                <div class="card-body">
                    <?php 
                    $donateurs = [];
                    foreach ($dons as $don) {
                        if (!isset($donateurs[$don['donateur']])) {
                            $donateurs[$don['donateur']] = ['dons' => 0, 'valeur' => 0];
                        }
                        $donateurs[$don['donateur']]['dons']++;
                        $donateurs[$don['donateur']]['valeur'] += $don['quantite'] * $don['valeur_unitaire'];
                    }
                    arsort($donateurs);
                    $topDonateurs = array_slice($donateurs, 0, 5, true);
                    ?>
                    
                    <div class="row">
                        <?php $position = 1; foreach ($topDonateurs as $donateur => $stats): ?>
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <?php if ($position == 1): ?>
                                            <i class="fas fa-trophy text-warning fa-2x"></i>
                                        <?php elseif ($position == 2): ?>
                                            <i class="fas fa-medal text-secondary fa-2x"></i>
                                        <?php elseif ($position == 3): ?>
                                            <i class="fas fa-medal text-warning fa-2x"></i>
                                        <?php else: ?>
                                            <i class="fas fa-star text-info fa-2x"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <h6 class="mb-0"><?= htmlspecialchars($donateur) ?></h6>
                                        <small class="text-muted">
                                            <?= $stats['dons'] ?> don(s) - <?= formatMoney($stats['valeur']) ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        <?php $position++; endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include(__DIR__ . '/../layout/layout.php');
?>