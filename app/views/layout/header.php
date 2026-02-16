    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-dark bngrc-navbar">
        <div class="container-fluid">
            <a class="navbar-brand bngrc-brand" href="/">
                <i class="fas fa-hands-helping"></i>
                <span class="brand-text">BNGRC - Suivi des Dons</span>
            </a>
            
            <button class="navbar-toggler sidebar-toggle d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar">
                <i class="fas fa-bars"></i>
            </button>

            <ul class="navbar-nav ms-auto bngrc-nav">
                <li class="nav-item">
                    <a href="/" class="nav-link<?= $_SERVER['REQUEST_URI'] == '/' ? ' active' : '' ?>">
                        <i class="fas fa-tachometer-alt"></i> Tableau de bord
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/besoins" class="nav-link<?= strpos($_SERVER['REQUEST_URI'], '/besoins') !== false ? ' active' : '' ?>">
                        <i class="fas fa-list-ul"></i> Besoins
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/villes" class="nav-link<?= strpos($_SERVER['REQUEST_URI'], '/villes') !== false ? ' active' : '' ?>">
                        <i class="fas fa-city"></i> Villes
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/dons" class="nav-link<?= strpos($_SERVER['REQUEST_URI'], '/dons') !== false ? ' active' : '' ?>">
                        <i class="fas fa-gift"></i> Dons
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/distributions" class="nav-link<?= strpos($_SERVER['REQUEST_URI'], '/distributions') !== false ? ' active' : '' ?>">
                        <i class="fas fa-truck"></i> Distributions
                    </a>
                </li>
            </ul>
        </div>
    </nav>