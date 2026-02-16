<?php
$title = "Ajouter une Ville - BNGRC";
ob_start();
?>

<div class="row">
                <div class="col-md-8 col-lg-6">
                    <div class="card form-ville">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-city"></i> Nouvelle Ville
                            </h3>
                        </div>
                        <div class="card-body">
                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <?= htmlspecialchars($error) ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (isset($success)): ?>
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle"></i>
                                    <?= htmlspecialchars($success) ?>
                                </div>
                            <?php endif; ?>

                            <form method="POST" action="/villes/ajouter">
                                <div class="form-group">
                                    <label for="nom">
                                        <i class="fas fa-city"></i> Nom de la Ville *
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="nom" 
                                           name="nom" 
                                           placeholder="Ex: Antananarivo, Antsirabe..."
                                           value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>"
                                           required>
                                    <small class="form-text text-muted">
                                        Saisissez le nom de la ville
                                    </small>
                                </div>

                                <div class="form-group">
                                    <label for="id_region">
                                        <i class="fas fa-map"></i> Région *
                                    </label>
                                    <select class="form-control" id="id_region" name="id_region" required>
                                        <option value="">-- Sélectionner une région --</option>
                                        <?php foreach ($regions as $region): ?>
                                            <option value="<?= htmlspecialchars($region['idRegion'] ?? $region['id'] ?? '') ?>"
                                                    <?= (isset($_POST['id_region']) && $_POST['id_region'] == ($region['idRegion'] ?? $region['id'] ?? '')) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($region['nom'] ?? $region['name'] ?? '') ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-6">
                                            <button type="submit" class="btn btn-primary btn-block btn-avec-icone">
                                                <i class="fas fa-save"></i> Enregistrer
                                            </button>
                                        </div>
                                        <div class="col-6">
                                            <a href="/villes" class="btn btn-secondary btn-block btn-avec-icone">
                                                <i class="fas fa-arrow-left"></i> Retour
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Aide -->
                <div class="col-md-4 col-lg-6">
                    <div class="card aide-card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-info-circle"></i> Aide
                            </h3>
                        </div>
                        <div class="card-body">
                            <h5>Instructions</h5>
                            <ul>
                                <li><strong>Nom de la ville :</strong> Obligatoire, doit être unique</li>
                                <li><strong>Région :</strong> Obligatoire, sélectionnez parmi les régions existantes</li>
                            </ul>

                            <?php if (!empty($regions)): ?>
                                <h5>Régions disponibles (<?= count($regions) ?>)</h5>
                                <div class="d-flex flex-wrap">
                                    <?php foreach ($regions as $region): ?>
                                        <span class="badge badge-region badge-interactive mr-1 mb-1">
                                            <?= htmlspecialchars($region['nom'] ?? $region['name'] ?? 'N/A') ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-circle"></i> Aucune région disponible
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>



<?php
$content = ob_get_clean();
include(__DIR__ . '/../layout/layout.php');
?>