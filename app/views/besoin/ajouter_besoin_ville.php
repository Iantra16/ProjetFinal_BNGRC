<?php
$title = "Ajouter un Besoin - " . htmlspecialchars($ville['nom']) . " - BNGRC";
ob_start();
?>

<div class="row">
                <!-- Formulaire -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-plus"></i> 
                                Nouveau Besoin pour <?= htmlspecialchars($ville['nom']) ?>
                            </h3>
                        </div>
                        <div class="card-body">
                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <?= htmlspecialchars($error) ?>
                                </div>
                            <?php endif; ?>

                            <form method="POST" id="formBesoin">
                                <div class="row">
                                    <!-- Ville (lecture seule) -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label><i class="fas fa-city"></i> Ville</label>
                                            <input type="text" 
                                                   class="form-control-plaintext" 
                                                   value="<?= htmlspecialchars($ville['nom']) ?> (<?= htmlspecialchars($ville['region']) ?>)" 
                                                   readonly>
                                        </div>
                                    </div>
                                    
                                    <!-- Date (lecture seule) -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label><i class="fas fa-calendar"></i> Date</label>
                                            <input type="text" 
                                                   class="form-control-plaintext" 
                                                   value="<?= date('d/m/Y H:i') ?>" 
                                                   readonly>
                                        </div>
                                    </div>
                                </div>

                                <!-- Sélection de catégorie -->
                                <div class="form-group">
                                    <label for="categorie">
                                        <i class="fas fa-tags"></i> Catégorie *
                                    </label>
                                    <select class="form-control" id="categorie" name="categorie" required>
                                        <option value="">-- Sélectionner une catégorie --</option>
                                        <?php foreach ($categories as $categorie): ?>
                                            <option value="<?= $categorie['id'] ?>" 
                                                    data-items='<?= json_encode($categorie['items']) ?>'>
                                                <?= htmlspecialchars($categorie['nom']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Sélection d'article -->
                                <div class="form-group">
                                    <label for="article">
                                        <i class="fas fa-box"></i> Article *
                                    </label>
                                    <select class="form-control" id="article" name="article" required disabled>
                                        <option value="">-- Sélectionner d'abord une catégorie --</option>
                                    </select>
                                    <small id="article-info" class="form-text text-muted"></small>
                                </div>

                                <!-- Quantité -->
                                <div class="form-group">
                                    <label for="quantite">
                                        <i class="fas fa-calculator"></i> Quantité *
                                    </label>
                                    <div class="input-group">
                                        <input type="number" 
                                               class="form-control" 
                                               id="quantite" 
                                               name="quantite" 
                                               placeholder="Ex: 100"
                                               step="0.01"
                                               min="0.01"
                                               required>
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="unite-display">unité</span>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted">
                                        Saisissez la quantité nécessaire
                                    </small>
                                </div>

                                <!-- Estimation de valeur -->
                                <div class="form-group">
                                    <label><i class="fas fa-money-bill-wave"></i> Estimation de Valeur</label>
                                    <div class="input-group">
                                        <input type="text" 
                                               class="form-control" 
                                               id="valeur-estimee" 
                                               readonly 
                                               placeholder="Calculé automatiquement">
                                        <div class="input-group-append">
                                            <span class="input-group-text">Ar</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Boutons -->
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <button type="submit" class="btn btn-primary btn-block">
                                                <i class="fas fa-save"></i> Enregistrer le Besoin
                                            </button>
                                        </div>
                                        <div class="col-md-3">
                                            <a href="/villes/<?= $ville['id'] ?>/besoins" class="btn btn-secondary btn-block">
                                                <i class="fas fa-arrow-left"></i> Retour
                                            </a>
                                        </div>
                                        <div class="col-md-3">
                                            <button type="reset" class="btn btn-outline-warning btn-block">
                                                <i class="fas fa-undo"></i> Réinitialiser
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Aide et informations -->
                <div class="col-md-4">
                    <!-- Informations sur la ville -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-info-circle"></i> Informations
                            </h3>
                        </div>
                        <div class="card-body">
                            <h5><?= htmlspecialchars($ville['nom']) ?></h5>
                            <p class="text-muted">Région: <?= htmlspecialchars($ville['region']) ?></p>
                            
                            <hr>
                            
                            <h6>Instructions</h6>
                            <ol>
                                <li>Sélectionnez une catégorie</li>
                                <li>Choisissez l'article souhaité</li>
                                <li>Indiquez la quantité nécessaire</li>
                                <li>Vérifiez l'estimation de valeur</li>
                                <li>Enregistrez le besoin</li>
                            </ol>
                        </div>
                    </div>

                    <!-- Catégories disponibles -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-tags"></i> Catégories Disponibles
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush">
                                <?php foreach ($categories as $categorie): ?>
                                    <div class="list-group-item">
                                        <strong><?= htmlspecialchars($categorie['nom']) ?></strong>
                                        <small class="text-muted d-block">
                                            <?= count($categorie['items']) ?> article<?= count($categorie['items']) > 1 ? 's' : '' ?> disponible<?= count($categorie['items']) > 1 ? 's' : '' ?>
                                        </small>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const categorieSelect = document.getElementById('categorie');
    const articleSelect = document.getElementById('article');
    const quantiteInput = document.getElementById('quantite');
    const uniteDisplay = document.getElementById('unite-display');
    const valeurEstimee = document.getElementById('valeur-estimee');
    const articleInfo = document.getElementById('article-info');
    
    let articleData = null;
    
    // Gestion du changement de catégorie
    categorieSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if (this.value) {
            const items = JSON.parse(selectedOption.getAttribute('data-items') || '[]');
            
            // Vider et réactiver la sélection d'articles
            articleSelect.innerHTML = '<option value="">-- Sélectionner un article --</option>';
            
            items.forEach(function(item) {
                const option = document.createElement('option');
                option.value = item.nom;
                option.textContent = item.nom;
                option.dataset.unite = item.unite;
                option.dataset.prix = item.prix_unitaire;
                articleSelect.appendChild(option);
            });
            
            articleSelect.disabled = false;
        } else {
            articleSelect.innerHTML = '<option value="">-- Sélectionner d\'abord une catégorie --</option>';
            articleSelect.disabled = true;
            resetFields();
        }
    });
    
    // Gestion du changement d'article
    articleSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if (this.value) {
            articleData = {
                nom: this.value,
                unite: selectedOption.dataset.unite,
                prix: parseFloat(selectedOption.dataset.prix)
            };
            
            uniteDisplay.textContent = articleData.unite;
            articleInfo.textContent = `Prix unitaire: ${new Intl.NumberFormat('fr-FR').format(articleData.prix)} Ar/${articleData.unite}`;
            
            calculerValeur();
        } else {
            articleData = null;
            resetFields();
        }
    });
    
    // Gestion du changement de quantité
    quantiteInput.addEventListener('input', calculerValeur);
    
    function calculerValeur() {
        if (articleData && quantiteInput.value && parseFloat(quantiteInput.value) > 0) {
            const quantite = parseFloat(quantiteInput.value);
            const valeurTotale = quantite * articleData.prix;
            valeurEstimee.value = new Intl.NumberFormat('fr-FR').format(Math.round(valeurTotale));
        } else {
            valeurEstimee.value = '';
        }
    }
    
    function resetFields() {
        uniteDisplay.textContent = 'unité';
        articleInfo.textContent = '';
        valeurEstimee.value = '';
        articleData = null;
    }
    
    // Gestion de la réinitialisation du formulaire
    document.querySelector('button[type="reset"]').addEventListener('click', function(e) {
        setTimeout(function() {
            articleSelect.innerHTML = '<option value="">-- Sélectionner d\'abord une catégorie --</option>';
            articleSelect.disabled = true;
            resetFields();
        }, 10);
    });
});
</script>

<?php
$content = ob_get_clean();
include(__DIR__ . '/../layout/layout.php');
?>