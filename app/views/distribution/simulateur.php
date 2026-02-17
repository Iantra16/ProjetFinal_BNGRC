<?php 
$title = "Simulateur de Distribution - BNGRC";
ob_start();
?>

<div class="page-header mb-4">
    <h1 class="page-title">
        <i class="fas fa-magic"></i> Simulateur de Distribution
    </h1>
    <p class="page-subtitle">
        Visualisez comment les dons actuels pourraient être répartis entre les villes selon leurs besoins.
    </p>
</div>

<!-- Formulaire de sélection du type de distribution -->
<div class="card mb-4 shadow-sm">
    <div class="card-header bg-info text-white">
        <h6 class="mb-0"><i class="fas fa-layer-group"></i> Type de Distribution</h6>
    </div>
    <div class="card-body">
        <div class="d-flex gap-4">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="type-distribution" id="type-niv1" value="Niv1">
                <label class="form-check-label" for="type-niv1">
                    <strong>Mode 1 : Par ordre de date et de saisie</strong>
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="type-distribution" id="type-niv2" value="Niv2">
                <label class="form-check-label" for="type-niv2">
                    <strong>Mode 2 : Par ordre croissant</strong>
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="type-distribution" id="type-niv3" value="Niv3">
                <label class="form-check-label" for="type-niv3">
                    <strong>Mode 3 : Par proporitonnalite</strong>
                </label>
            </div>
        </div>
    </div>
</div>

<div class="row mb-3">
    <div class="col-12 text-end">
        <button id="btn-simuler" class="btn btn-lg btn-success shadow me-2">
            <i class="fas fa-play"></i> Simuler
        </button>
        <button id="btn-reinitialiser" class="btn btn-lg btn-warning shadow me-2">
            <i class="fas fa-sync-alt"></i> Réinitialiser
        </button>
        <button id="btn-valider" class="btn btn-lg btn-primary shadow d-none" disabled>
            <i class="fas fa-check-circle"></i> Valider la Distribution
        </button>
    </div>
</div>

<div class="row">
    <!-- Stock Disponible -->
    <div class="col-md-5 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-warehouse"></i> Stock Disponible</h5>
                <span class="badge bg-primary"><?= count($restdons) ?> articles</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Article</th>
                                <th class="text-end">Reste</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($restdons as $don): ?>
                                <tr>
                                    <td><?= htmlspecialchars($don['article']) ?></td>
                                    <td class="text-end">
                                        <strong><?= number_format($don['stock_restant'], 0, ',', ' ') ?></strong>
                                        <small class="text-muted"><?= htmlspecialchars($don['unite']) ?></small>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Résultats de la Simulation -->
    <div class="col-md-7 mb-4">
        <div id="simulation-loading" class="text-center py-5 d-none">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Calcul en cours...</span>
            </div>
            <p class="mt-2">Calcul de la distribution optimale...</p>
        </div>

        <div id="simulation-placeholder" class="card border-dashed h-100 text-center py-5 d-flex align-items-center justify-content-center">
            <div class="text-muted">
                <i class="fas fa-chart-line fa-4x mb-3 opacity-25"></i>
                <h5>Aucune simulation en cours</h5>
                <p>Cliquez sur le bouton "Lancer la Simulation" pour voir les résultats.</p>
            </div>
        </div>

        <div id="simulation-container" class="card shadow-sm h-100 d-none">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-clipboard-check"></i> Plan de Distribution Stimulé</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="table-simulation" class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Ville</th>
                                <th>Article</th>
                                <th class="text-end">Quantité</th>
                            </tr>
                        </thead>
                        <tbody id="result-body">
                            <!-- Rempli par AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .border-dashed {
        border: 2px dashed #dee2e6;
        background-color: #f8f9fa;
        min-height: 400px;
    }
</style>

<script>
let simulationData = [];

function resetSimulationUI() {
    const btnSimuler = document.getElementById('btn-simuler');
    const btnValider = document.getElementById('btn-valider');
    const loading = document.getElementById('simulation-loading');
    const placeholder = document.getElementById('simulation-placeholder');
    const container = document.getElementById('simulation-container');
    const resultBody = document.getElementById('result-body');

    simulationData = [];
    resultBody.innerHTML = '';
    loading.classList.add('d-none');
    container.classList.add('d-none');
    placeholder.classList.remove('d-none');
    btnValider.classList.add('d-none');
    btnValider.disabled = true;
    btnSimuler.disabled = false;
    btnSimuler.innerHTML = '<i class="fas fa-play"></i> Simuler';
}

// Bouton REINITIALISER
document.getElementById('btn-reinitialiser').addEventListener('click', function() {
    resetSimulationUI();
});

// Bouton SIMULER
document.getElementById('btn-simuler').addEventListener('click', function() {
    // Vérifier si un niveau a été sélectionné
    const selectedLevel = document.querySelector('input[name="type-distribution"]:checked');
    if (!selectedLevel) {
        alert('Veuillez choisir un niveau de distribution avant de simuler.');
        return;
    }

    const btn = this;
    const btnValider = document.getElementById('btn-valider');
    const loading = document.getElementById('simulation-loading');
    const placeholder = document.getElementById('simulation-placeholder');
    const container = document.getElementById('simulation-container');
    const resultBody = document.getElementById('result-body');

    // UI Feedback
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Simulation...';
    btnValider.classList.add('d-none');
    
    placeholder.classList.add('d-none');
    container.classList.add('d-none');
    loading.classList.remove('d-none');

    fetch('<?= BASE_URL ?>/distributions/simuler', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'type=' + selectedLevel.value
    })
    .then(response => response.json())
    .then(data => {
        simulationData = data;
        resultBody.innerHTML = '';
        
        if (data.length === 0) {
            resultBody.innerHTML = '<tr><td colspan="3" class="text-center py-3 text-muted">Aucune distribution n\'est nécessaire ou possible avec le stock actuel.</td></tr>';
            btnValider.classList.add('d-none');
        } else {
            data.forEach(item => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td><strong>${item.ville_nom}</strong></td>
                    <td>${item.article_nom}</td>
                    <td class="text-end">
                        <span class="badge bg-success">${Number(item.quantite_attribuee).toLocaleString()}</span>
                        <small class="text-muted">${item.unite}</small>
                    </td>
                `;
                resultBody.appendChild(tr);
            });
            
            // Afficher le bouton Valider
            btnValider.classList.remove('d-none');
            btnValider.disabled = false;
        }

        loading.classList.add('d-none');
        container.classList.remove('d-none');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-play"></i> Simuler';
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Une erreur est survenue lors de la simulation.');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-play"></i> Simuler';
        loading.classList.add('d-none');
        placeholder.classList.remove('d-none');
    });
});

// Bouton VALIDER
document.getElementById('btn-valider').addEventListener('click', function() {
    if (simulationData.length === 0) {
        alert('Veuillez d\'abord lancer une simulation.');
        return;
    }
    
    if (!confirm('Êtes-vous sûr de vouloir valider cette distribution ?\n\nCette action est irréversible.')) {
        return;
    }
    
    const btn = this;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Validation...';
    
    fetch('<?= BASE_URL ?>/distributions/valider', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            type: document.querySelector('input[name="type-distribution"]:checked').value
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            // Recharger la page pour voir les changements
            window.location.reload();
        } else {
            alert('Erreur: ' + data.message);
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check-circle"></i> Valider la Distribution';
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Une erreur est survenue lors de la validation.');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check-circle"></i> Valider la Distribution';
    });
});
</script>

<?php
$content = ob_get_clean();
include(__DIR__ . '/../layout/layout.php');
?>
