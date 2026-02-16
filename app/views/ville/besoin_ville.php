<?php
$title = "Besoins de " . htmlspecialchars($ville['nom']) . " - BNGRC";
ob_start();
?>

<div class="page-header mb-4">
    <h1 class="page-title">
        <i class="fas fa-list-ul"></i>
        Besoins de <?= htmlspecialchars($ville['nom']) ?>
    </h1>
    <p class="page-subtitle">
        <i class="fas fa-map-marker-alt"></i>
        Région: <?= htmlspecialchars($ville['region_nom'] ?? 'Non assignée') ?>
    </p>
</div>

<!-- Actions -->
<div class="row mb-3">
    <div class="col-12">
        <a href="/besoins" class="btn btn-primary">
            <i class="fas fa-plus"></i> Ajouter un Besoin
        </a>
        <a href="/" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour au Dashboard
        </a>
        <a href="/villes" class="btn btn-info">
            <i class="fas fa-city"></i> Toutes les Villes
        </a>
    </div>
</div>

<!-- Statistiques de la ville -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card primary">
            <i class="fas fa-list text-primary"></i>
            <div class="stats-number text-primary"><?= count($besoins) ?></div>
            <div class="stats-label">Besoins enregistrés</div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card success">
            <i class="fas fa-calculator text-success"></i>
            <?php
            $valeurTotale = 0;
            $totalArticles = 0;
            foreach ($besoins as $besoin) {
                if (isset($besoin['articles'])) {
                    foreach ($besoin['articles'] as $article) {
                        $valeurTotale += $article['quantite'] * $article['prix_unitaire'];
                        $totalArticles++;
                    }
                }
            }
            ?>
            <div class="stats-number text-success"><?= number_format($valeurTotale, 0, ',', ' ') ?></div>
            <div class="stats-label">Valeur totale (Ar)</div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card warning">
            <i class="fas fa-box text-warning"></i>
            <div class="stats-number text-warning"><?= $totalArticles ?></div>
            <div class="stats-label">Articles différents</div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card info">
            <i class="fas fa-clock text-info"></i>
            <div class="stats-number text-info">
                <?php
                if (!empty($besoins)) {
                    $dernierBesoin = max(array_column($besoins, 'date_saisie'));
                    echo date('d/m/Y', strtotime($dernierBesoin));
                } else {
                    echo 'N/A';
                }
                ?>
            </div>
            <div class="stats-label">Dernier ajout</div>
        </div>
    </div>
</div>

<!-- Liste des besoins -->
<div class="row">
    <div class="col-12">
        <?php if (empty($besoins)): ?>
            <div class="alert alert-info text-center py-5">
                <i class="fas fa-info-circle fa-3x mb-3"></i>
                <h4>Aucun besoin enregistré</h4>
                <p>Cette ville n'a pas encore de besoins enregistrés.</p>
                <a href="/besoins" class="btn btn-primary mt-3">
                    <i class="fas fa-plus"></i> Ajouter le premier besoin
                </a>
            </div>
        <?php else: ?>
            <?php foreach ($besoins as $besoin): ?>
                <div class="card mb-3 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-clipboard-list"></i>
                            Besoin #<?= $besoin['id'] ?>
                            <small class="float-end">
                                <i class="fas fa-calendar"></i>
                                <?= date('d/m/Y', strtotime($besoin['date_saisie'])) ?>
                            </small>
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($besoin['articles'])): ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Article</th>
                                            <th>Type</th>
                                            <th>Quantité</th>
                                            <th>Prix Unitaire</th>
                                            <th>Valeur Totale</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $totalBesoin = 0;
                                        foreach ($besoin['articles'] as $article): 
                                            $valeurArticle = $article['quantite'] * $article['prix_unitaire'];
                                            $totalBesoin += $valeurArticle;
                                        ?>
                                            <tr>
                                                <td>
                                                    <strong><?= htmlspecialchars($article['article_nom']) ?></strong>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">
                                                        <?= htmlspecialchars($article['type_besoin']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?= number_format($article['quantite'], 0, ',', ' ') ?>
                                                    <?= htmlspecialchars($article['unite']) ?>
                                                </td>
                                                <td>
                                                    <?= number_format($article['prix_unitaire'], 0, ',', ' ') ?> Ar
                                                </td>
                                                <td>
                                                    <strong class="text-success">
                                                        <?= number_format($valeurArticle, 0, ',', ' ') ?> Ar
                                                    </strong>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <th colspan="4" class="text-end">Total du besoin:</th>
                                            <th>
                                                <span class="text-success">
                                                    <?= number_format($totalBesoin, 0, ',', ' ') ?> Ar
                                                </span>
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                Aucun article associé à ce besoin
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- Résumé total -->
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line"></i>
                        Résumé Total
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <h3 class="text-primary"><?= count($besoins) ?></h3>
                            <p class="text-muted">Besoins</p>
                        </div>
                        <div class="col-md-4">
                            <h3 class="text-warning"><?= $totalArticles ?></h3>
                            <p class="text-muted">Articles</p>
                        </div>
                        <div class="col-md-4">
                            <h3 class="text-success"><?= number_format($valeurTotale, 0, ',', ' ') ?> Ar</h3>
                            <p class="text-muted">Valeur Totale</p>
                        </div>
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