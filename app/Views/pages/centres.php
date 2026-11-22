<?php /** Attend : $centres, $gouvernorats, $gov */ ?>
<section class="wrap section">
    <h1>Centres de collecte</h1>
    <form class="filters" method="GET" action="index.php">
        <input type="hidden" name="page" value="centres">
        <select name="gov" aria-label="Gouvernorat">
            <option value="">Toute la Tunisie</option>
            <?php foreach ($gouvernorats as $g): ?><option value="<?= (int) $g['id'] ?>" <?= $gov === (int) $g['id'] ? 'selected' : '' ?>><?= e($g['name']) ?></option><?php endforeach; ?>
        </select>
        <button class="btn btn-secondary" type="submit">Filtrer</button>
    </form>
    <div class="grid">
        <?php foreach ($centres as $c): ?>
            <article class="card need-card">
                <div class="badge-blood">+</div>
                <h3><?= e($c['name']) ?></h3>
                <p class="muted"><?= e($c['gouvernorat']) ?><?= $c['address'] ? ' · ' . e($c['address']) : '' ?><?= $c['phone'] ? '<br>Tél. ' . e($c['phone']) : '' ?></p>
                <a href="https://www.openstreetmap.org/search?query=<?= urlencode($c['name'] . ' ' . $c['address']) ?>" target="_blank" rel="noopener noreferrer">Voir sur la carte</a>
                <p><a class="btn btn-secondary" href="index.php?page=reserve&amp;centre=<?= (int) $c['id'] ?>">Réserver</a></p>
            </article>
        <?php endforeach; ?>
    </div>
</section>
