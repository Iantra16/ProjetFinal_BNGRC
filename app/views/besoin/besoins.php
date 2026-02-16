<?php
$title = "Gestion des besoins - BNGRC";
ob_start();
?>

<div class="page-header mb-4">
    <h1 class="page-title">
        <i class="fas fa-list-ul"></i>
        Gestion des besoins
    </h1>
    <p class="page-subtitle">Saisie et suivi des besoins par ville</p>
</div>

<div class="row">
    <!-- Formulaire d'ajout -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-plus"></i>
                    Ajouter un besoin
                </h3>
            </div>
            <div class="card-body">
                <form action="<?= BASE_URL ?>/besoins/ajouter" method="POST">
                    <div class="mb-3">
                        <label for="ville_id" class="form-label">Ville</label>
                        <select class="form-select" id="ville_id" name="ville_id" required>
                            <option value="">Sélectionner une ville...</option>
                            <?php foreach ($villes as $ville): ?>
                                <option value="<?= $ville['id'] ?>">
                                    <?= htmlspecialchars($ville['nom']) ?> - <?= htmlspecialchars($ville['region']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="item" class="form-label">Article demandé</label>
                        <input type="text" class="form-control" id="item" name="item" 
                               placeholder="Ex: Riz, Tôles ondulées, Ciment..." required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="quantite" class="form-label">Quantité</label>
                                <input type="number" class="form-control" id="quantite" name="quantite" 
                                       min="1" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="prix_unitaire" class="form-label">Prix unitaire (Ar)</label>
                                <input type="number" class="form-control" id="prix_unitaire" name="prix_unitaire" 
                                       min="0" step="0.01" required>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save"></i> Enregistrer le besoin
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Liste des besoins -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-list"></i>
                    Liste des besoins enregistrés
                </h3>
                <div class="card-tools">
                    <div class="input-group input-group-sm" style="width: 250px;">
                        <input type="text" class="form-control" placeholder="Rechercher..." id="searchInput">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-default">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover" id="besoinsTable">
                        <thead>
                            <tr>
                                <th>Ville</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Quantité</th>
                                <th>Prix unit.</th>
                                <th>Valeur totale</th>
                                <th>Priorité</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($besoins as $besoin): ?>
                                <tr>
                                    <td>
                                        <?php
                                        // Trouver le nom de la ville
                                        $nomVille = 'Ville inconnue';
                                        foreach ($villes as $ville) {
                                            if ($ville['id'] == $besoin['ville_id']) {
                                                $nomVille = $ville['nom'];
                                                break;
                                            }
                                        }
                                        ?>
                                        <strong><?= htmlspecialchars($nomVille) ?></strong>
                                    </td>
                                    <td>
                                        <?php
                                        // Déterminer le type basé sur l'item
                                        $item = strtolower($besoin['item']);
                                        if (in_array($item, ['riz', 'huile', 'sucre', 'lait', 'farine'])) {
                                            $type = 'nature';
                                            $typeClass = 'success';
                                            $typeIcon = 'leaf';
                                        } elseif (in_array($item, ['tôle', 'ciment', 'clou', 'bois', 'fer'])) {
                                            $type = 'materiaux';
                                            $typeClass = 'warning';
                                            $typeIcon = 'tools';
                                        } elseif (in_array($item, ['argent', 'argent liquide'])) {
                                            $type = 'argent';
                                            $typeClass = 'info';
                                            $typeIcon = 'coins';
                                        } else {
                                            $type = 'autre';
                                            $typeClass = 'secondary';
                                            $typeIcon = 'question';
                                        }
                                        ?>
                                        <span class="badge bg-<?= $typeClass ?>">
                                            <i class="fas fa-<?= $typeIcon ?>"></i>
                                            <?= ucfirst($type) ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($besoin['item']) ?></td>
                                    <td>
                                        <span class="badge bg-primary">
                                            <?= number_format($besoin['quantite'], 0, ',', ' ') ?>
                                        </span>
                                    </td>
                                    <td><?= number_format($besoin['prix_unitaire'], 0, ',', ' ') ?> Ar</td>
                                    <td>
                                        <strong><?= number_format($besoin['prix_unitaire'] * $besoin['quantite'], 0, ',', ' ') ?> Ar</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">Normale</span>
                                    </td>
                                    <td>
                                        <small><?= date('d/m/Y', strtotime($besoin['date_saisie'])) ?></small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                                    title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                    title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-sm-12 col-md-5">
                        <div class="dataTables_info">
                            Affichage de <?= count($besoins) ?> besoins au total
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-7">
                        <div class="float-end">
                            <strong>
                                Valeur totale des besoins: 
                                <?= number_format(array_sum(array_map(fn($b) => $b['prix_unitaire'] * $b['quantite'], $besoins)), 0, ',', ' ') ?> Ar
                            </strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistiques par type -->
<div class="row mt-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-leaf"></i>
                    Besoins en nature
                </h3>
            </div>
            <div class="card-body">
                <?php
                $besoinsNature = array_filter($besoins, function($b) {
                    $item = strtolower($b['item']);
                    return in_array($item, ['riz', 'huile', 'sucre', 'lait', 'farine']);
                });
                $valeurNature = array_sum(array_map(fn($b) => $b['prix_unitaire'] * $b['quantite'], $besoinsNature));
                ?>
                <div class="text-center">
                    <h2 class="text-success"><?= count($besoinsNature) ?></h2>
                    <p>Types d'aliments</p>
                    <p><strong><?= number_format($valeurNature, 0, ',', ' ') ?> Ar</strong></p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-tools"></i>
                    Besoins en matériaux
                </h3>
            </div>
            <div class="card-body">
                <?php
                $besoinsMateriaux = array_filter($besoins, function($b) {
                    $item = strtolower($b['item']);
                    return in_array($item, ['tôle', 'ciment', 'clou', 'bois', 'fer']);
                });
                $valeurMateriaux = array_sum(array_map(fn($b) => $b['prix_unitaire'] * $b['quantite'], $besoinsMateriaux));
                ?>
                <div class="text-center">
                    <h2 class="text-warning"><?= count($besoinsMateriaux) ?></h2>
                    <p>Types de matériaux</p>
                    <p><strong><?= number_format($valeurMateriaux, 0, ',', ' ') ?> Ar</strong></p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-coins"></i>
                    Besoins en argent
                </h3>
            </div>
            <div class="card-body">
                <?php
                $besoinsArgent = array_filter($besoins, function($b) {
                    $item = strtolower($b['item']);
                    return in_array($item, ['argent', 'argent liquide']);
                });
                $valeurArgent = array_sum(array_map(fn($b) => $b['prix_unitaire'] * $b['quantite'], $besoinsArgent));
                ?>
                <div class="text-center">
                    <h2 class="text-info"><?= count($besoinsArgent) ?></h2>
                    <p>Demandes d'aide</p>
                    <p><strong><?= number_format($valeurArgent, 0, ',', ' ') ?> Ar</strong></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fonction de recherche
    const searchInput = document.getElementById('searchInput');
    const table = document.getElementById('besoinsTable');
    
    if (searchInput && table) {
        searchInput.addEventListener('keyup', function() {
            const filter = this.value.toUpperCase();
            const rows = table.getElementsByTagName('tr');
            
            for (let i = 1; i < rows.length; i++) {
                const row = rows[i];
                const cells = row.getElementsByTagName('td');
                let found = false;
                
                for (let j = 0; j < cells.length; j++) {
                    if (cells[j].textContent.toUpperCase().indexOf(filter) > -1) {
                        found = true;
                        break;
                    }
                }
                
                row.style.display = found ? '' : 'none';
            }
        });
    }
});
</script>

<?php
$content = ob_get_clean();
include(__DIR__ . '/../layout/layout.php');
?>