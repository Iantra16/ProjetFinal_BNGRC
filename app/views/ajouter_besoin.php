<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header bg-warning bg-opacity-10">
                <h3 class="mb-0">
                    <i class="fas fa-plus text-warning"></i> Ajouter un Nouveau Besoin
                </h3>
            </div>
            <div class="card-body">
                <form method="POST" action="/besoins/ajouter">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="ville_id" class="form-label">
                                    <i class="fas fa-city"></i> Ville concernée *
                                </label>
                                <select class="form-select" id="ville_id" name="ville_id" required>
                                    <option value="">Sélectionnez une ville</option>
                                    <?php foreach ($villes as $ville): ?>
                                        <option value="<?= $ville['id'] ?>">
                                            <?= $ville['nom'] ?> (<?= $ville['region'] ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
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
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="item" class="form-label">
                                    <i class="fas fa-box"></i> Article *
                                </label>
                                <select class="form-select" id="item" name="item" onchange="updatePrix()" required>
                                    <option value="">Sélectionnez d'abord une catégorie</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="quantite" class="form-label">
                                    <i class="fas fa-calculator"></i> Quantité nécessaire *
                                </label>
                                <input type="number" class="form-control" id="quantite" name="quantite" 
                                       min="1" placeholder="Ex: 100" onchange="calculateTotal()" required>
                                <div class="form-text" id="unite-text"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="prix_unitaire" class="form-label">
                                    <i class="fas fa-coins"></i> Prix unitaire (Ar) *
                                </label>
                                <input type="number" class="form-control" id="prix_unitaire" name="prix_unitaire" 
                                       min="1" readonly required>
                                <div class="form-text">Prix automatique selon l'article sélectionné</div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-money-bill-wave"></i> Valeur totale estimée
                                </label>
                                <div class="form-control bg-light" id="valeur_totale">0 Ar</div>
                                <div class="form-text">Quantité × Prix unitaire</div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Information :</strong> Les besoins sont traités par ordre de priorité selon la date de saisie. 
                        Les besoins les plus anciens ont la priorité lors des distributions.
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save"></i> Enregistrer le Besoin
                        </button>
                        <a href="/besoins" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                    </div>
                </form>
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
    document.getElementById('prix_unitaire').value = '';
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
        document.getElementById('prix_unitaire').value = selectedOption.dataset.prix;
        document.getElementById('unite-text').textContent = `Unité : ${selectedOption.dataset.unite}`;
        calculateTotal();
    }
}

function calculateTotal() {
    const quantite = parseFloat(document.getElementById('quantite').value) || 0;
    const prixUnitaire = parseFloat(document.getElementById('prix_unitaire').value) || 0;
    const total = quantite * prixUnitaire;
    
    document.getElementById('valeur_totale').textContent = new Intl.NumberFormat('fr-FR').format(total) + ' Ar';
}

// Validation du formulaire
document.querySelector('form').addEventListener('submit', function(e) {
    const quantite = document.getElementById('quantite').value;
    const item = document.getElementById('item').value;
    const ville = document.getElementById('ville_id').value;
    
    if (!ville || !item || !quantite || quantite <= 0) {
        e.preventDefault();
        alert('Veuillez remplir tous les champs obligatoires avec des valeurs valides.');
        return false;
    }
});
</script>