<?php /** Attend : $articles */ ?>
<section class="wrap section">
    <h1>Actualités</h1>
    <div class="grid">
        <?php foreach ($articles as $a): ?>
            <article class="card"><span class="badge badge-user"><?= e($a['category']) ?></span>
                <h3><a href="index.php?page=article&amp;id=<?= (int) $a['id'] ?>"><?= e($a['title']) ?></a></h3>
                <p class="muted"><?= dateFr($a['created_at']) ?></p><p><?= e($a['excerpt']) ?>…</p></article>
        <?php endforeach; ?>
    </div>
</section>
