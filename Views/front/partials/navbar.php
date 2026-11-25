<header class="topnav">
    <div class="wrap topnav-inner">
        <a class="brand" href="<?= e(front('accueil.php')) ?>">
            <img src="<?= e(url('assets/img/logo.svg')) ?>" alt="" width="34" height="34">
            BloodLink
        </a>
        <button class="menu-toggle" type="button" aria-label="Menu" aria-expanded="false">&#9776;</button>
        <nav class="nav-links" aria-label="Navigation principale">
            <a class="<?= navActive('accueil.php') ?>" href="<?= e(front('accueil.php')) ?>">Accueil</a>
            <a class="<?= navActive('besoins.php') ?>" href="<?= e(front('besoins.php')) ?>">Besoins</a>
            <a class="<?= navActive('campagnes.php') ?>" href="<?= e(front('campagnes.php')) ?>">Campagnes</a>
            <a class="<?= navActive('centres.php') ?>" href="<?= e(front('centres.php')) ?>">Centres</a>
            <a class="<?= navActive('actualites.php') ?>" href="<?= e(front('actualites.php')) ?>">Actualités</a>
            <?php if (AuthC::isAuthenticated()): ?>
                <?php if (AuthC::hasRole('admin') || AuthC::hasRole('centre')): ?>
                    <a href="<?= e(back('tableau_de_bord.php')) ?>">Back office</a>
                <?php endif; ?>
                <a class="<?= navActive('reserver.php') ?>" href="<?= e(front('reserver.php')) ?>">Réserver</a>
                <a class="<?= navActive('mon_compte.php') ?>" href="<?= e(front('mon_compte.php')) ?>">Mon compte</a>
                <form class="inline-form" method="POST" action="<?= e(front('deconnexion.php')) ?>">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-outline">Déconnexion</button>
                </form>
            <?php else: ?>
                <a class="<?= navActive('connexion.php') ?>" href="<?= e(front('connexion.php')) ?>">Connexion</a>
                <a class="btn btn-primary" href="<?= e(front('inscription.php')) ?>">Inscription</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
