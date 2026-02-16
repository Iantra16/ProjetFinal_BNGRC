<?php 
$title = "Récapitulation - BNGRC";
ob_start();
?>

<div class="page-header mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">
                <i class="fas fa-chart-pie"></i> Récapitulation
            </h1>
            <p class="page-subtitle">
                Vue d'ensemble des besoins et des distributions.
            </p>
        </div>
        <button id="btn-actualiser" class="btn btn-primary btn-lg">
            <i class="fas fa-sync-alt"></i> Actualiser
        </button>
    </div>
</div>

<!-- Cartes récapitulatives -->
<div class="row mb-4" id="recap-cards">
    <div class="col-md-4">
        <div class="card shadow-sm border-start border-primary border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted text-uppercase small">Besoins Totaux</h6>
                        <h2 class="mb-0" id="montant-total">
                            <?= number_format($recap['montant_total_besoins'], 0, ',', ' ') ?> Ar
                        </h2>
                    </div>
                    <div class="bg-primary bg-opacity-10 p-3 rounded">
                        <i class="fas fa-clipboard-list fa-2x text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card shadow-sm border-start border-success border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted text-uppercase small">Besoins Satisfaits</h6>
                        <h2 class="mb-0 text-success" id="montant-satisfait">
                            <?= number_format($recap['montant_besoins_satisfaits'], 0, ',', ' ') ?> Ar
                        </h2>
                        <small class="text-muted" id="pourcentage-satisfait">
                            <?= $recap['pourcentage_satisfait'] ?>% du total
                        </small>
                    </div>
                    <div class="bg-success bg-opacity-10 p-3 rounded">
                        <i class="fas fa-check-circle fa-2x text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card shadow-sm border-start border-danger border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted text-uppercase small">Besoins Restants</h6>
                        <h2 class="mb-0 text-danger" id="montant-restant">
                            <?= number_format($recap['montant_besoins_restants'], 0, ',', ' ') ?> Ar
                        </h2>
                        <small class="text-muted" id="pourcentage-restant">
                            <?= 100 - $recap['pourcentage_satisfait'] ?>% du total
                        </small>
                    </div>
                    <div class="bg-danger bg-opacity-10 p-3 rounded">
                        <i class="fas fa-exclamation-triangle fa-2x text-danger"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Barre de progression -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between mb-2">
            <span>Progression globale</span>
            <span id="progress-text"><?= $recap['pourcentage_satisfait'] ?>%</span>
        </div>
        <div class="progress" style="height: 25px;">
            <div class="progress-bar bg-success" 
                 role="progressbar" 
                 id="progress-bar"
                 style="width: <?= $recap['pourcentage_satisfait'] ?>%"
                 aria-valuenow="<?= $recap['pourcentage_satisfait'] ?>" 
                 aria-valuemin="0" 
                 aria-valuemax="100">
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Récap par ville -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0"><i class="fas fa-city"></i> Par Ville</h5>
            </div>
            <div class="card-body p-0" style="max-height: 400px; overflow-y: auto;">
                <table class="table table-hover mb-0">
                    <thead class="table-light sticky-top">
                        <tr>
                            <th>Ville</th>
                            <th class="text-end">Total</th>
                            <th class="text-end">Satisfait</th>
                            <th class="text-end">%</th>
                        </tr>
                    </thead>
                    <tbody id="table-villes">
                        <?php foreach ($recap_par_ville as $ville): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($ville['ville_nom']) ?></strong>
                                    <br><small class="text-muted"><?= htmlspecialchars($ville['region_nom']) ?></small>
                                </td>
                                <td class="text-end">
                                    <?= number_format($ville['montant_total'], 0, ',', ' ') ?> Ar
                                </td>
                                <td class="text-end text-success">
                                    <?= number_format($ville['montant_satisfait'], 0, ',', ' ') ?> Ar
                                </td>
                                <td class="text-end">
                                    <div class="progress" style="width: 60px; height: 20px;">
                                        <div class="progress-bar bg-<?= $ville['pourcentage_satisfait'] >= 50 ? 'success' : 'warning' ?>" 
                                             style="width: <?= $ville['pourcentage_satisfait'] ?>%">
                                        </div>
                                    </div>
                                    <small><?= $ville['pourcentage_satisfait'] ?>%</small>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Récap par type -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0"><i class="fas fa-tags"></i> Par Type de Besoin</h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Type</th>
                            <th class="text-end">Total</th>
                            <th class="text-end">Satisfait</th>
                            <th class="text-end">Restant</th>
                        </tr>
                    </thead>
                    <tbody id="table-types">
                        <?php foreach ($recap_par_type as $type): ?>
                            <tr>
                                <td>
                                    <?php
                                    $icon = 'box';
                                    $color = 'secondary';
                                    if ($type['type_libelle'] == 'Nature') { $icon = 'seedling'; $color = 'success'; }
                                    if ($type['type_libelle'] == 'Materiaux') { $icon = 'tools'; $color = 'warning'; }
                                    if ($type['type_libelle'] == 'Argent') { $icon = 'money-bill'; $color = 'info'; }
                                    ?>
                                    <i class="fas fa-<?= $icon ?> text-<?= $color ?>"></i>
                                    <?= htmlspecialchars($type['type_libelle']) ?>
                                </td>
                                <td class="text-end">
                                    <?= number_format($type['montant_total'], 0, ',', ' ') ?> Ar
                                </td>
                                <td class="text-end text-success">
                                    <?= number_format($type['montant_satisfait'], 0, ',', ' ') ?> Ar
                                </td>
                                <td class="text-end text-danger">
                                    <?= number_format($type['montant_restant'], 0, ',', ' ') ?> Ar
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Récap dons -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0"><i class="fas fa-gift"></i> Récapitulatif des Dons</h5>
    </div>
    <div class="card-body">
        <div class="row text-center" id="recap-dons">
            <div class="col-md-3">
                <h6 class="text-muted">Dons reçus</h6>
                <h4 id="dons-recus"><?= number_format($recap_dons['total_dons_recus'], 0, ',', ' ') ?> Ar</h4>
            </div>
            <div class="col-md-3">
                <h6 class="text-muted">Distribués</h6>
                <h4 class="text-success" id="dons-distribues"><?= number_format($recap_dons['total_distribue'], 0, ',', ' ') ?> Ar</h4>
            </div>
            <div class="col-md-3">
                <h6 class="text-muted">Achats effectués</h6>
                <h4 class="text-warning" id="dons-achats"><?= number_format($recap_dons['total_achats'], 0, ',', ' ') ?> Ar</h4>
            </div>
            <div class="col-md-3">
                <h6 class="text-muted">Disponible</h6>
                <h4 class="text-primary" id="dons-disponible"><?= number_format($recap_dons['reste_disponible'], 0, ',', ' ') ?> Ar</h4>
            </div>
        </div>
    </div>
