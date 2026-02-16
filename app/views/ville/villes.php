<?php
$title = "Gestion des Villes - BNGRC";
ob_start();

// Initialiser la variable si elle n'existe pas
$villes = $villes ?? [];
?>

<!-- Actions -->
<div class="row mb-3">
    <div class="col-12">
        <a href="<?= BASE_URL ?>/villes/ajouter" class="btn btn-primary">
            <i class="fas fa-plus"></i> Ajouter une Ville
        </a>
        <a href="<?= BASE_URL ?>/villes/besoins" class="btn btn-success">
            <i class="fas fa-list"></i> Gérer les Besoins par Ville
        </a>
    </div>
</div>

            <!-- Liste des villes -->
            <div class="row">
                <div class="col-12">
                    <div class="card ville-card">
                        <div class="card-header">
                            <h3 class="card-title">Liste des Villes (<?= count($villes) ?>)</h3>
                        </div>
                        <div class="card-body">
                            <?php if (empty($villes)): ?>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    Aucune ville enregistrée pour le moment.
                                </div>
                            <?php else: ?>
                                <div class="table-responsive besoins-table">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Nom de la Ville</th>
                                                <th>Région</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($villes as $ville): ?>
                                                <tr>
                                                    <td>
                                                        <strong><?= htmlspecialchars($ville['nom']) ?></strong>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-region">
                                                            <?= htmlspecialchars($ville['region_nom'] ?? 'N/A') ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <a href="<?= BASE_URL ?>/villes/<?= $ville['id'] ?>/besoins" 
                                                               class="btn btn-sm btn-success btn-avec-icone" 
                                                               title="Gérer les besoins">
                                                                <i class="fas fa-list"></i> Besoins
                                                            </a>
                                                            <a href="<?= BASE_URL ?>/besoins/<?= $ville['id'] ?>" 
                                                               class="btn btn-sm btn-primary btn-avec-icone" 
                                                               title="Ajouter un besoin">
                                                                <i class="fas fa-plus"></i> Ajouter Besoin
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
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