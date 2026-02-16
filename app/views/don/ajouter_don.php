<?php
$title = "Ajouter un don - BNGRC";
ob_start();
?>

<div class="page-header mb-4">
    <h1 class="page-title">
        <i class="fas fa-heart"></i>
        Enregistrer un nouveau don
    </h1>
    <p class="page-subtitle">Saisie des dons reçus pour les sinistrés</p>
</div>

<div class="row">
    <div class="col-lg-10 mx-auto">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-gift"></i> Informations du don
                </h3>
            </div>
            <div class="card-body">
                <form method="POST" action="/dons/ajouter" id="donForm">
                    <!-- Donateur -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="donateur" class="form-label">
                                    <i class="fas fa-user"></i> Nom du Donateur *
                                </label>
                                <input type="text" class="form-control" id="donateur" name="donateur" 
                                       placeholder="Ex: Association Humanitaire, Entreprise ABC, M. Dupont..." required>
                                <div class="form-text">Nom de la personne, organisation ou entreprise donatrice</div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Articles -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">
                                <i class="fas fa-list"></i> Articles donnés
                            </h5>
                            <button type="button" class="btn btn-primary btn-sm" onclick="addArticleLine()">
                                <i class="fas fa-plus"></i> Ajouter un article
                            </button>
                        </div>
                        
                        <div id="articles-container">
                            <!-- Les lignes d'articles seront ajoutées ici dynamiquement -->
                        </div>
                    </div>

                    <!-- Total général -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">
                                            <i class="fas fa-money-bill-wave text-success"></i> Valeur totale du don
                                        </h5>
                                        <h4 class="mb-0 text-success" id="valeur_totale_globale">0 Ar</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle"></i>
                        <strong>Information :</strong> Les dons sont distribués automatiquement aux villes selon l'ordre chronologique des besoins. 
                        Les besoins les plus anciens sont prioritaires.
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Enregistrer le Don
                        </button>
                        <a href="/dons" class="btn btn-secondary">
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
                            <li>Quantité en kg ou litres</li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <h6><i class="fas fa-tools text-primary"></i> Matériau</h6>
                        <ul class="small">
                            <li>Matériaux de construction</li>
                            <li>Tôles, clous, bois, ciment</li>
                            <li>État neuf ou d'occasion</li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <h6><i class="fas fa-coins text-warning"></i> Argent</h6>
                        <ul class="small">
                            <li>Dons monétaires</li>
                            <li>Montant en Ariary</li>
                            <li>Argent liquide ou virement</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Données depuis PHP
const categories = <?= json_encode($categories) ?>;
const articlesByType = <?= json_encode($articlesByType) ?>;

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
                <h6 class="mb-0"><i class="fas fa-gift"></i> Article #${articleCounter}</h6>
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
                        ${categories.map(cat => `<option value="${cat.id}">${cat.libelle}</option>`).join('')}
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
                        <strong>Valeur de cet article : <span id="total-line-${articleCounter}" class="text-success">0 Ar</span></strong>
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
    const selectedType = categories.find(c => c.id == typeBesoinSelect.value);
    
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
                    <label class="form-label">Montant (Ar) *</label>
                    <input type="number" class="form-control" 
                           name="articles[${lineId}][montant]" 
                           id="montant-${lineId}"
                           min="1" step="1" 
                           placeholder="Ex: 500000"
                           onchange="calculateLineTotal(${lineId}, true)" required>
                    <input type="hidden" name="articles[${lineId}][quantite]" value="1">
                    <input type="hidden" name="articles[${lineId}][valeur_unitaire]" id="hidden-valeur-${lineId}">
                </div>
            </div>
        `;
    } else {
        // Pour Nature et Matériau : quantité + valeur unitaire
        fieldsContainer.innerHTML = `
            <div class="row">
                <div class="col-md-4">
                    <label class="form-label">Quantité *</label>
                    <input type="number" class="form-control" 
                           name="articles[${lineId}][quantite]" 
                           id="quantite-${lineId}"
                           min="1" step="0.01" 
                           placeholder="Ex: 50"
                           onchange="calculateLineTotal(${lineId})" required>
                    <small class="form-text">Unité : ${unite}</small>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Valeur unitaire (Ar) *</label>
                    <input type="number" class="form-control" 
                           name="articles[${lineId}][valeur_unitaire]" 
                           id="valeur-unitaire-${lineId}"
                           value="${prix}"
                           min="1" step="1" 
                           onchange="calculateLineTotal(${lineId})" required>
                    <small class="form-text">Prix de référence</small>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Total</label>
                    <div class="form-control bg-light" id="subtotal-${lineId}">0 Ar</div>
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
        // Pour l'argent, le montant est la valeur unitaire (quantité = 1)
        const hiddenValeur = document.getElementById(`hidden-valeur-${lineId}`);
        if (hiddenValeur) {
            hiddenValeur.value = montant;
        }
    } else {
        const quantite = parseFloat(document.getElementById(`quantite-${lineId}`).value) || 0;
        const valeurUnitaire = parseFloat(document.getElementById(`valeur-unitaire-${lineId}`).value) || 0;
        total = quantite * valeurUnitaire;
        
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
            const selectedType = categories.find(c => c.id == typeBesoinSelect.value);
            const isArgent = selectedType && selectedType.libelle.toLowerCase().includes('argent');
            
            if (isArgent) {
                const montantInput = document.getElementById(`montant-${lineId}`);
                if (montantInput) {
                    globalTotal += parseFloat(montantInput.value) || 0;
                }
            } else {
                const quantiteInput = document.getElementById(`quantite-${lineId}`);
                const valeurInput = document.getElementById(`valeur-unitaire-${lineId}`);
                if (quantiteInput && valeurInput) {
                    const quantite = parseFloat(quantiteInput.value) || 0;
                    const valeur = parseFloat(valeurInput.value) || 0;
                    globalTotal += quantite * valeur;
                }
            }
        }
    });
    
    document.getElementById('valeur_totale_globale').textContent = new Intl.NumberFormat('fr-FR').format(globalTotal) + ' Ar';
}

// Validation du formulaire
document.getElementById('donForm').addEventListener('submit', function(e) {
    const donateur = document.getElementById('donateur').value.trim();
    const lines = document.querySelectorAll('.article-line');
    
    if (!donateur) {
        e.preventDefault();
        alert('Veuillez saisir le nom du donateur.');
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