<!-- Sidebar -->
<aside class="main-sidebar sidebar-primary elevation-4">
    <!-- Sidebar Menu -->
    <nav class="mt-2 sidebar-nav">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            

            <!-- Gestion des Articles -->
            <li class="nav-item<?= strpos($_SERVER['REQUEST_URI'], '/articles') !== false ? ' menu-open' : '' ?>">
                <a href="<?= BASE_URL ?>/articles" class="nav-link<?= strpos($_SERVER['REQUEST_URI'], '/articles') !== false ? ' active' : '' ?>">
                    <i class="nav-icon fas fa-boxes"></i> <p>Articles</p>
                </a>
            </li>

            <!-- Gestion des Besoins -->
            <li class="nav-item<?= strpos($_SERVER['REQUEST_URI'], '/besoins') !== false ? ' menu-open' : '' ?>">
                <a href="<?= BASE_URL ?>/besoins" class="nav-link<?= strpos($_SERVER['REQUEST_URI'], '/besoins') !== false ? ' active' : '' ?>">
                    <i class="nav-icon fas fa-list-ul"></i> <p>Besoins</p>
                </a>
            </li>

            <!-- Gestion des Villes -->
            <li class="nav-item<?= strpos($_SERVER['REQUEST_URI'], '/villes') !== false ? ' menu-open' : '' ?>">
                <a href="<?= BASE_URL ?>/villes" class="nav-link<?= strpos($_SERVER['REQUEST_URI'], '/villes') !== false ? ' active' : '' ?>">
                    <i class="nav-icon fas fa-city"></i> <p>Villes</p>
                </a>
            </li>

            <!-- Gestion des Dons -->
            <li class="nav-item<?= strpos($_SERVER['REQUEST_URI'], '/dons') !== false ? ' menu-open' : '' ?>">
                <a href="<?= BASE_URL ?>/dons" class="nav-link<?= strpos($_SERVER['REQUEST_URI'], '/dons') !== false ? ' active' : '' ?>">
                    <i class="nav-icon fas fa-gift"></i> <p>Dons</p>
                </a>
            </li>

            <!-- Gestion des Distributions -->
            <li class="nav-item<?= strpos($_SERVER['REQUEST_URI'], '/distributions') !== false ? ' menu-open' : '' ?>">
                <a href="<?= BASE_URL ?>/distributions" class="nav-link<?= strpos($_SERVER['REQUEST_URI'], '/distributions') !== false && strpos($_SERVER['REQUEST_URI'], '/simulateur') === false ? ' active' : '' ?>">
                    <i class="nav-icon fas fa-truck"></i> <p>Distributions</p>
                </a>
            </li>

            <!-- Simulateur -->
            <li class="nav-item">
                <a href="<?= BASE_URL ?>/distributions/simulateur" class="nav-link<?= strpos($_SERVER['REQUEST_URI'], '/simulateur') !== false ? ' active' : '' ?>">
                    <i class="nav-icon fas fa-magic"></i> <p>Simulateur</p>
                </a>
            </li>

            <!-- Achats -->
            <li class="nav-item">
                <a href="<?= BASE_URL ?>/achats" class="nav-link<?= strpos($_SERVER['REQUEST_URI'], '/achats') !== false ? ' active' : '' ?>">
                    <i class="nav-icon fas fa-shopping-cart"></i> <p>Achats</p>
                </a>
            </li>

            <!-- Récapitulatif -->
            <li class="nav-item">
                <a href="<?= BASE_URL ?>/recap" class="nav-link<?= strpos($_SERVER['REQUEST_URI'], '/recap') !== false ? ' active' : '' ?>">
                    <i class="nav-icon fas fa-chart-pie"></i> <p>Récapitulatif</p>
                </a>
            </li>

            <!-- Configuration -->
            <li class="nav-item">
                <a href="<?= BASE_URL ?>/config" class="nav-link<?= strpos($_SERVER['REQUEST_URI'], '/config') !== false ? ' active' : '' ?>">
                    <i class="nav-icon fas fa-cogs"></i> <p>Configuration</p>
                </a>
            </li>

        </ul>
    </nav>
    <!-- /.sidebar-menu -->
</aside>