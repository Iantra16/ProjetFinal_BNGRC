<?php 
$title = isset($ville) && $ville ? "Distributions - " . htmlspecialchars($ville['nom']) : "Distributions - BNGRC";
ob_start();
?>

<div class="page-header mb-4">
    <h1 class="page-title">
        <i class="fas fa-truck"></i>
        <?php if (isset($ville) && $ville): ?>
            Distributions pour <?= htmlspecialchars($ville['nom']) ?>
        <?php else: ?>
            Toutes les distributions
        <?php endif; ?>
    </h1>
    <p class="page-subtitle">
        <?php if (isset($ville) && $ville): ?>
            <i class="fas fa-map-marker-alt"></i>
            Région: <?= htmlspecialchars($ville['region_nom'] ?? 'Non assignée') ?>
        <?php else: ?>
            Vue d'ensemble de toutes les distributions
        <?php endif; ?>
    </p>
</div>

<!-- Actions -->
<div class="row mb-3">
    <div class="col-12">
        <a href="<?= BASE_URL ?>/" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour au Dashboard
        </a>
        <?php if (isset($ville) && $ville): ?>
            <a href="<?= BASE_URL ?>/distributions" class="btn btn-info">
                <i class="fas fa-list"></i> Toutes les Distributions
            </a>
            <a href="<?= BASE_URL ?>/villes/<?= $ville['id'] ?>/besoins" class="btn btn-primary">
                <i class="fas fa-list-ul"></i> Voir les Besoins
            </a>
        <?php else: ?>
            <a href="<?= BASE_URL ?>/villes" class="btn btn-info">
                <i class="fas fa-city"></i> Voir les Villes
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Statistiques -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card primary">
            <i class="fas fa-truck text-primary"></i>
            <div class="stats-number text-primary"><?= count($distributions) ?></div>
            <div class="stats-label">Distributions effectuées</div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card success">
            <i class="fas fa-calculator text-success"></i>
            <?php
            $valeurTotale = 0;
            foreach ($distributions as $dist) {
                $valeurTotale += $dist['valeur_totale'];
            }
            ?>
            <div class="stats-number text-success"><?= number_format($valeurTotale, 0, ',', ' ') ?></div>
            <div class="stats-label">Valeur totale (Ar)</div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card warning">
            <i class="fas fa-box text-warning"></i>
            <?php
            $articlesUniques = array_unique(array_column($distributions, 'article_nom'));
            ?>
            <div class="stats-number text-warning"><?= count($articlesUniques) ?></div>
            <div class="stats-label">Articles différents</div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card info">
            <i class="fas fa-clock text-info"></i>
            <div class="stats-number text-info">
                <?php
                if (!empty($distributions)) {
                    $derniereDistrib = max(array_column($distributions, 'date_distribution'));
                    echo date('d/m/Y', strtotime($derniereDistrib));
                } else {
                    echo 'N/A';
                }
                ?>
            </div>
            <div class="stats-label">Dernière distribution</div>
        </div>
    </div>
</div>

<!-- Liste des distributions -->
<div class="row">
    <div class="col-12">
        <?php if (empty($distributions)): ?>
            <div class="alert alert-info text-center py-5">
                <i class="fas fa-info-circle fa-3x mb-3"></i>
                <h4>Aucune distribution enregistrée</h4>
                <p>
                    <?php if (isset($ville) && $ville): ?>
                        Cette ville n'a pas encore reçu de distributions.
                    <?php else: ?>
                        Aucune distribution n'a été enregistrée dans le système.
                    <?php endif; ?>
                </p>
            </div>
        <?php else: ?>
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-list"></i>
                        Liste des distributions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <?php if (!isset($ville) || !$ville): ?>
                                        <th>Ville</th>
                                    <?php endif; ?>
                                    <th>Article</th>
                                    <th>Quantité</th>
                                    <th>Prix Unit.</th>
                                    <th>Valeur</th>
                                    <th>Donateur</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($distributions as $dist): ?>
                                    <tr>
                                        <td>
                                            <small>
                                                <i class="fas fa-calendar"></i>
                                                <?= date('d/m/Y', strtotime($dist['date_distribution'])) ?>
                                            </small>
                                        </td>
                                        <?php if (!isset($ville) || !$ville): ?>
                                            <td>
                                                <strong><?= htmlspecialchars($dist['ville_nom']) ?></strong>
                                            </td>
                                        <?php endif; ?>
                                        <td>
                                            <i class="fas fa-box"></i>
                                            <?= htmlspecialchars($dist['article_nom']) ?>
                                        </td>
                                        <td>
                                            <?= number_format($dist['quantite_attribuee'], 0, ',', ' ') ?>
                                            <?= htmlspecialchars($dist['unite']) ?>
                                        </td>
                                        <td>
                                            <?= number_format($dist['prix_unitaire'], 0, ',', ' ') ?> Ar
                                        </td>
                                        <td>
                                            <strong class="text-success">
                                                <?= number_format($dist['valeur_totale'], 0, ',', ' ') ?> Ar
                                            </strong>
                                        </td>
                                        <td>
                                            <i class="fas fa-user"></i>
                                            <?= htmlspecialchars($dist['donateur'] ?? 'Anonyme') ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="<?= (!isset($ville) || !$ville) ? 5 : 4 ?>" class="text-end">Total:</th>
                                    <th>
                                        <span class="text-success">
                                            <?= number_format($valeurTotale, 0, ',', ' ') ?> Ar
                                        </span>
                                    </th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
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