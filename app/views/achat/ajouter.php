<?php 
$title = "Nouvel Achat - BNGRC";
ob_start();
?>

<div class="page-header mb-4">
    <h1 class="page-title">
        <i class="fas fa-shopping-cart"></i> Nouvel Achat
    </h1>
    <p class="page-subtitle">
        Utilisez les dons en argent pour acheter des articles (nature ou matériaux).
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
    <!-- Formulaire d'achat -->
    <div class="col-md-7">
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="fas fa-plus-circle"></i> Effectuer un achat
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($dons_argent)): ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        Aucun don en argent disponible pour effectuer des achats.
                    </div>
                <?php else: ?>
                    <form action="<?= BASE_URL ?>/achats/ajouter" method="POST" id="form-achat">
                        
                        <!-- Sélection du don en argent -->
                        <div class="mb-3">
                            <label for="id_don_article" class="form-label fw-bold">
                                <i class="fas fa-money-bill-wave"></i> Don en argent à utiliser
                            </label>
                            <select name="id_don_article" id="id_don_article" class="form-select" required>
                                <option value="">-- Sélectionnez un don --</option>
                                <?php foreach ($dons_argent as $don): ?>
                                    <option value="<?= $don['id_don_article'] ?>" 
                                            data-solde="<?= $don['solde_disponible'] ?>">
                                        <?= htmlspecialchars($don['donateur'] ?? 'Anonyme') ?> 
                                        - Solde: <?= number_format($don['solde_disponible'], 0, ',', ' ') ?> Ar
                                        (<?= date('d/m/Y', strtotime($don['date_don'])) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">
                                Solde disponible : <strong id="solde-display">-</strong>
                            </div>
                        </div>
                        
                        <!-- Sélection de l'article -->
                        <div class="mb-3">
                            <label for="id_article" class="form-label fw-bold">
                                <i class="fas fa-box"></i> Article à acheter
                            </label>
                            <select name="id_article" id="id_article" class="form-select" required>
                                <option value="">-- Sélectionnez un article --</option>
                                <?php foreach ($articles as $article): ?>
                                    <option value="<?= $article['id'] ?>" 
                                            data-prix="<?= $article['prix_unitaire'] ?>"
                                            data-unite="<?= htmlspecialchars($article['unite']) ?>">
                                        <?= htmlspecialchars($article['nom']) ?> 
                                        - <?= number_format($article['prix_unitaire'], 0, ',', ' ') ?> Ar/<?= $article['unite'] ?>
                                        (<?= $article['type_besoin'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div id="article-warning" class="alert alert-danger mt-2 d-none">
                                <i class="fas fa-exclamation-circle"></i>
                                <span id="article-warning-text"></span>
                            </div>
                        </div>
                        
                        <!-- Quantité -->
                        <div class="mb-3">
                            <label for="quantite" class="form-label fw-bold">
                                <i class="fas fa-sort-numeric-up"></i> Quantité
                            </label>
                            <div class="input-group">
                                <input type="number" 
                                       class="form-control" 
                                       id="quantite" 
                                       name="quantite" 
                                       min="1" 
                                       step="1"
                                       required>
                                <span class="input-group-text" id="unite-display">unité</span>
                            </div>
                        </div>
                        
                        <!-- Récapitulatif -->
                        <div id="recap-achat" class="alert alert-info d-none">
                            <h6 class="alert-heading"><i class="fas fa-calculator"></i> Récapitulatif</h6>
                            <hr>
                            <div class="row">
                                <div class="col-6">
                                    <small class="text-muted">Montant HT</small>
                                    <div id="recap-ht" class="fw-bold">-</div>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Frais (<?= $frais_pourcent ?>%)</small>
                                    <div id="recap-frais" class="fw-bold text-warning">-</div>
                                </div>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between align-items-center">
                                <span>Total TTC</span>
                                <span id="recap-total" class="fs-4 fw-bold text-success">-</span>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg" id="btn-submit">
                                <i class="fas fa-check"></i> Valider l'achat
                            </button>
                            <a href="<?= BASE_URL ?>/achats" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Retour à la liste
                            </a>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Besoins restants -->
    <div class="col-md-5">
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                    <i class="fas fa-clipboard-list"></i> Besoins restants
                </h5>
            </div>
            <div class="card-body p-0" style="max-height: 500px; overflow-y: auto;">
                <?php if (empty($besoins_restants)): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-check-circle fa-2x mb-2"></i>
                        <p>Tous les besoins sont satisfaits !</p>
                    </div>
                <?php else: ?>
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th>Ville</th>
                                <th>Article</th>
                                <th class="text-end">Reste</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($besoins_restants as $besoin): ?>
                                <tr>
                                    <td><small><?= htmlspecialchars($besoin['ville']) ?></small></td>
                                    <td><small><?= htmlspecialchars($besoin['article']) ?></small></td>
                                    <td class="text-end">
                                        <span class="badge bg-danger">
                                            <?= number_format($besoin['reste_a_combler'], 0, ',', ' ') ?>
                                        </span>
                                        <small><?= $besoin['unite'] ?></small>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="card shadow-sm mt-3">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle"></i> Règles d'achat
                </h5>
            </div>
            <div class="card-body">
                <ul class="mb-0 small">
                    <li>Frais actuels : <strong><?= $frais_pourcent ?>%</strong></li>
                    <li>Vous ne pouvez pas acheter un article qui existe encore dans les dons.</li>
                    <li>Le solde du don doit être suffisant.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
const fraisPourcent = <?= $frais_pourcent ?>;

// Mise à jour du solde affiché
document.getElementById('id_don_article').addEventListener('change', function() {
    const option = this.options[this.selectedIndex];
    const solde = option.dataset.solde || 0;
    document.getElementById('solde-display').textContent = 
        parseFloat(solde).toLocaleString('fr-FR') + ' Ar';
    calculerTotal();
});

// Vérification article + mise à jour unité
document.getElementById('id_article').addEventListener('change', function() {
    const option = this.options[this.selectedIndex];
    const articleId = this.value;
    const unite = option.dataset.unite || 'unité';
    
    document.getElementById('unite-display').textContent = unite;
    
    // Vérifier si l'article existe dans les dons
    if (articleId) {
        fetch(`<?= BASE_URL ?>/achats/api/check-article?id_article=${articleId}`)
            .then(r => r.json())
            .then(data => {
                const warning = document.getElementById('article-warning');
                const warningText = document.getElementById('article-warning-text');
                const btnSubmit = document.getElementById('btn-submit');
                
                if (data.existe_dans_dons) {
                    warningText.textContent = data.message;
                    warning.classList.remove('d-none');
                    btnSubmit.disabled = true;
                } else {
                    warning.classList.add('d-none');
                    btnSubmit.disabled = false;
                }
            });
    }
    
    calculerTotal();
});

// Calcul du total
document.getElementById('quantite').addEventListener('input', calculerTotal);

function calculerTotal() {
    const articleSelect = document.getElementById('id_article');
    const option = articleSelect.options[articleSelect.selectedIndex];
    const prix = parseFloat(option?.dataset?.prix) || 0;
    const quantite = parseFloat(document.getElementById('quantite').value) || 0;
    
    const recap = document.getElementById('recap-achat');
    
    if (prix > 0 && quantite > 0) {
        const montantHT = prix * quantite;
        const montantFrais = montantHT * (fraisPourcent / 100);
        const total = montantHT + montantFrais;
        
        document.getElementById('recap-ht').textContent = montantHT.toLocaleString('fr-FR') + ' Ar';
        document.getElementById('recap-frais').textContent = montantFrais.toLocaleString('fr-FR') + ' Ar';
        document.getElementById('recap-total').textContent = total.toLocaleString('fr-FR') + ' Ar';
        
        recap.classList.remove('d-none');
        
        // Vérifier le solde
        const donSelect = document.getElementById('id_don_article');
        const donOption = donSelect.options[donSelect.selectedIndex];
        const solde = parseFloat(donOption?.dataset?.solde) || 0;
        
        if (solde < total && solde > 0) {
            recap.classList.remove('alert-info');
            recap.classList.add('alert-danger');
            document.getElementById('btn-submit').disabled = true;
        } else {
            recap.classList.remove('alert-danger');
            recap.classList.add('alert-info');
        }
    } else {
        recap.classList.add('d-none');
    }
}
</script>

<?php
$content = ob_get_clean();
include(__DIR__ . '/../layout/layout.php');
?>
