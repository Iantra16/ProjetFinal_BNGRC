<?php 
$title = "Distributions - BNGRC";
ob_start();

function formatMoney($amount) {
    return number_format($amount, 0, ',', ' ') . ' Ar';
}

function getVilleNom($villeId, $villes) {
    foreach ($villes as $ville) {
        if ($ville['id'] == $villeId) {
            return $ville['nom'];
        }
    }
    return 'Ville inconnue';
}
?>

<div class="page-header mb-4">
    <h1 class="page-title">
        <i class="fas fa-truck"></i>
        Simulation des distributions
    </h1>
    <p class="page-subtitle">Attribution automatique des dons aux villes selon les besoins</p>
</div>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3><i class="fas fa-route text-primary"></i> Résultats de la distribution</h3>
            <form method="POST" action="/distributions/simuler" style="display: inline;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-calculator"></i> Relancer la Simulation
                </button>
            </form>
        </div>

        <?php if (empty($distributions)): ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Aucune distribution simulée.</strong> 
                Cliquez sur "Relancer la Simulation" pour calculer automatiquement les distributions 
                selon l'ordre chronologique des besoins et des dons disponibles.
            </div>

            <!-- Aperçu des données disponibles -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-warning bg-opacity-10">
                            <h5 class="mb-0"><i class="fas fa-exclamation-triangle text-warning"></i> Besoins en Attente</h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($besoins)): ?>
                                <p class="text-muted">Aucun besoin enregistré.</p>
                                <a href="/besoins/ajouter" class="btn btn-warning btn-sm">Ajouter des besoins</a>
                            <?php else: ?>
                                <p><strong><?= count($besoins) ?></strong> besoin(s) enregistré(s)</p>
                                <?php 
                                $totalBesoins = array_sum(array_map(function($b) { 
                                    return $b['quantite'] * $b['prix_unitaire']; 
                                }, $besoins));
                                ?>
                                <p class="text-warning"><strong>Valeur totale :</strong> <?= formatMoney($totalBesoins) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-success bg-opacity-10">
                            <h5 class="mb-0"><i class="fas fa-gift text-success"></i> Dons Disponibles</h5>
                        </div>
                        <div class="card-body">
                            <?php 
                            $donsDisponibles = array_filter($dons, function($d) { 
                                return $d['statut'] === 'disponible'; 
                            });
                            ?>
                            <?php if (empty($donsDisponibles)): ?>
                                <p class="text-muted">Aucun don disponible.</p>
                                <a href="/dons/ajouter" class="btn btn-success btn-sm">Ajouter des dons</a>
                            <?php else: ?>
                                <p><strong><?= count($donsDisponibles) ?></strong> don(s) disponible(s)</p>
                                <?php 
                                $totalDons = array_sum(array_map(function($d) { 
                                    return $d['quantite'] * $d['valeur_unitaire']; 
                                }, $donsDisponibles));
                                ?>
                                <p class="text-success"><strong>Valeur totale :</strong> <?= formatMoney($totalDons) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <!-- Résultats de la simulation -->
            <?php 
            $totalDistribue = array_sum(array_column($distributions, 'valeur_totale'));
            $totalBesoins = array_sum(array_map(function($b) { 
                return $b['quantite'] * $b['prix_unitaire']; 
            }, $besoins));
            $tauxCouverture = $totalBesoins > 0 ? round(($totalDistribue / $totalBesoins) * 100, 1) : 0;
            ?>

            <!-- Statistiques de la simulation -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card border-primary">
                        <div class="card-body text-center">
                            <i class="fas fa-truck fa-2x text-primary mb-2"></i>
                            <h4 class="text-primary"><?= count($distributions) ?></h4>
                            <small class="text-muted">Distributions</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-success">
                        <div class="card-body text-center">
                            <i class="fas fa-money-bill-wave fa-2x text-success mb-2"></i>
                            <h4 class="text-success"><?= formatMoney($totalDistribue) ?></h4>
                            <small class="text-muted">Valeur Distribuée</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-warning">
                        <div class="card-body text-center">
                            <i class="fas fa-exclamation-triangle fa-2x text-warning mb-2"></i>
                            <h4 class="text-warning"><?= formatMoney($totalBesoins) ?></h4>
                            <small class="text-muted">Besoins Totaux</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-info">
                        <div class="card-body text-center">
                            <i class="fas fa-chart-pie fa-2x text-info mb-2"></i>
                            <h4 class="text-info"><?= $tauxCouverture ?>%</h4>
                            <small class="text-muted">Taux de Couverture</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Détails des distributions -->
            <div class="card">
                <div class="card-header bg-primary bg-opacity-10">
                    <h5 class="mb-0">Détail des Distributions Simulées</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th><i class="fas fa-city"></i> Ville</th>
                                    <th><i class="fas fa-box"></i> Article</th>
                                    <th><i class="fas fa-calculator"></i> Quantité</th>
                                    <th><i class="fas fa-coins"></i> Valeur Unitaire</th>
                                    <th><i class="fas fa-money-bill-wave"></i> Valeur Totale</th>
                                    <th><i class="fas fa-heart"></i> Donateur</th>
                                    <th><i class="fas fa-calendar"></i> Date Distribution</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($distributions as $distribution): ?>
                                    <tr>
                                        <td>
                                            <strong class="text-primary">
                                                <?= getVilleNom($distribution['ville_id'], $villes) ?>
                                            </strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary"><?= $distribution['item'] ?></span>
                                        </td>
                                        <td>
                                            <strong><?= number_format($distribution['quantite_attribuee']) ?></strong>
                                        </td>
                                        <td><?= formatMoney($distribution['valeur_unitaire']) ?></td>
                                        <td>
                                            <strong class="text-success"><?= formatMoney($distribution['valeur_totale']) ?></strong>
                                        </td>
                                        <td>
                                            <small><?= htmlspecialchars($distribution['donateur']) ?></small>
                                        </td>
                                        <td>
                                            <small><?= date('d/m/Y H:i', strtotime($distribution['date_distribution'])) ?></small>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Répartition par ville -->
            <div class="row mt-4">
                <div class="col-12">
                    <h4><i class="fas fa-map-marker-alt"></i> Répartition par Ville</h4>
                </div>
                <?php 
                $repartitionVilles = [];
                foreach ($distributions as $distribution) {
                    $villeNom = getVilleNom($distribution['ville_id'], $villes);
                    if (!isset($repartitionVilles[$villeNom])) {
                        $repartitionVilles[$villeNom] = ['count' => 0, 'valeur' => 0];
                    }
                    $repartitionVilles[$villeNom]['count']++;
                    $repartitionVilles[$villeNom]['valeur'] += $distribution['valeur_totale'];
                }
                ?>
                
                <?php foreach ($repartitionVilles as $ville => $stats): ?>
                    <div class="col-md-4 mb-3">
                        <div class="card border-primary">
                            <div class="card-body">
                                <h5 class="card-title text-primary">
                                    <i class="fas fa-city"></i> <?= $ville ?>
                                </h5>
                                <p class="card-text">
                                    <strong><?= $stats['count'] ?></strong> distribution(s)<br>
                                    <span class="text-success"><?= formatMoney($stats['valeur']) ?></span>
                                </p>
                                <div class="progress">
                                    <div class="progress-bar bg-primary" role="progressbar" 
                                         style="width: <?= ($stats['valeur'] / $totalDistribue) * 100 ?>%">
                                        <?= round(($stats['valeur'] / $totalDistribue) * 100, 1) ?>%
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Règles de distribution -->
        <div class="card mt-4">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Règles de Distribution</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="fas fa-sort-amount-down text-primary"></i> Ordre de Priorité</h6>
                        <ul class="small">
                            <li>Les besoins sont traités par ordre chronologique (date de saisie)</li>
                            <li>Les besoins les plus anciens sont prioritaires</li>
                            <li>Distribution automatique selon les correspondances d'articles</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="fas fa-balance-scale text-success"></i> Répartition</h6>
                        <ul class="small">
                            <li>Un don peut couvrir plusieurs besoins partiellement</li>
                            <li>Les dons sont distribués dans l'ordre de leur réception</li>
                            <li>Les quantités non utilisées restent disponibles</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include(__DIR__ . '/../layout/layout.php');
?>