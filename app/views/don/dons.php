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
                $totalValeur = 0;
                $totalArticles = 0;
                
                // Calculer la valeur totale de tous les dons
                foreach ($dons as $don) {
                    if (isset($don['articles']) && is_array($don['articles'])) {
                        foreach ($don['articles'] as $article) {
                            $totalValeur += $article['quantite'] * $article['prix_unitaire'];
                            $totalArticles++;
                        }
                    }
                }
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
                            <i class="fas fa-box fa-2x text-info mb-2"></i>
                            <h4 class="text-info"><?= $totalArticles ?></h4>
                            <small class="text-muted">Articles différents</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-warning">
                        <div class="card-body text-center">
                            <i class="fas fa-user fa-2x text-warning mb-2"></i>
                            <h4 class="text-warning"><?= count(array_unique(array_column($dons, 'donateur'))) ?></h4>
                            <small class="text-muted">Donateurs</small>
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
                                    <th><i class="fas fa-box"></i> Articles</th>
                                    <th><i class="fas fa-money-bill-wave"></i> Valeur Totale</th>
                                    <th><i class="fas fa-calendar"></i> Date du Don</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                // Trier les dons par date (plus récent en premier)
                                usort($dons, function($a, $b) {
                                    return strtotime($b['date_don']) - strtotime($a['date_don']);
                                });
                                
                                foreach ($dons as $don): 
                                    // Calculer la valeur totale de ce don
                                    $valeurTotaleDon = 0;
                                    if (isset($don['articles']) && is_array($don['articles'])) {
                                        foreach ($don['articles'] as $article) {
                                            $valeurTotaleDon += $article['quantite'] * $article['prix_unitaire'];
                                        }
                                    }
                                ?>
                                    <tr>
                                        <td>
                                            <strong><?= htmlspecialchars($don['donateur'] ?? 'Anonyme') ?></strong>
                                        </td>
                                        <td>
                                            <?php if (isset($don['articles']) && is_array($don['articles'])): ?>
                                                <ul class="list-unstyled mb-0">
                                                    <?php foreach ($don['articles'] as $article): ?>
                                                        <li>
                                                            <span class="badge bg-secondary"><?= htmlspecialchars($article['article_nom'] ?? 'Article') ?></span>
                                                            <strong><?= number_format($article['quantite'], 2) ?></strong> <?= htmlspecialchars($article['unite'] ?? '') ?>
                                                            <small class="text-muted">(<?= formatMoney($article['quantite'] * $article['prix_unitaire']) ?>)</small>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            <?php else: ?>
                                                <span class="text-muted">Aucun article</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong class="text-success"><?= formatMoney($valeurTotaleDon) ?></strong>
                                        </td>
                                        <td>
                                            <small><?= date('d/m/Y H:i', strtotime($don['date_don'])) ?></small>
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
                    if (isset($don['articles']) && is_array($don['articles'])) {
                        foreach ($don['articles'] as $article) {
                            $nom = $article['article_nom'];
                            if (!isset($repartitionArticles[$nom])) {
                                $repartitionArticles[$nom] = [
                                    'quantite' => 0, 
                                    'valeur' => 0, 
                                    'donateurs' => [],
                                    'unite' => $article['unite']
                                ];
                            }
                            $repartitionArticles[$nom]['quantite'] += $article['quantite'];
                            $repartitionArticles[$nom]['valeur'] += $article['quantite'] * $article['prix_unitaire'];
                            if (!in_array($don['donateur'], $repartitionArticles[$nom]['donateurs'])) {
                                $repartitionArticles[$nom]['donateurs'][] = $don['donateur'];
                            }
                        }
                    }
                }
                ?>
                
                <?php if (!empty($repartitionArticles)): ?>
                    <?php foreach ($repartitionArticles as $article => $stats): ?>
                        <div class="col-md-4 mb-3">
                            <div class="card border-success">
                                <div class="card-body">
                                    <h5 class="card-title text-success">
                                        <i class="fas fa-box"></i> <?= htmlspecialchars($article ?? 'Article') ?>
                                    </h5>
                                    <p class="card-text">
                                        <br>
                                        <strong>Quantité :</strong> <?= number_format($stats['quantite'], 2) ?> <?= $stats['unite'] ?><br>
                                        <strong>Valeur :</strong> <?= formatMoney($stats['valeur']) ?><br>
                                        <strong>Donateurs :</strong> <?= count($stats['donateurs']) ?>
                                    </p>
                                    <?php if ($totalValeur > 0): ?>
                                        <div class="progress">
                                            <div class="progress-bar bg-success" role="progressbar" 
                                                 style="width: <?= ($stats['valeur'] / $totalValeur) * 100 ?>%">
                                                <?= round(($stats['valeur'] / $totalValeur) * 100, 1) ?>%
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Aucune répartition disponible.
                        </div>
                    </div>
                <?php endif; ?>
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
                        
                        // Calculer la valeur de ce don
                        if (isset($don['articles']) && is_array($don['articles'])) {
                            foreach ($don['articles'] as $article) {
                                $donateurs[$don['donateur']]['valeur'] += $article['quantite'] * $article['prix_unitaire'];
                            }
                        }
                    }
                    arsort($donateurs);
                    $topDonateurs = array_slice($donateurs, 0, 5, true);
                    ?>
                    
                    <?php if (!empty($topDonateurs)): ?>
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
                                            <h6 class="mb-0"><?= htmlspecialchars($donateur ?? 'Anonyme') ?></h6>
                                            <small class="text-muted">
                                                <?= $stats['dons'] ?> don(s) - <?= formatMoney($stats['valeur']) ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            <?php $position++; endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle"></i> Aucun donateur pour le moment.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include(__DIR__ . '/../layout/layout.php');
?>