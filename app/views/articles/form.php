<?php
$title = "Ajouter un Article - BNGRC";
ob_start();
?>

<div class="page-header mb-4">
    <h1 class="page-title">
        <i class="fas fa-box"></i>
        Enregistrer un nouvel article
    </h1>
    <p class="page-subtitle">Saisie des articles disponibles pour répondre aux besoins</p>
</div>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-list"></i> Informations de l'article
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

                <form method="POST" action="<?= BASE_URL ?>/articles/ajouter" id="formArticle">
                    <!-- Nom de l'article -->
                    <div class="mb-3">
                        <label for="nom" class="form-label">
                            <i class="fas fa-heading"></i> Nom de l'article *
                        </label>
                        <input type="text" class="form-control" id="nom" name="nom" 
                               placeholder="Ex: Riz, Eau potable, Couverture..." 
                               required maxlength="255" value="<?= isset($_POST['nom']) ? htmlspecialchars($_POST['nom']) : '' ?>">
                        <div class="form-text">Désignation claire et précise de l'article</div>
                    </div>

                    <div class="row">
                        <!-- Prix unitaire -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="prix_unitaire" class="form-label">
                                    <i class="fas fa-money-bill"></i> Prix unitaire (Ar) *
                                </label>
                                <input type="number" class="form-control" id="prix_unitaire" 
                                       name="prix_unitaire" placeholder="0.00" 
                                       step="0.01" min="0" required
                                       value="<?= isset($_POST['prix_unitaire']) ? htmlspecialchars($_POST['prix_unitaire']) : '' ?>">
                                <div class="form-text">Prix unitaire en Ariary (MGA)</div>
                            </div>
                        </div>

                        <!-- Unité de mesure -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="unite" class="form-label">
                                    <i class="fas fa-ruler"></i> Unité de mesure *
                                </label>

                                <select class="form-select" id="unite" name="unite" required>
                                    <option value="">-- Sélectionner une unité --</option>
                                    <option value="kg" <?= (isset($_POST['unite']) && $_POST['unite'] == 'kg') ? 'selected' : '' ?>>
                                        Kilogramme (kg)
                                    </option>
                                    <option value="l" <?= (isset($_POST['unite']) && $_POST['unite'] == 'l') ? 'selected' : '' ?>>
                                        Litre (l)
                                    </option>
                                    <option value="m" <?= (isset($_POST['unite']) && $_POST['unite'] == 'm') ? 'selected' : '' ?>>
                                        Mètre (m)
                                    </option>
                                    <option value="m2" <?= (isset($_POST['unite']) && $_POST['unite'] == 'm2') ? 'selected' : '' ?>>
                                        Mètre carré (m²)
                                    </option>
                                    <option value="piece" <?= (isset($_POST['unite']) && $_POST['unite'] == 'piece') ? 'selected' : '' ?>>
                                        Pièce
                                    </option>
                                    <option value="boite" <?= (isset($_POST['unite']) && $_POST['unite'] == 'boite') ? 'selected' : '' ?>>
                                        Boîte
                                    </option>
                                    <option value="colis" <?= (isset($_POST['unite']) && $_POST['unite'] == 'colis') ? 'selected' : '' ?>>
                                        Colis
                                    </option>
                                    <option value="carton" <?= (isset($_POST['unite']) && $_POST['unite'] == 'carton') ? 'selected' : '' ?>>
                                        Carton
                                    </option>
                                    <option value="autre" <?= (isset($_POST['unite']) && $_POST['unite'] == 'autre') ? 'selected' : '' ?>>
                                        Autre
                                    </option>
                                </select>
                                <div class="form-text">Unité de mesure de l'article</div>
                            </div>
                        </div>
                    </div>

                    <!-- Type de besoin -->
                    <div class="mb-4">
                        <label for="id_type_besoin" class="form-label">
                            <i class="fas fa-list-check"></i> Type de besoin *
                        </label>
                        <?php if (isset($types_besoin) && !empty($types_besoin)): ?>
                            <select class="form-select" id="id_type_besoin" name="id_type_besoin" required>
                                <option value="">-- Sélectionner un type --</option>
                                <?php foreach ($types_besoin as $type): ?>
                                    <option value="<?= htmlspecialchars($type['id']) ?>"
                                        <?= (isset($_POST['id_type_besoin']) && $_POST['id_type_besoin'] == $type['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($type['libelle']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">Catégorie de besoin à laquelle correspond cet article</div>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                Aucun type de besoin disponible. Veuillez d'abord créer des types de besoin.
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Boutons d'action -->
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Enregistrer l'article
                        </button>
                        <a href="<?= BASE_URL ?>/articles" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .form-actions {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }
    
    .form-label {
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
    }
    
    .form-text {
        font-size: 0.85rem;
        color: #6c757d;
        margin-top: 4px;
    }
    
    .card {
        border-left: 4px solid #0d6efd;
    }
    
    .page-header {
        margin-bottom: 30px;
    }
    
    .page-title {
        color: #0d6efd;
        font-weight: 700;
        margin-bottom: 5px;
    }
    
    .page-subtitle {
        color: #6c757d;
        font-size: 14px;
    }
</style>

<?php
$content = ob_get_clean();
include(__DIR__ . '/../layout/layout.php');

?>
