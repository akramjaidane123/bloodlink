<?php /** Attend : $needs, $campaigns, $articles, $dbError */ ?>
<section class="hero">
    <div class="wrap hero-inner">
        <div>
            <h1>Donnez votre sang, sauvez des vies</h1>
            <p>Consultez les besoins des centres de collecte et réservez votre don en quelques clics.</p>
            <a class="btn btn-primary" href="index.php?page=reserve">Je veux donner</a>
            <a class="btn btn-secondary" href="index.php?page=needs">Voir les besoins</a>
        </div>
        <img class="hero-drop" src="assets/img/logo.svg" width="170" height="170" alt="Logo BloodLink : goutte, croix et plume">
    </div>
</section>

<?php if ($dbError): ?>
<div class="wrap section"><div class="alert alert-error">Base de données indisponible : importez <code>database/bloodlink.sql</code> et vérifiez <code>app/config/config.php</code>.</div></div>
<?php else: ?>
<section class="wrap section">
    <h2>Besoins les plus urgents</h2>
    <div class="grid"><?php foreach ($needs as $n) { require __DIR__ . '/../partials/need_card.php'; } ?></div>
    <p><a href="index.php?page=needs">Voir tous les besoins &rarr;</a></p>
</section>
<section class="wrap section">
    <h2>Prochaines campagnes</h2>
    <div class="grid"><?php foreach ($campaigns as $c) { require __DIR__ . '/../partials/campaign_card.php'; } ?></div>
</section>
<section class="wrap section">
    <h2>Actualités</h2>
    <div class="grid">
        <?php foreach ($articles as $a): ?>
            <article class="card"><span class="badge badge-user"><?= e($a['category']) ?></span>
                <h3><a href="index.php?page=article&amp;id=<?= (int) $a['id'] ?>"><?= e($a['title']) ?></a></h3>
                <p class="muted"><?= e($a['excerpt']) ?>…</p></article>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>
