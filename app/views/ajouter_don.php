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
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-gift"></i> Informations du don
                </h3>
            </div>
            <div class="card-body">
                <form method="POST" action="/dons/ajouter">
                    <div class="row">
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
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="categorie" class="form-label">
                                    <i class="fas fa-tags"></i> Catégorie *
                                </label>
                                <select class="form-select" id="categorie" onchange="updateItems()" required>
                                    <option value="">Sélectionnez une catégorie</option>
                                    <?php foreach ($categories as $categorie): ?>
                                        <option value="<?= $categorie['id'] ?>"><?= $categorie['nom'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="item" class="form-label">
                                    <i class="fas fa-gift"></i> Article donné *
                                </label>
                                <select class="form-select" id="item" name="item" onchange="updatePrix()" required>
                                    <option value="">Sélectionnez d'abord une catégorie</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="quantite" class="form-label">
                                    <i class="fas fa-calculator"></i> Quantité donnée *
                                </label>
                                <input type="number" class="form-control" id="quantite" name="quantite" 
                                       min="1" placeholder="Ex: 50" onchange="calculateTotal()" required>
                                <div class="form-text" id="unite-text"></div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="valeur_unitaire" class="form-label">
                                    <i class="fas fa-coins"></i> Valeur unitaire (Ar) *
                                </label>
                                <input type="number" class="form-control" id="valeur_unitaire" name="valeur_unitaire" 
                                       min="1" onchange="calculateTotal()" required>
                                <div class="form-text">Valeur estimée par unité</div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-money-bill-wave"></i> Valeur totale du don
                                </label>
                                <div class="form-control bg-light" id="valeur_totale">0 Ar</div>
                                <div class="form-text">Quantité × Valeur unitaire</div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-success">
                        <i class="fas fa-info-circle"></i>
                        <strong>Information :</strong> Les dons sont distribués automatiquement aux villes selon l'ordre chronologique des besoins. 
                        Les besoins les plus anciens sont prioritaires.
                    </div>

                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Remarque :</strong> Vous pouvez ajuster la valeur unitaire si elle diffère du prix de référence. 
                        Cela peut arriver selon la qualité ou l'état des articles donnés.
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
                <h5 class="mb-0"><i class="fas fa-question-circle"></i> Aide pour l'enregistrement</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="fas fa-leaf text-success"></i> Dons en Nature</h6>
                        <ul class="small">
                            <li>Riz, huile, haricots, sucre</li>
                            <li>Quantités en kg ou litres</li>
                            <li>Vérifier la qualité des produits</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="fas fa-tools text-primary"></i> Matériaux</h6>
                        <ul class="small">
                            <li>Tôles, clous, bois, ciment</li>
                            <li>État neuf ou d'occasion</li>
                            <li>Ajuster la valeur selon l'état</li>
                        </ul>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <h6><i class="fas fa-coins text-warning"></i> Dons en Argent</h6>
                        <p class="small mb-0">
                            Pour les dons monétaires, sélectionner "Argent liquide" et saisir le montant en Ariary. 
                            La valeur unitaire sera de 1 Ar par Ariary donné.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Données des catégories depuis PHP
const categories = <?= json_encode($categories) ?>;

function updateItems() {
    const categorieSelect = document.getElementById('categorie');
    const itemSelect = document.getElementById('item');
    const categorieId = parseInt(categorieSelect.value);
    
    // Vider la liste des articles
    itemSelect.innerHTML = '<option value="">Sélectionnez un article</option>';
    document.getElementById('valeur_unitaire').value = '';
    document.getElementById('valeur_totale').textContent = '0 Ar';
    document.getElementById('unite-text').textContent = '';
    
    if (categorieId) {
        const categorie = categories.find(c => c.id === categorieId);
        if (categorie) {
            categorie.items.forEach(item => {
                const option = document.createElement('option');
                option.value = item.nom;
                option.textContent = `${item.nom} (${item.unite})`;
                option.dataset.prix = item.prix_unitaire;
                option.dataset.unite = item.unite;
                itemSelect.appendChild(option);
            });
        }
    }
}

function updatePrix() {
    const itemSelect = document.getElementById('item');
    const selectedOption = itemSelect.options[itemSelect.selectedIndex];
    
    if (selectedOption.dataset.prix) {
        document.getElementById('valeur_unitaire').value = selectedOption.dataset.prix;
        document.getElementById('unite-text').textContent = `Unité : ${selectedOption.dataset.unite}`;
        calculateTotal();
    }
}

function calculateTotal() {
    const quantite = parseFloat(document.getElementById('quantite').value) || 0;
    const valeurUnitaire = parseFloat(document.getElementById('valeur_unitaire').value) || 0;
    const total = quantite * valeurUnitaire;
    
    document.getElementById('valeur_totale').textContent = new Intl.NumberFormat('fr-FR').format(total) + ' Ar';
}

// Validation du formulaire
document.querySelector('form').addEventListener('submit', function(e) {
    const donateur = document.getElementById('donateur').value.trim();
    const quantite = document.getElementById('quantite').value;
    const item = document.getElementById('item').value;
    const valeurUnitaire = document.getElementById('valeur_unitaire').value;
    
    if (!donateur || !item || !quantite || quantite <= 0 || !valeurUnitaire || valeurUnitaire <= 0) {
        e.preventDefault();
        alert('Veuillez remplir tous les champs obligatoires avec des valeurs valides.');
        return false;
    }
});

// Auto-complétion pour les donateurs fréquents
const donateursFrequents = [
    'Croix-Rouge Malagasy',
    'UNICEF Madagascar',
    'World Food Programme',
    'Action Contre la Faim',
    'Médecins Sans Frontières',
    'CARE International',
    'Fondation Tany Meva'
];

const donateurInput = document.getElementById('donateur');
donateurInput.addEventListener('input', function() {
    // Ici on pourrait ajouter une logique d'auto-complétion
    // Pour l'instant, on se contente d'une validation simple
});
</script>

<?php
$content = ob_get_clean();
include 'layout.php';
?>