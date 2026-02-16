<?php
$title = "Tableau de bord - BNGRC";
ob_start();

// Initialiser les variables par défaut
$totalVilles = $totalVilles ?? 0;
$villes = $villes ?? [];
$besoins = $besoins ?? [];
$dons = $dons ?? [];
$distributions = $distributions ?? [];
?>

<div class="page-header mb-4">
    <h1 class="page-title">
        <i class="fas fa-tachometer-alt"></i>
        Tableau de bord - Suivi des dons
    </h1>
    <p class="page-subtitle">Vue d'ensemble des besoins et distributions par ville</p>
</div>

<!-- Statistiques générales -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card primary">
            <i class="fas fa-city text-primary"></i>
            <div class="stats-number text-primary"><?= $totalVilles ?></div>
            <div class="stats-label">Villes concernées</div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card success">
            <i class="fas fa-list-ul text-success"></i>
            <div class="stats-number text-success"><?= count($besoins) ?></div>
            <div class="stats-label">Besoins enregistrés</div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card warning">
            <i class="fas fa-gift text-warning"></i>
            <div class="stats-number text-warning"><?= count($dons) ?></div>
            <div class="stats-label">Dons reçus</div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card info">
            <i class="fas fa-truck text-info"></i>
            <div class="stats-number text-info"><?= count($distributions) ?></div>
            <div class="stats-label">Distributions</div>
        </div>
    </div>
</div>

<!-- Liste des villes en cards -->
<div class="row">
    <div class="col-12 mb-3">
        <h3 class="mb-3">
            <i class="fas fa-map-marked-alt"></i>
            Situation par ville
        </h3>
    </div>
    
    <?php foreach ($villes as $ville): ?>
        <?php
        // Calculer les besoins pour cette ville
        $besoinsVille = array_filter($besoins, fn($b) => $b['id_ville'] == $ville['id']);
        $valeurBesoins = array_sum(array_map(function($b) {
            return $b['valeur_totale'] ?? 0;
        }, $besoinsVille));
        
        // Calculer les dons distribués pour cette ville
        $distributionsVille = array_filter($distributions, fn($d) => $d['ville_id'] == $ville['id']);
        $valeurDistributions = array_sum(array_map(fn($d) => $d['valeur_totale'], $distributionsVille));
        
        // Calculer le pourcentage de satisfaction
        $pourcentage = $valeurBesoins > 0 ? min(100, ($valeurDistributions / $valeurBesoins) * 100) : 0;
        
        // Déterminer le statut
        if ($pourcentage >= 100) {
            $statut = 'Complet';
            $statutClass = 'success';
        } elseif ($pourcentage >= 50) {
            $statut = 'Partiel';
            $statutClass = 'warning';
        } else {
            $statut = 'En attente';
            $statutClass = 'danger';
        }
        ?>
        
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body text-center">
                    <!-- Icône ville -->
                    <div class="mb-3">
                        <i class="fas fa-city fa-3x text-primary"></i>
                    </div>
                    
                    <!-- Nom de la ville -->
                    <h4 class="card-title mb-2">
                        <?= htmlspecialchars($ville['nom']) ?>
                    </h4>
                    
                    <!-- Région -->
                    <p class="text-muted mb-3">
                        <i class="fas fa-map-marker-alt"></i>
                        <?= htmlspecialchars($ville['region_nom']) ?>
                    </p>
                    
                    <!-- Statut -->
                    <div class="mb-3">
                        <span class="badge bg-<?= $statutClass ?> fs-6"><?= $statut ?></span>
                    </div>
                    
                    <!-- Barre de progression -->
                    <div class="progress mb-3" style="height: 25px;">
                        <div class="progress-bar bg-<?= $statutClass ?>" 
                             role="progressbar" 
                             style="width: <?= $pourcentage ?>%"
                             aria-valuenow="<?= $pourcentage ?>" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                            <?= round($pourcentage) ?>%
                        </div>
                    </div>
                    
                    <!-- Statistiques rapides -->
                    <div class="row text-center mb-3">
                        <div class="col-6">
                            <div class="border-end">
                                <h5 class="text-info mb-0"><?= count($besoinsVille) ?></h5>
                                <small class="text-muted">Besoins</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h5 class="text-success mb-0"><?= count($distributionsVille) ?></h5>
                            <small class="text-muted">Dons reçus</small>
                        </div>
                    </div>
                    
                    <div class="row text-center mb-3">
                        <div class="col-12">
                            <small class="text-muted">Valeur besoins: </small>
                            <strong><?= number_format($valeurBesoins, 0, ',', ' ') ?> Ar</strong>
                        </div>
                        <div class="col-12">
                            <small class="text-muted">Valeur dons: </small>
                            <strong class="text-success"><?= number_format($valeurDistributions, 0, ',', ' ') ?> Ar</strong>
                        </div>
                    </div>
                </div>
                
                <!-- Boutons d'action -->
                <div class="card-footer bg-light">
                    <div class="d-grid gap-2">
                        <a href="/villes/<?= $ville['id'] ?>/besoins" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-list-ul"></i> Voir les besoins
                        </a>
                        <a href="/distributions?ville=<?= $ville['id'] ?>" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-gift"></i> Voir les dons reçus
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php
$content = ob_get_clean();
include (__DIR__ . '/layout/layout.php');
?>