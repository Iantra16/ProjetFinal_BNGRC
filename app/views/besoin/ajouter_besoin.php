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
                <form method="POST" id="formBesoin">
                    
                    <!-- 1. SÉLECTION VILLE -->
                    <div class="mb-3">
                        <label>🏙️ Ville *</label>
                        <select name="id_ville" class="form-select" required>
                            <option value="">-- Sélectionner --</option>
                            <?php foreach ($villes as $v): ?>
                                <option value="<?= $v['id'] ?>">
                                    <?= $v['nom'] ?> (<?= $v['region_nom'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- 2. CHOIX MODE -->
                    <div class="mb-4">
                        <label>⚙️ Mode de Saisie *</label>
                        <div class="btn-group w-100">
                            <input type="radio" name="mode_article" id="mode_existant" value="existant" checked>
                            <label for="mode_existant" class="btn btn-outline-primary">
                                📦 Article Existant
                            </label>
                            
                            <input type="radio" name="mode_article" id="mode_nouveau" value="nouveau">
                            <label for="mode_nouveau" class="btn btn-outline-success">
                                ➕ Nouvel Article
                            </label>
                        </div>
                    </div>

                    <!-- 3a. SECTION ARTICLE EXISTANT -->
                    <div id="section-existant">
                        <div class="card border-primary mb-3">
                            <div class="card-body">
                                <!-- Filtre par type -->
                                <div class="mb-3">
                                    <label>🏷️ Type de Besoin (filtre)</label>
                                    <select id="type_besoin_filter" class="form-select">
                                        <option value="">Tous</option>
                                        <?php foreach ($types_besoin as $type): ?>
                                            <option value="<?= $type['id'] ?>">
                                                <?= $type['libelle'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <!-- Sélection article -->
                                <div class="mb-3">
                                    <label>📦 Article *</label>
                                    <select name="id_article_existant" id="article_existant" class="form-select">
                                        <option value="">-- Sélectionner --</option>
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
                            </div>
                        </div>
                    </div>

                    <!-- 3b. SECTION NOUVEL ARTICLE -->
                    <div id="section-nouveau" class="d-none">
                        <div class="card border-success mb-3">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label>🏷️ Nom de l'Article *</label>
                                        <input type="text" name="nouveau_nom" id="nouveau_nom" class="form-control" placeholder="Ex: Riz local">
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label>📑 Type de Besoin *</label>
                                        <select name="id_type_besoin_nouveau" id="nouveau_type" class="form-select">
                                            <option value="">-- Sélectionner --</option>
                                            <?php foreach ($types_besoin as $type): ?>
                                                <option value="<?= $type['id'] ?>">
                                                    <?= $type['libelle'] ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label>💰 Prix Unitaire (Ar) *</label>
                                        <input type="number" name="nouveau_prix" id="nouveau_prix" class="form-control" step="0.01" min="0">
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label>📏 Unité *</label>
                                        <input type="text" name="nouveau_unite" id="nouveau_unite" class="form-control" list="unites" placeholder="kg, L, pièce...">
                                        <datalist id="unites">
                                            <option value="kg">
                                            <option value="L">
                                            <option value="pièce">
                                            <option value="sac">
                                            <option value="tôle">
                                        </datalist>
                                    </div>
                                </div>
                                <div class="alert alert-warning">
                                    ⚠️ Cet article sera ajouté à la BDD
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 4. QUANTITÉ -->
                    <div class="mb-3">
                        <label>🧮 Quantité Nécessaire *</label>
                        <div class="input-group">
                            <input type="number" name="quantite" id="quantite" class="form-control" step="0.01" min="0.01" required>
                            <span class="input-group-text" id="unite-display">unité</span>
                        </div>
                    </div>

                    <!-- 5. VALEUR ESTIMÉE -->
                    <div class="mb-4">
                        <label>💵 Valeur Estimée</label>
                        <div class="input-group">
                            <input type="text" id="valeur-estimee" class="form-control bg-light" readonly>
                            <span class="input-group-text bg-success text-white">Ar</span>
                        </div>
                    </div>

                    <!-- BOUTONS -->
                    <div class="row">
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-primary w-100">
                                💾 Enregistrer
                            </button>
                        </div>
                        <div class="col-md-3">
                            <a href="/besoins" class="btn btn-secondary w-100">⬅️ Retour</a>
                        </div>
                        <div class="col-md-3">
                            <button type="reset" class="btn btn-outline-warning w-100">🔄 Reset</button>
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
                    <li>Choisir le mode (existant/nouveau)</li>
                    <li>Indiquer la quantité</li>
                    <li>Vérifier la valeur</li>
                    <li>Enregistrer</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<script>
// JAVASCRIPT POUR LA GESTION DU FORMULAIRE
document.addEventListener('DOMContentLoaded', function() {
    const modeExistant = document.getElementById('mode_existant');
    const modeNouveau = document.getElementById('mode_nouveau');
    const sectionExistant = document.getElementById('section-existant');
    const sectionNouveau = document.getElementById('section-nouveau');
    const articleExistant = document.getElementById('article_existant');
    const typeFilter = document.getElementById('type_besoin_filter');
    const quantite = document.getElementById('quantite');
    const uniteDisplay = document.getElementById('unite-display');
    const valeurEstimee = document.getElementById('valeur-estimee');
    const nouveauPrix = document.getElementById('nouveau_prix');
    const nouveauUnite = document.getElementById('nouveau_unite');
    const nouveauNom = document.getElementById('nouveau_nom');
    const nouveauType = document.getElementById('nouveau_type');
    
    let prixUnitaire = 0;
    
    // Basculer entre les modes
    modeExistant.addEventListener('change', () => {
        sectionExistant.classList.remove('d-none');
        sectionNouveau.classList.add('d-none');
        articleExistant.required = true;
        nouveauNom.required = false;
        nouveauType.required = false;
        nouveauPrix.required = false;
        nouveauUnite.required = false;
    });
    
    modeNouveau.addEventListener('change', () => {
        sectionNouveau.classList.remove('d-none');
        sectionExistant.classList.add('d-none');
        articleExistant.required = false;
        nouveauNom.required = true;
        nouveauType.required = true;
        nouveauPrix.required = true;
        nouveauUnite.required = true;
    });
    
    // Filtrer les articles par type
    typeFilter.addEventListener('change', function() {
        const typeId = this.value;
        Array.from(articleExistant.options).forEach(option => {
            if (option.value === '') return;
            if (!typeId || option.dataset.type === typeId) {
                option.style.display = '';
            } else {
                option.style.display = 'none';
            }
        });
    });
    
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
    
    // Nouvel article: prix
    nouveauPrix.addEventListener('input', function() {
        prixUnitaire = parseFloat(this.value) || 0;
        calculer();
    });
    
    // Nouvel article: unité
    nouveauUnite.addEventListener('input', function() {
        if (this.value) {
            uniteDisplay.textContent = this.value;
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