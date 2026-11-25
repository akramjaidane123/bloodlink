<?php

$role = $_SESSION['role'] ?? '';
$menuBack = [
    ['centres', 'Centres', '&#127973;', 'admin', 'Views/back/centres/liste_centres.php'],
    ['gouvernorats', 'Gouvernorats', '&#127757;', 'admin', 'Views/back/gouvernorats/liste_gouvernorats.php'],
    ['besoins', 'Besoins en sang', '&#128137;', 'staff', 'Views/back/besoins/liste_besoins.php'],
    ['campagnes', 'Campagnes', '&#128226;', 'staff', 'Views/back/campagnes/liste_campagnes.php'],
    ['sponsors', 'Sponsors', '&#129309;', 'admin', 'Views/back/sponsors/liste_sponsors.php'],
    ['contrats', 'Contrats', '&#128196;', 'admin', 'Views/back/contrats/liste_contrats.php'],
    ['categories', 'Catégories', '&#127991;', 'admin', 'Views/back/categories/liste_categories.php'],
    ['articles', 'Articles', '&#128240;', 'admin', 'Views/back/articles/liste_articles.php'],
    ['commentaires', 'Commentaires', '&#128172;', 'admin', 'Views/back/commentaires/liste_commentaires.php'],
    ['reservations', 'Réservations', '&#128197;', 'staff', 'Views/back/reservations/liste_reservations.php'],
    ['notifications', 'Notifications', '&#128276;', 'admin', 'Views/back/notifications/liste_notifications.php'],
    ['utilisateurs', 'Comptes utilisateurs', '&#128101;', 'admin', 'Views/back/utilisateurs/liste_utilisateurs.php'],
];
?>

<aside class="sidebar">
    <div class="brand"><span class="dot"></span> BloodLink Admin</div>
    <nav>
        <a href="<?= e(back('tableau_de_bord.php')) ?>" class="<?= navActive('tableau_de_bord.php') ?>"><span class="icon">&#9632;</span> Tableau de bord</a>
        <?php foreach ($menuBack as [$dossier, $libelle, $icone, $acces, $page]): ?>
            <?php if ($acces === 'staff' || $role === 'admin'): ?>
                <a href="<?= e(url($page)) ?>" class="<?= navActiveBack($dossier) ?>"><span class="icon"><?= $icone ?></span> <?= e($libelle) ?></a>
            <?php endif; ?>
        <?php endforeach; ?>
        <a href="<?= e(front('accueil.php')) ?>"><span class="icon">&#8617;</span> Retour au site</a>
    </nav>
    <div class="sidebar-footer">
        <div class="who"><?= e($_SESSION['username'] ?? '') ?></div>
        <div class="role-tag"><?= e(labels()['role'][$role] ?? '') ?></div>
        <form method="POST" action="<?= e(front('deconnexion.php')) ?>">
            <?= csrf_field() ?>
            <button type="submit" class="logout">Déconnexion</button>
        </form>
    </div>
</aside>
