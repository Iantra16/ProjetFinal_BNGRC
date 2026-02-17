<?php include(__DIR__ . "/../function.php"); ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $title ?? 'Takalo-takalo' ?></title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- AdminLTE 4 -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/dist/css/adminlte.min.css">
    <!-- CSS personnalisé -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
    <!-- CSS BNGRC -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
    </head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <?php include("header.php"); ?>

    <?php include("sidebar.php"); ?>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <div class="content">
            <div class="container-fluid mt-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
                            <div>
                                <div class="text-muted text-uppercase small">BNGRC - Gestion des dons</div>
                                <h1 class="h4 mb-0"><?= $title ?? 'Suivi des collectes et distributions' ?></h1>
                            </div>
                        </div>
                        <?= $content ?? '' ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include("footer.php"); ?>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- Bootstrap 5 (pour AdminLTE 4) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="<?= BASE_URL ?>/assets/dist/js/adminlte.min.js"></script>
</body>
</html>