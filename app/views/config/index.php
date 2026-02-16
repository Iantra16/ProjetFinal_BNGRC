<?php 
$title = "Configuration - BNGRC";
ob_start();
?>

<div class="page-header mb-4">
    <h1 class="page-title">
        <i class="fas fa-cogs"></i> Configuration
    </h1>
    <p class="page-subtitle">
        Paramétrage des options de l'application.
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

<div class="row">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-percentage"></i> Frais d'achat
                </h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-4">
                    Définissez le pourcentage de frais appliqué lors des achats effectués avec les dons en argent.
                    <br>
                    <small>Exemple : avec 10% de frais, un achat de 100 000 Ar coûtera 110 000 Ar.</small>
                </p>
                
                <form action="<?= BASE_URL ?>/config/frais" method="POST">
                    <div class="mb-4">
                        <label for="frais_pourcent" class="form-label fw-bold">
                            Pourcentage de frais (%)
                        </label>
                        <div class="input-group input-group-lg">
                            <input type="number" 
                                   class="form-control" 
                                   id="frais_pourcent" 
                                   name="frais_pourcent" 
                                   value="<?= htmlspecialchars($frais_pourcent) ?>"
                                   min="0" 
                                   max="100" 
                                   step="0.1"
                                   required>
                            <span class="input-group-text">%</span>
                        </div>
                        <div class="form-text">
                            Valeur actuelle : <strong><?= number_format($frais_pourcent, 1) ?>%</strong>
                        </div>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save"></i> Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="fas fa-calculator"></i> Simulateur de calcul
                </h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">
                    Testez le calcul des frais sur un montant.
                </p>
                
                <div class="mb-3">
                    <label for="montant_test" class="form-label">Montant de l'achat (Ar)</label>
                    <input type="number" 
                           class="form-control" 
                           id="montant_test" 
                           placeholder="Ex: 100000"
                           min="0">
                </div>
                
                <div id="resultat-calcul" class="alert alert-light d-none">
                    <div class="row text-center">
                        <div class="col-4">
                            <small class="text-muted">Montant HT</small>
                            <div class="fw-bold" id="calc-ht">-</div>
                        </div>
                        <div class="col-4">
                            <small class="text-muted">Frais (<span id="calc-pourcent"><?= $frais_pourcent ?></span>%)</small>
                            <div class="fw-bold text-warning" id="calc-frais">-</div>
                        </div>
                        <div class="col-4">
                            <small class="text-muted">Total TTC</small>
                            <div class="fw-bold text-success fs-5" id="calc-total">-</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card shadow-sm mt-3">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle"></i> Aide
                </h5>
            </div>
            <div class="card-body">
                <ul class="mb-0">
                    <li>Les frais sont appliqués automatiquement lors de chaque achat.</li>
                    <li>La modification prend effet immédiatement.</li>
                    <li>Les achats existants ne sont pas affectés.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('montant_test').addEventListener('input', function() {
    const montant = parseFloat(this.value) || 0;
    const frais = <?= $frais_pourcent ?>;
    const resultat = document.getElementById('resultat-calcul');
    
    if (montant > 0) {
        const montantFrais = montant * (frais / 100);
        const total = montant + montantFrais;
        
        document.getElementById('calc-ht').textContent = montant.toLocaleString('fr-FR') + ' Ar';
        document.getElementById('calc-frais').textContent = montantFrais.toLocaleString('fr-FR') + ' Ar';
        document.getElementById('calc-total').textContent = total.toLocaleString('fr-FR') + ' Ar';
        
        resultat.classList.remove('d-none');
    } else {
        resultat.classList.add('d-none');
    }
});
</script>

<?php
$content = ob_get_clean();
include(__DIR__ . '/../layout/layout.php');
?>
