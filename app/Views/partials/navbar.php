<?php $currentPage = $currentPage ?? ''; ?>
<!-- Navbar Start -->
<header class="topnav">
    <div class="wrap topnav-inner">
        <a class="brand" href="index.php?page=home">
            <img src="assets/img/logo.svg" alt="" width="34" height="34">
            BloodLink
        </a>
        <button class="menu-toggle" aria-label="Menu" aria-expanded="false">&#9776;</button>
        <nav class="nav-links">
            <a class="<?= navActive('home', $currentPage) ?>" href="index.php?page=home">Accueil</a>
            <a class="<?= navActive('needs', $currentPage) ?>" href="index.php?page=needs">Besoins</a>
            <a class="<?= navActive('campaigns', $currentPage) ?>" href="index.php?page=campaigns">Campagnes</a>
            <a class="<?= navActive('centres', $currentPage) ?>" href="index.php?page=centres">Centres</a>
            <a class="<?= navActive('news', $currentPage) ?>" href="index.php?page=news">Actualités</a>
            <?php if (AuthController::isAuthenticated()): ?>
                <?php if (AuthController::hasRole('admin') || AuthController::hasRole('centre')): ?>
                    <a href="index.php?page=admin">Back office</a>
                <?php endif; ?>
                <a class="<?= navActive('reserve', $currentPage) ?>" href="index.php?page=reserve">Réserver</a>
                <a class="<?= navActive('dashboard', $currentPage) ?>" href="index.php?page=dashboard">Mon compte</a>
                <form class="inline-form" method="POST" action="index.php?page=logout">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-outline">Déconnexion</button>
                </form>
            <?php else: ?>
                <a class="<?= navActive('login', $currentPage) ?>" href="index.php?page=login">Connexion</a>
                <a class="btn btn-primary" href="index.php?page=register">Inscription</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<!-- Navbar End -->
