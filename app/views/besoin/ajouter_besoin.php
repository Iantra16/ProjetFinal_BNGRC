<?php
$title = "Ajouter un Besoin - BNGRC";
ob_start();
?>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h3>📋 Saisir un Nouveau Besoin</h3>
            </div>
            <div class="card-body">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i>
                        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i>
                        <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                <form method="POST" action="/besoins" id="formBesoin">
                    <!-- 1. SÉLECTION VILLE -->
                    <div class="mb-3">
                        <label>🏙️ Ville *</label>
                        <select name="id_ville" id="id_ville" class="form-select" required>
                            <option value="">-- Sélectionner --</option>
                            <?php foreach ($villes as $v): ?>
                                <option value="<?= $v['id'] ?>">
                                    <?= $v['nom'] ?> (<?= $v['region_nom'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- 2. SÉLECTION TYPE DE BESOIN -->
                    <div class="mb-4">
                        <label>🏷️ Type de Besoin *</label>
                        <select id="type_besoin_principal" name="type_besoin_principal" class="form-select" required>
                            <option value="">-- Sélectionner --</option>
                            <?php foreach ($types_besoin as $type): ?>
                                <option value="<?= $type['id'] ?>" data-libelle="<?= htmlspecialchars($type['libelle']) ?>">
                                    <?= $type['libelle'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- SECTION NATURE ET MATÉRIAUX -->
                    <div id="section-nature-materiaux" class="d-none">
                        <!-- 3. SÉLECTION ARTICLE -->
                        <div class="mb-3">
                            <label>📦 Article *</label>
                            <select name="id_article_existant" id="article_existant" class="form-select">
                                <option value="">-- Sélectionner un article --</option>
                                <?php foreach ($articles as $art): ?>
                                    <option value="<?= $art['id'] ?>"
                                            data-type="<?= $art['id_type_besoin'] ?>"
                                            data-prix="<?= $art['prix_unitaire'] ?>"
                                            data-unite="<?= $art['unite'] ?>">
                                        <?= $art['nom'] ?> 
                                        (<?= number_format($art['prix_unitaire']) ?> Ar/<?= $art['unite'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- 4. QUANTITÉ -->
                        <div class="mb-3">
                            <label>🧮 Quantité Nécessaire *</label>
                            <div class="input-group">
                                <input type="number" name="quantite" id="quantite" class="form-control" step="0.01" min="0.01">
                                <span class="input-group-text" id="unite-display">unité</span>
                            </div>
                        </div>

                        <!-- 5. VALEUR ESTIMÉE -->
                        <div class="mb-4">
                            <label>💵 Valeur Estimée</label>
                            <div class="input-group">
                                <input type="text" id="valeur-estimee" name="valeur_estimee" class="form-control bg-light" readonly>
                                <span class="input-group-text bg-success text-white">Ar</span>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION ARGENT -->
                    <div id="section-argent" class="d-none">
                        <div class="mb-4">
                            <label>💰 Somme d'Argent Nécessaire (Ar) *</label>
                            <input type="number" name="somme_argent" id="somme_argent" class="form-control" 
                                   placeholder="Montant en Ariary" step="0.01" min="0">
                            <div class="form-text">Veuillez entrer le montant en Ariary</div>
                        </div>
                    </div>

                    <!-- BOUTONS -->
                    <div class="row">
                        <div class="col-md-6">
                            <button type="submit" id="btn-submit" class="btn btn-primary w-100" disabled>
                                💾 Enregistrer
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">ℹ️ Guide</div>
            <div class="card-body">
                <ol>
                    <li>Sélectionner la ville</li>
                    <li>Choisir le type de besoin (Nature, Matériaux ou Argent)</li>
                    <li>Pour Nature/Matériaux : choisir le mode et indiquer la quantité</li>
                    <li>Pour Argent : entrer la somme</li>
                    <li>Enregistrer</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<script>
// JAVASCRIPT POUR LA GESTION DU FORMULAIRE
document.addEventListener('DOMContentLoaded', function() {
    // Éléments du DOM
    const typeBesoinPrincipal = document.getElementById('type_besoin_principal');
    const sectionNatureMateriaux = document.getElementById('section-nature-materiaux');
    const sectionArgent = document.getElementById('section-argent');
    const articleExistant = document.getElementById('article_existant');
    const quantite = document.getElementById('quantite');
    const uniteDisplay = document.getElementById('unite-display');
    const valeurEstimee = document.getElementById('valeur-estimee');
    const sommeArgent = document.getElementById('somme_argent');
    const btnSubmit = document.getElementById('btn-submit');
    const allArticleOptions = Array.from(articleExistant.options);
    
    let prixUnitaire = 0;
    let typeBesoinSelectedId = '';
    
    // Gestion du changement de type de besoin
    typeBesoinPrincipal.addEventListener('change', function() {
        typeBesoinSelectedId = this.value;
        const libelle = this.options[this.selectedIndex].dataset.libelle || '';
        
        if (!typeBesoinSelectedId) {
            sectionNatureMateriaux.classList.add('d-none');
            sectionArgent.classList.add('d-none');
            btnSubmit.disabled = true;
            return;
        }
        
        // Afficher la section appropriée
        if (libelle === 'Argent') {
            sectionNatureMateriaux.classList.add('d-none');
            sectionArgent.classList.remove('d-none');
            quantite.removeAttribute('required');
            articleExistant.removeAttribute('required');
            sommeArgent.setAttribute('required', 'required');
            btnSubmit.disabled = false;
        } else if (libelle === 'Nature' || libelle === 'Materiaux') {
            sectionNatureMateriaux.classList.remove('d-none');
            sectionArgent.classList.add('d-none');
            sommeArgent.removeAttribute('required');
            quantite.setAttribute('required', 'required');
            articleExistant.setAttribute('required', 'required');
            // Filtrer les articles par type sélectionné
            filterArticlesByType();
            btnSubmit.disabled = false;
        } else {
            sectionNatureMateriaux.classList.add('d-none');
            sectionArgent.classList.add('d-none');
            btnSubmit.disabled = true;
        }
    });
    
    // Filtrer les articles par type sélectionné
    function filterArticlesByType() {
        articleExistant.innerHTML = '';
        const firstOption = allArticleOptions.find(opt => opt.value === '');
        if (firstOption) {
            articleExistant.appendChild(firstOption.cloneNode(true));
        }
        
        allArticleOptions.forEach(option => {
            if (option.value && option.dataset.type === typeBesoinSelectedId) {
                articleExistant.appendChild(option.cloneNode(true));
            }
        });
        
        articleExistant.value = '';
        prixUnitaire = 0;
        uniteDisplay.textContent = 'unité';
        valeurEstimee.value = '';
    }
    
    // Sélection article existant
    articleExistant.addEventListener('change', function() {
        const option = this.options[this.selectedIndex];
        if (this.value) {
            prixUnitaire = parseFloat(option.dataset.prix);
            uniteDisplay.textContent = option.dataset.unite;
            calculer();
        } else {
            prixUnitaire = 0;
            uniteDisplay.textContent = 'unité';
            valeurEstimee.value = '';
        }
    });
    
    // Calcul quantité
    quantite.addEventListener('input', calculer);
    
    function calculer() {
        const qty = parseFloat(quantite.value) || 0;
        if (qty > 0 && prixUnitaire > 0) {
            const valeur = qty * prixUnitaire;
            valeurEstimee.value = new Intl.NumberFormat('fr-FR').format(Math.round(valeur));
        } else {
            valeurEstimee.value = '';
        }
    }
});
</script>

<?php
$content = ob_get_clean();
include(__DIR__ . '/../layout/layout.php');
?>