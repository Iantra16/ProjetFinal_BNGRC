<?php
$title = "Besoins de " . htmlspecialchars($ville['nom']) . " - BNGRC";
ob_start();
?>

<!-- Actions -->
            <div class="row mb-3">
                <div class="col-12">
                    <a href="/villes/<?= $ville['id'] ?>/besoins/ajouter" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Ajouter un Besoin
                    </a>
                    <a href="/villes" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour aux Villes
                    </a>
                    <a href="/villes/besoins" class="btn btn-info">
                        <i class="fas fa-exchange-alt"></i> Changer de Ville
                    </a>
                </div>
            </div>

            <!-- Statistiques de la ville -->
            <div class="row mb-3">
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-info">
                            <i class="fas fa-list"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Besoins</span>
                            <span class="info-box-number"><?= count($besoins) ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-success">
                            <i class="fas fa-calculator"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Valeur Totale</span>
                            <span class="info-box-number">
                                <?php
                                $valeurTotale = 0;
                                foreach ($besoins as $besoin) {
                                    $valeurTotale += $besoin['quantite'] * $besoin['prix_unitaire'];
                                }
                                echo number_format($valeurTotale, 0, ',', ' ') . ' Ar';
                                ?>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-warning">
                            <i class="fas fa-tags"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Catégories</span>
                            <span class="info-box-number">
                                <?php
                                $categories = array_unique(array_column($besoins, 'categorie_id'));
                                echo count($categories);
                                ?>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-danger">
                            <i class="fas fa-clock"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Dernier Ajout</span>
                            <span class="info-box-number">
                                <?php
                                if (!empty($besoins)) {
                                    $dernierBesoin = max(array_column($besoins, 'date_ajout'));
                                    echo date('d/m', strtotime($dernierBesoin));
                                } else {
                                    echo 'Aucun';
                                }
                                ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Liste des besoins -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-clipboard-list"></i> 
                                Besoins de <?= htmlspecialchars($ville['nom']) ?>
                            </h3>
                        </div>
                        <div class="card-body">
                            <?php if (empty($besoins)): ?>
                                <div class="alert alert-info text-center">
                                    <i class="fas fa-info-circle fa-2x mb-3"></i>
                                    <h5>Aucun besoin enregistré</h5>
                                    <p>Cette ville n'a pas encore de besoins enregistrés.</p>
                                    <a href="/villes/<?= $ville['id'] ?>/besoins/ajouter" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Ajouter le premier besoin
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Article</th>
                                                <th>Catégorie</th>
                                                <th>Quantité</th>
                                                <th>Prix Unitaire</th>
                                                <th>Valeur Totale</th>
                                                <th>Date d'Ajout</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($besoins as $besoin): ?>
                                                <?php
                                                $categorieName = '';
                                                foreach ($categories as $cat) {
                                                    if ($cat['id'] == $besoin['categorie_id']) {
                                                        $categorieName = $cat['nom'];
                                                        break;
                                                    }
                                                }
                                                $valeurTotaleBesoin = $besoin['quantite'] * $besoin['prix_unitaire'];
                                                ?>
                                                <tr>
                                                    <td>
                                                        <strong><?= htmlspecialchars($besoin['article']) ?></strong>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-primary">
                                                            <?= htmlspecialchars($categorieName) ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?= number_format($besoin['quantite'], 0, ',', ' ') ?>
                                                        <?= htmlspecialchars($besoin['unite']) ?>
                                                    </td>
                                                    <td>
                                                        <?= number_format($besoin['prix_unitaire'], 0, ',', ' ') ?> Ar
                                                    </td>
                                                    <td>
                                                        <strong class="text-success">
                                                            <?= number_format($valeurTotaleBesoin, 0, ',', ' ') ?> Ar
                                                        </strong>
                                                    </td>
                                                    <td>
                                                        <small>
                                                            <?= date('d/m/Y H:i', strtotime($besoin['date_ajout'])) ?>
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <button class="btn btn-sm btn-warning" 
                                                                    onclick="alert('Fonction de modification à implémenter')">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-danger" 
                                                                    onclick="confirmerSuppression(<?= $besoin['id'] ?>)">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Résumé par catégorie -->
                                <div class="mt-4">
                                    <h5>Résumé par Catégorie</h5>
                                    <div class="row">
                                        <?php
                                        $resumeCategories = [];
                                        foreach ($besoins as $besoin) {
                                            $catId = $besoin['categorie_id'];
                                            if (!isset($resumeCategories[$catId])) {
                                                $resumeCategories[$catId] = [
                                                    'nom' => '',
                                                    'count' => 0,
                                                    'valeur' => 0
                                                ];
                                                // Trouver le nom de la catégorie
                                                foreach ($categories as $cat) {
                                                    if ($cat['id'] == $catId) {
                                                        $resumeCategories[$catId]['nom'] = $cat['nom'];
                                                        break;
                                                    }
                                                }
                                            }
                                            $resumeCategories[$catId]['count']++;
                                            $resumeCategories[$catId]['valeur'] += $besoin['quantite'] * $besoin['prix_unitaire'];
                                        }
                                        ?>
                                        
                                        <?php foreach ($resumeCategories as $catResume): ?>
                                            <div class="col-md-4 col-sm-6 col-12 mb-2">
                                                <div class="small-box bg-light">
                                                    <div class="inner">
                                                        <h4><?= $catResume['count'] ?> article<?= $catResume['count'] > 1 ? 's' : '' ?></h4>
                                                        <p><?= htmlspecialchars($catResume['nom']) ?></p>
                                                        <small class="text-muted">
                                                            Valeur: <?= number_format($catResume['valeur'], 0, ',', ' ') ?> Ar
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmerSuppression(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce besoin ?')) {
        // Ici, vous pourriez implémenter la suppression
        alert('Fonction de suppression à implémenter pour le besoin ID: ' + id);
    }
}
</script>

<?php
$content = ob_get_clean();
include(__DIR__ . '/../layout/layout.php');
?>