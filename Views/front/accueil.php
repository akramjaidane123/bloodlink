<?php
require_once __DIR__ . '/../../init.php';
require_once __DIR__ . '/../../Controllers/NeedC.php';
require_once __DIR__ . '/../../Controllers/CampaignC.php';
require_once __DIR__ . '/../../Controllers/ArticleC.php';

$besoins   = (new NeedC())->besoinsFiltres(null, null, 6);
$campagnes = (new CampaignC())->campagnesAVenir(3);
$articles  = (new ArticleC())->derniersArticles(3);

$titre = 'BloodLink | Relie les donneurs. Sauve des vies.';
require_once __DIR__ . '/partials/head.php';
require_once __DIR__ . '/partials/spinner.php';
require_once __DIR__ . '/partials/navbar.php';
?>
<main class="site-main">
    <section class="hero">
        <div class="wrap hero-inner">
            <div>
                <h1>Donnez votre sang, sauvez des vies</h1>
                <p>Consultez les besoins des centres de collecte et réservez votre don en quelques clics.</p>
                <a class="btn btn-primary" href="<?= e(front('reserver.php')) ?>">Je veux donner</a>
                <a class="btn btn-secondary" href="<?= e(front('besoins.php')) ?>">Voir les besoins</a>
            </div>
            <img class="hero-drop" src="<?= e(url('assets/img/logo.svg')) ?>" width="170" height="170" alt="Logo BloodLink : goutte, croix et plume">
        </div>
    </section>

    <section class="wrap section">
        <h2>Besoins les plus urgents</h2>
        <div class="grid">
            <?php foreach ($besoins as $n) { require __DIR__ . '/partials/carte_besoin.php'; } ?>
        </div>
        <?php if (!$besoins): ?><p class="muted">Aucun besoin publié pour le moment.</p><?php endif; ?>
        <p><a href="<?= e(front('besoins.php')) ?>">Voir tous les besoins &rarr;</a></p>
    </section>

    <section class="wrap section">
        <h2>Prochaines campagnes</h2>
        <div class="grid">
            <?php foreach ($campagnes as $c) { require __DIR__ . '/partials/carte_campagne.php'; } ?>
        </div>
        <?php if (!$campagnes): ?><p class="muted">Aucune campagne à venir.</p><?php endif; ?>
    </section>

    <section class="wrap section">
        <h2>Actualités</h2>
        <div class="grid">
            <?php foreach ($articles as $a): ?>
                <article class="card">
                    <span class="badge badge-user"><?= e($a['category']) ?></span>
                    <h3><a href="<?= e(front('article.php?id=' . (int) $a['id'])) ?>"><?= e($a['title']) ?></a></h3>
                    <p class="muted"><?= e($a['excerpt']) ?>…</p>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
</main>
<?php
require_once __DIR__ . '/partials/footer.php';
require_once __DIR__ . '/partials/scripts.php';
