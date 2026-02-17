    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-dark bngrc-navbar">
        <div class="container-fluid">
            <a class="navbar-brand bngrc-brand" href="<?= BASE_URL ?>/">
                <i class="fas fa-hands-helping"></i>
                <span class="brand-text">BNGRC - Suivi des Dons</span>
            </a>
            
            <button class="navbar-toggler sidebar-toggle d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar">
                <i class="fas fa-bars"></i>
            </button>

            <ul class="navbar-nav ms-auto bngrc-nav">
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>/articles" class="nav-link<?= strpos($_SERVER['REQUEST_URI'], '/articles') !== false ? ' active' : '' ?>">
                        <i class="fas fa-boxes"></i> Articles
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>/besoins" class="nav-link<?= strpos($_SERVER['REQUEST_URI'], '/besoins') !== false ? ' active' : '' ?>">
                        <i class="fas fa-list-ul"></i> Besoins
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>/villes" class="nav-link<?= strpos($_SERVER['REQUEST_URI'], '/villes') !== false ? ' active' : '' ?>">
                        <i class="fas fa-city"></i> Villes
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>/dons" class="nav-link<?= strpos($_SERVER['REQUEST_URI'], '/dons') !== false ? ' active' : '' ?>">
                        <i class="fas fa-gift"></i> Dons
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>/distributions" class="nav-link<?= strpos($_SERVER['REQUEST_URI'], '/distributions') !== false ? ' active' : '' ?>">
                        <i class="fas fa-truck"></i> Distributions
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>/distributions/simulateur" class="nav-link<?= strpos($_SERVER['REQUEST_URI'], '/simulateur') !== false ? ' active' : '' ?>">
                        <i class="fas fa-magic"></i> Simulateur
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>/achats" class="nav-link<?= strpos($_SERVER['REQUEST_URI'], '/achats') !== false ? ' active' : '' ?>">
                        <i class="fas fa-shopping-cart"></i> Achats
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>/recap" class="nav-link<?= strpos($_SERVER['REQUEST_URI'], '/recap') !== false ? ' active' : '' ?>">
                        <i class="fas fa-chart-pie"></i> Récap
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>/config" class="nav-link<?= strpos($_SERVER['REQUEST_URI'], '/config') !== false ? ' active' : '' ?>">
                        <i class="fas fa-cogs"></i> Config
                    </a>
                </li>
            </ul>
        </div>
    </nav>