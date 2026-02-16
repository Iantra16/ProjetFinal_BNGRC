<?php
$title = "Ajouter un Besoin - BNGRC";
ob_start();
?>

<div class="page-header mb-4">
    <h1 class="page-title">
        <i class="fas fa-clipboard-list"></i>
        Saisir un nouveau besoin
    </h1>
    <p class="page-subtitle">Enregistrez les besoins d'une ville sinistrée</p>
</div>

<div class="row">
    <div class="col-lg-10 mx-auto">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-city"></i> Informations du besoin
                </h3>
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
                
                <form method="POST" action="<?= BASE_URL ?>/besoins" id="formBesoin">
                    <!-- Ville -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="id_ville" class="form-label">
                                    <i class="fas fa-map-marker-alt"></i> Ville concernée *
                                </label>
                                <select name="id_ville" id="id_ville" class="form-select" required>
                                    <option value="">Sélectionnez une ville</option>
                                    <?php foreach ($villes as $v): ?>
                                        <option value="<?= $v['id'] ?>">
                                            <?= $v['nom'] ?> (<?= $v['region_nom'] ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text">Ville qui a besoin d'assistance</div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <hr class="my-4">

                    <!-- Articles -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">
                                <i class="fas fa-list-alt"></i> Articles nécessaires
                            </h5>
                            <button type="button" class="btn btn-primary btn-sm" onclick="addArticleLine()">
                                <i class="fas fa-plus"></i> Ajouter un article
                            </button>
                        </div>
                        
                        <div id="articles-container">
                            <!-- Les lignes d'articles seront ajoutées ici dynamiquement -->
                        </div>
                    </div>

                    <!-- Total estimé -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">
                                            <i class="fas fa-calculator text-primary"></i> Valeur totale estimée du besoin
                                        </h5>
                                        <h4 class="mb-0 text-primary" id="valeur_totale_globale">0 Ar</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle"></i>
                        <strong>Information :</strong> Les besoins enregistrés ici seront satisfaits par les dons disponibles selon l'ordre chronologique de saisie.
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Enregistrer le Besoin
                        </button>
                        <a href="<?= BASE_URL ?>/villes" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Aide contextuelle -->
        <div class="card mt-4">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="fas fa-question-circle"></i> Guide d'utilisation</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <h6><i class="fas fa-leaf text-success"></i> Nature</h6>
                        <ul class="small">
                            <li>Denrées alimentaires</li>
                            <li>Riz, huile, haricots, sucre</li>
                            <li>Quantité nécessaire en kg ou litres</li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <h6><i class="fas fa-tools text-primary"></i> Matériau</h6>
                        <ul class="small">
                            <li>Matériaux de construction</li>
                            <li>Tôles, clous, bois, ciment</li>
                            <li>Quantité pour reconstruction</li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <h6><i class="fas fa-coins text-warning"></i> Argent</h6>
                        <ul class="small">
                            <li>Besoin de fonds</li>
                            <li>Montant nécessaire en Ariary</li>
                            <li>Aide financière directe</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Données depuis PHP
const types_besoin = <?= json_encode($types_besoin) ?>;
const articles = <?= json_encode($articles) ?>;

// Organiser les articles par type de besoin
const articlesByType = {};
articles.forEach(article => {
    const typeId = article.id_type_besoin;
    if (!articlesByType[typeId]) {
        articlesByType[typeId] = [];
    }
    articlesByType[typeId].push(article);
});

let articleCounter = 0;

// Initialiser avec une première ligne
document.addEventListener('DOMContentLoaded', function() {
    addArticleLine();
});

function addArticleLine() {
    articleCounter++;
    const container = document.getElementById('articles-container');
    
    const lineDiv = document.createElement('div');
    lineDiv.className = 'article-line card mb-3';
    lineDiv.id = `article-line-${articleCounter}`;
    lineDiv.dataset.lineId = articleCounter;
    
    lineDiv.innerHTML = `
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0"><i class="fas fa-box"></i> Article #${articleCounter}</h6>
                ${articleCounter > 1 ? `
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeArticleLine(${articleCounter})">
                        <i class="fas fa-trash"></i> Supprimer
                    </button>
                ` : ''}
            </div>
            
            <div class="row">
                <div class="col-md-3">
                    <label class="form-label">Type *</label>
                    <select class="form-select" name="articles[${articleCounter}][type]" 
                            onchange="updateArticlesForType(${articleCounter})" required>
                        <option value="">Sélectionnez un type</option>
                        ${types_besoin.map(type => `<option value="${type.id}">${type.libelle}</option>`).join('')}
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Article *</label>
                    <select class="form-select" name="articles[${articleCounter}][article_id]" 
                            id="article-select-${articleCounter}"
                            onchange="updateArticleFields(${articleCounter})" required>
                        <option value="">Sélectionnez d'abord un type</option>
                    </select>
                </div>
                
                <div class="col-md-6" id="fields-container-${articleCounter}">
                    <!-- Les champs seront ajoutés dynamiquement selon le type -->
                </div>
            </div>
            
            <div class="row mt-2">
                <div class="col-md-12">
                    <div class="text-end">
                        <strong>Valeur estimée de cet article : <span id="total-line-${articleCounter}" class="text-primary">0 Ar</span></strong>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    container.appendChild(lineDiv);
}

function removeArticleLine(lineId) {
    const line = document.getElementById(`article-line-${lineId}`);
    if (line) {
        line.remove();
        calculateGlobalTotal();
    }
}

function updateArticlesForType(lineId) {
    const typeBesoinSelect = document.querySelector(`#article-line-${lineId} select[name="articles[${lineId}][type]"]`);
    const articleSelect = document.getElementById(`article-select-${lineId}`);
    const fieldsContainer = document.getElementById(`fields-container-${lineId}`);
    const typeId = typeBesoinSelect.value;
    
    // Réinitialiser
    articleSelect.innerHTML = '<option value="">Sélectionnez un article</option>';
    fieldsContainer.innerHTML = '';
    document.getElementById(`total-line-${lineId}`).textContent = '0 Ar';
    
    if (typeId && articlesByType[typeId]) {
        articlesByType[typeId].forEach(article => {
            const option = document.createElement('option');
            option.value = article.id;
            option.textContent = `${article.nom} (${article.unite})`;
            option.dataset.prix = article.prix_unitaire;
            option.dataset.unite = article.unite;
            option.dataset.nom = article.nom;
            articleSelect.appendChild(option);
        });
    }
}

function updateArticleFields(lineId) {
    const articleSelect = document.getElementById(`article-select-${lineId}`);
    const selectedOption = articleSelect.options[articleSelect.selectedIndex];
    const fieldsContainer = document.getElementById(`fields-container-${lineId}`);
    const typeBesoinSelect = document.querySelector(`#article-line-${lineId} select[name="articles[${lineId}][type]"]`);
    const selectedType = types_besoin.find(t => t.id == typeBesoinSelect.value);
    
    if (!selectedOption.value) {
        fieldsContainer.innerHTML = '';
        return;
    }
    
    const prix = selectedOption.dataset.prix;
    const unite = selectedOption.dataset.unite;
    const nomArticle = selectedOption.dataset.nom;
    
    // Détecter si c'est de l'argent
    const isArgent = selectedType && selectedType.libelle.toLowerCase().includes('argent');
    
    if (isArgent) {
        // Pour l'argent : seulement le montant
        fieldsContainer.innerHTML = `
            <div class="row">
                <div class="col-md-12">
                    <label class="form-label">Montant nécessaire (Ar) *</label>
                    <input type="number" class="form-control" 
                           name="articles[${lineId}][montant]" 
                           id="montant-${lineId}"
                           min="1" step="1" 
                           placeholder="Ex: 500000"
                           onchange="calculateLineTotal(${lineId}, true)" required>
                    <input type="hidden" name="articles[${lineId}][quantite]" id="hidden-quantite-${lineId}">
                </div>
            </div>
        `;
    } else {
        // Pour Nature et Matériau : quantité + valeur estimée
        fieldsContainer.innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <label class="form-label">Quantité nécessaire *</label>
                    <input type="number" class="form-control" 
                           name="articles[${lineId}][quantite]" 
                           id="quantite-${lineId}"
                           min="0.01" step="0.01" 
                           placeholder="Ex: 50"
                           onchange="calculateLineTotal(${lineId})" required>
                    <small class="form-text">Unité : ${unite}</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Valeur estimée</label>
                    <div class="input-group">
                        <div class="form-control bg-light" id="subtotal-${lineId}">0 Ar</div>
                        <span class="input-group-text">${prix} Ar/${unite}</span>
                    </div>
                </div>
            </div>
        `;
    }
}

function calculateLineTotal(lineId, isArgent = false) {
    let total = 0;
    
    if (isArgent) {
        const montant = parseFloat(document.getElementById(`montant-${lineId}`).value) || 0;
        total = montant;
        // Pour l'argent, le montant est la quantité
        const hiddenQuantite = document.getElementById(`hidden-quantite-${lineId}`);
        if (hiddenQuantite) {
            hiddenQuantite.value = montant;
        }
    } else {
        const quantite = parseFloat(document.getElementById(`quantite-${lineId}`).value) || 0;
        const articleSelect = document.getElementById(`article-select-${lineId}`);
        const selectedOption = articleSelect.options[articleSelect.selectedIndex];
        const prixUnitaire = parseFloat(selectedOption.dataset.prix) || 0;
        total = quantite * prixUnitaire;
        
        const subtotalDiv = document.getElementById(`subtotal-${lineId}`);
        if (subtotalDiv) {
            subtotalDiv.textContent = new Intl.NumberFormat('fr-FR').format(total) + ' Ar';
        }
    }
    
    document.getElementById(`total-line-${lineId}`).textContent = new Intl.NumberFormat('fr-FR').format(total) + ' Ar';
    calculateGlobalTotal();
}

function calculateGlobalTotal() {
    let globalTotal = 0;
    const lines = document.querySelectorAll('.article-line');
    
    lines.forEach(line => {
        const lineId = line.dataset.lineId;
        const typeBesoinSelect = line.querySelector(`select[name="articles[${lineId}][type]"]`);
        const articleSelect = document.getElementById(`article-select-${lineId}`);
        
        if (typeBesoinSelect && articleSelect && articleSelect.value) {
            const selectedType = types_besoin.find(t => t.id == typeBesoinSelect.value);
            const isArgent = selectedType && selectedType.libelle.toLowerCase().includes('argent');
            
            if (isArgent) {
                const montantInput = document.getElementById(`montant-${lineId}`);
                if (montantInput) {
                    globalTotal += parseFloat(montantInput.value) || 0;
                }
            } else {
                const quantiteInput = document.getElementById(`quantite-${lineId}`);
                const selectedOption = articleSelect.options[articleSelect.selectedIndex];
                if (quantiteInput && selectedOption) {
                    const quantite = parseFloat(quantiteInput.value) || 0;
                    const prix = parseFloat(selectedOption.dataset.prix) || 0;
                    globalTotal += quantite * prix;
                }
            }
        }
    });
    
    document.getElementById('valeur_totale_globale').textContent = new Intl.NumberFormat('fr-FR').format(globalTotal) + ' Ar';
}

// Validation du formulaire
document.getElementById('formBesoin').addEventListener('submit', function(e) {
    const villeId = document.getElementById('id_ville').value;
    const lines = document.querySelectorAll('.article-line');
    
    if (!villeId) {
        e.preventDefault();
        alert('Veuillez sélectionner une ville.');
        return false;
    }
    
    if (lines.length === 0) {
        e.preventDefault();
        alert('Veuillez ajouter au moins un article.');
        return false;
    }
    
    let hasValidArticle = false;
    lines.forEach(line => {
        const lineId = line.dataset.lineId;
        const articleSelect = document.getElementById(`article-select-${lineId}`);
        if (articleSelect && articleSelect.value) {
            hasValidArticle = true;
        }
    });
    
    if (!hasValidArticle) {
        e.preventDefault();
        alert('Veuillez sélectionner au moins un article valide.');
        return false;
    }
});
</script>

<?php
$content = ob_get_clean();
include(__DIR__ . '/../layout/layout.php');
?>