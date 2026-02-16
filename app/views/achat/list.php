<?php 
$title = "Liste des Achats - BNGRC";
ob_start();
?>

<div class="page-header mb-4">
    <h1 class="page-title">
        <i class="fas fa-shopping-cart"></i> Liste des Achats
    </h1>
    <p class="page-subtitle">
        Achats effectués avec les dons en argent.
    </p>
</div>

<?php if (!empty($success)): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($success) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row mb-3">
    <div class="col-md-6">
        <!-- Filtre par ville -->
        <form method="GET" action="<?= BASE_URL ?>/achats" class="d-flex gap-2">
            <select name="ville" class="form-select" onchange="this.form.submit()">
                <option value="">-- Toutes les villes --</option>
                <?php foreach ($villes ?? [] as $ville): ?>
                    <option value="<?= $ville['id'] ?>" <?= (isset($villeSelectionnee) && $villeSelectionnee['id'] == $ville['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($ville['nom']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>
    <div class="col-md-6 text-end">
        <a href="<?= BASE_URL ?>/achats/ajouter" class="btn btn-success">
            <i class="fas fa-plus"></i> Nouvel Achat
        </a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-list"></i> 
            <?php if (isset($villeSelectionnee)): ?>
                Achats pour <?= htmlspecialchars($villeSelectionnee['nom']) ?>
            <?php else: ?>
                Tous les achats
            <?php endif; ?>
        </h5>
        <span class="badge bg-primary"><?= count($achats) ?> achat(s)</span>
    </div>
    <div class="card-body p-0">
        <?php if (empty($achats)): ?>
            <div class="text-center py-5 text-muted">
                <i class="fas fa-shopping-basket fa-3x mb-3 opacity-25"></i>
                <p>Aucun achat enregistré.</p>
                <a href="<?= BASE_URL ?>/achats/ajouter" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Effectuer un achat
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Article</th>
                            <th class="text-end">Quantité</th>
                            <th class="text-end">Prix unit.</th>
                            <th class="text-end">Frais</th>
                            <th class="text-end">Total</th>
                            <th>Donateur</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($achats as $achat): ?>
                            <tr>
                                <td><span class="badge bg-secondary">#<?= $achat['id'] ?></span></td>
                                <td>
                                    <small><?= date('d/m/Y H:i', strtotime($achat['date_achat'])) ?></small>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($achat['article_nom']) ?></strong>
                                </td>
                                <td class="text-end">
                                    <?= number_format($achat['quantite'], 0, ',', ' ') ?>
                                    <small class="text-muted"><?= htmlspecialchars($achat['unite']) ?></small>
                                </td>
                                <td class="text-end">
                                    <?= number_format($achat['prix_unitaire'], 0, ',', ' ') ?> Ar
                                </td>
                                <td class="text-end">
                                    <span class="badge bg-warning text-dark"><?= $achat['frais_pourcent'] ?>%</span>
                                </td>
                                <td class="text-end">
                                    <strong class="text-success">
                                        <?= number_format($achat['montant_total'], 0, ',', ' ') ?> Ar
                                    </strong>
                                </td>
                                <td>
                                    <small><?= htmlspecialchars($achat['donateur'] ?? 'Anonyme') ?></small>
                                </td>
                                <td class="text-center">
                                    <form action="<?= BASE_URL ?>/achats/supprimer/<?= $achat['id'] ?>" method="POST" 
                                          onsubmit="return confirm('Supprimer cet achat ?');" style="display:inline;">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="6" class="text-end">Total :</th>
                            <th class="text-end text-success">
                                <?= number_format(array_sum(array_column($achats, 'montant_total')), 0, ',', ' ') ?> Ar
                            </th>
                            <th colspan="2"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include(__DIR__ . '/../layout/layout.php');
?>
