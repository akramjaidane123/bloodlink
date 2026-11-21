<?php
$currentPage = $currentPage ?? 'admin';
$role = $_SESSION['role'] ?? '';
$menu = ['admin' => ['Tableau de bord', '&#9632;']];
foreach (entities() as $key => $cfg) {
    if ($cfg['access'] === 'staff' || $role === 'admin') {
        $menu['admin-' . $key] = [$cfg['label'], $cfg['icon']];
    }
}
if ($role === 'admin') {
    $menu['admin-users'] = ['Comptes', '&#128101;'];
}
?>
<!-- Sidebar Start -->
<aside class="sidebar">
    <div class="brand"><span class="dot"></span> BloodLink Admin</div>
    <nav>
        <?php foreach ($menu as $key => [$label, $icon]): ?>
            <a href="index.php?page=<?= e($key) ?>" class="<?= navActive($key, $currentPage) ?>"><span class="icon"><?= $icon ?></span> <?= e($label) ?></a>
        <?php endforeach; ?>
        <a href="index.php?page=home"><span class="icon">&#8617;</span> Retour au site</a>
    </nav>
    <div class="sidebar-footer">
        <div class="who"><?= e($_SESSION['username'] ?? '') ?></div>
        <div class="role-tag"><?= $role === 'admin' ? 'Administrateur' : 'Personnel de centre' ?></div>
        <form method="POST" action="index.php?page=logout"><?= csrf_field() ?><button type="submit" class="logout">Déconnexion</button></form>
    </div>
</aside>
<!-- Sidebar End -->
