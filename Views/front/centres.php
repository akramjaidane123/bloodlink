<?php
require_once __DIR__ . '/../../init.php';
require_once __DIR__ . '/../../Controllers/CentreC.php';
require_once __DIR__ . '/../../Controllers/GouvernoratC.php';

$gov          = (int) ($_GET['gov'] ?? 0) ?: null;
$centres      = (new CentreC())->centresValides($gov);
$gouvernorats = (new GouvernoratC())->options();

$titre = 'Centres de collecte | BloodLink';
require_once __DIR__ . '/partials/head.php';
require_once __DIR__ . '/partials/spinner.php';
require_once __DIR__ . '/partials/navbar.php';
?>
<main class="site-main">
    <section class="wrap section">
        <h1>Centres de collecte</h1>
        <form class="filters" method="GET" action="centres.php">
            <select name="gov" aria-label="Gouvernorat">
                <option value="">Toute la Tunisie</option>
                <?php foreach ($gouvernorats as $g): ?>
                    <option value="<?= (int) $g['id'] ?>" <?= $gov === (int) $g['id'] ? 'selected' : '' ?>><?= e($g['label']) ?></option>
                <?php endforeach; ?>
            </select>
            <button class="btn btn-secondary" type="submit">Filtrer</button>
        </form>
        <div class="grid">
            <?php foreach ($centres as $c): ?>
                <article class="card need-card">
                    <div class="badge-blood">+</div>
                    <h3><?= e($c['name']) ?></h3>
                    <p class="muted">
                        <?= e($c['gouvernorat']) ?><?= $c['address'] ? ' · ' . e($c['address']) : '' ?>
                        <?= $c['phone'] ? '<br>Tél. ' . e($c['phone']) : '' ?>
                    </p>
                    <a href="https://www.openstreetmap.org/search?query=<?= urlencode($c['name'] . ' ' . $c['address']) ?>" target="_blank" rel="noopener noreferrer">Voir sur la carte</a>
                    <p><a class="btn btn-secondary" href="<?= e(front('reserver.php?centre=' . (int) $c['id'])) ?>">Réserver</a></p>
                </article>
            <?php endforeach; ?>
        </div>
        <?php if (!$centres): ?><p>Aucun centre dans ce gouvernorat.</p><?php endif; ?>
    </section>
</main>
<?php
require_once __DIR__ . '/partials/footer.php';
require_once __DIR__ . '/partials/scripts.php';
