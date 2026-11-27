<?php
require_once __DIR__ . '/../../init.php';
require_once __DIR__ . '/../../Controllers/ArticleC.php';

$articles = (new ArticleC())->derniersArticles();

$titre = 'Actualités | BloodLink';
require_once __DIR__ . '/partials/head.php';
require_once __DIR__ . '/partials/spinner.php';
require_once __DIR__ . '/partials/navbar.php';
?>
<main class="site-main">
    <section class="wrap section">
        <h1>Actualités</h1>
        <div class="grid">
            <?php foreach ($articles as $a): ?>
                <article class="card">
                    <span class="badge badge-user"><?= e($a['category']) ?></span>
                    <h3><a href="<?= e(front('article.php?id=' . (int) $a['id'])) ?>"><?= e($a['title']) ?></a></h3>
                    <p class="muted"><?= e(dateFr($a['created_at'])) ?></p>
                    <p><?= e($a['excerpt']) ?>…</p>
                </article>
            <?php endforeach; ?>
        </div>
        <?php if (!$articles): ?><p>Aucun article pour le moment.</p><?php endif; ?>
    </section>
</main>
<?php
require_once __DIR__ . '/partials/footer.php';
require_once __DIR__ . '/partials/scripts.php';