</div>

<style>
    #btn-actualiser.loading {
        pointer-events: none;
    }
    #btn-actualiser.loading i {
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
</style>

<script>
document.getElementById('btn-actualiser').addEventListener('click', function() {
    const btn = this;
    btn.classList.add('loading');
    btn.innerHTML = '<i class="fas fa-sync-alt"></i> Chargement...';
    
    fetch('<?= BASE_URL ?>/recap/api/dashboard')
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                const data = result.data;
                const recap = data.recap_general;
                const dons = data.recap_dons;
                
                // Mise à jour des cartes principales
                document.getElementById('montant-total').textContent = 
                    Number(recap.montant_total_besoins).toLocaleString('fr-FR') + ' Ar';
                document.getElementById('montant-satisfait').textContent = 
                    Number(recap.montant_besoins_satisfaits).toLocaleString('fr-FR') + ' Ar';
                document.getElementById('montant-restant').textContent = 
                    Number(recap.montant_besoins_restants).toLocaleString('fr-FR') + ' Ar';
                
                document.getElementById('pourcentage-satisfait').textContent = 
                    recap.pourcentage_satisfait + '% du total';
                document.getElementById('pourcentage-restant').textContent = 
                    (100 - recap.pourcentage_satisfait) + '% du total';
                
                // Barre de progression
                document.getElementById('progress-bar').style.width = recap.pourcentage_satisfait + '%';
                document.getElementById('progress-text').textContent = recap.pourcentage_satisfait + '%';
                
                // Dons
                document.getElementById('dons-recus').textContent = 
                    Number(dons.total_dons_recus).toLocaleString('fr-FR') + ' Ar';
                document.getElementById('dons-distribues').textContent = 
                    Number(dons.total_distribue).toLocaleString('fr-FR') + ' Ar';
                document.getElementById('dons-achats').textContent = 
                    Number(dons.total_achats).toLocaleString('fr-FR') + ' Ar';
                document.getElementById('dons-disponible').textContent = 
                    Number(dons.reste_disponible).toLocaleString('fr-FR') + ' Ar';
                
                // Animation de succès
                btn.classList.remove('btn-primary');
                btn.classList.add('btn-success');
                btn.innerHTML = '<i class="fas fa-check"></i> Actualisé !';
                
                setTimeout(() => {
                    btn.classList.remove('btn-success');
                    btn.classList.add('btn-primary');
                    btn.innerHTML = '<i class="fas fa-sync-alt"></i> Actualiser';
                    btn.classList.remove('loading');
                }, 1500);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            btn.classList.remove('loading');
            btn.innerHTML = '<i class="fas fa-sync-alt"></i> Actualiser';
            alert('Erreur lors de l\'actualisation.');
        });
});
</script>

<?php
$content = ob_get_clean();
include(__DIR__ . '/../layout/layout.php');
?>
