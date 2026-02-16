    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                    <i class="fas fa-bars"></i>
                </a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="<?= BASE_URL ?>/user" class="nav-link">Accueil</a>
            </li>
        </ul>

        <ul class="navbar-nav ms-auto">
            <li class="nav-item">
                <a href="<?= BASE_URL ?>/user/logout" class="nav-link">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </a>
            </li>
        </ul>
    </nav>

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="<?= BASE_URL ?>/user" class="brand-link">
            <i class="fas fa-exchange-alt ms-2"></i>
            <span class="brand-text font-weight-light ps-3"> Takalo-takalo</span>
        </a>

        <div class="sidebar">
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                    <li class="nav-item">
                        <a href="<?= BASE_URL ?>/user" class="nav-link">
                            <i class="nav-icon fas fa-home"></i>
                            <p>Accueil</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= BASE_URL ?>/user/myobject" class="nav-link">
                            <i class="nav-icon fas fa-box"></i>
                            <p>Mes objets</p>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>