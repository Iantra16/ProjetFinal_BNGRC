<?php
$title = "Sélectionner une Ville - BNGRC";
ob_start();
?>

<div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-map-marker-alt"></i> Choisir une ville pour gérer ses besoins
                            </h3>
                        </div>
                        <div class="card-body">
                            <?php if (empty($villes)): ?>
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Aucune ville disponible. 
                                    <a href="/villes/ajouter" class="alert-link">Ajoutez une ville d'abord</a>.
                                </div>
                            <?php else: ?>
                                <div class="row">
                                    <?php foreach ($villes as $ville): ?>
                                        <div class="col-md-4 col-sm-6 col-12 mb-3">
                                            <div class="card ville-selection-card">
                                                <div class="card-body">
                                                    <h5 class="card-title">
                                                        <i class="fas fa-city text-primary"></i>
                                                        <?= htmlspecialchars($ville['nom']) ?>
                                                    </h5>
                                                    <p class="card-text">
                                                        <small class="text-muted">
                                                            <i class="fas fa-map"></i>
                                                            Région: <?= htmlspecialchars($ville['region']) ?>
                                                        </small>
                                                    </p>
                                                    <div class="btn-group-vertical w-100" role="group">
                                                        <a href="/villes/<?= $ville['id'] ?>/besoins" 
                                                           class="btn btn-primary btn-avec-icone">
                                                            <i class="fas fa-list"></i> Voir les Besoins
                                                        </a>
                                                        <a href="/villes/<?= $ville['id'] ?>/besoins/ajouter" 
                                                           class="btn btn-success btn-avec-icone">
                                                            <i class="fas fa-plus"></i> Ajouter un Besoin
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="row">
                <div class="col-12">
                    <div class="card ville-card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-bolt"></i> Actions Rapides
                            </h3>
                        </div>
                        <div class="card-body actions-rapides">
                            <div class="row">
                                <div class="col-md-3 col-sm-6 col-12 mb-2">
                                    <a href="/villes" class="btn btn-info btn-block btn-avec-icone">
                                        <i class="fas fa-list"></i> Toutes les Villes
                                    </a>
                                </div>
                                <div class="col-md-3 col-sm-6 col-12 mb-2">
                                    <a href="/villes/ajouter" class="btn btn-primary btn-block btn-avec-icone">
                                        <i class="fas fa-plus"></i> Ajouter une Ville
                                    </a>
                                </div>
                                <div class="col-md-3 col-sm-6 col-12 mb-2">
                                    <a href="/besoins" class="btn btn-success btn-block btn-avec-icone">
                                        <i class="fas fa-clipboard-list"></i> Tous les Besoins
                                    </a>
                                </div>
                                <div class="col-md-3 col-sm-6 col-12 mb-2">
                                    <a href="/dashboard" class="btn btn-warning btn-block btn-avec-icone">
                                        <i class="fas fa-chart-pie"></i> Tableau de Bord
                                    </a>
                                </div>
                            </div>
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