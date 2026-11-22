<?php /** Carte d'une campagne. Attend : $c */ ?>
<article class="card">
    <h3><?= e($c['title']) ?></h3>
    <p class="muted"><?= dateFr($c['start_date']) ?><?= $c['end_date'] !== $c['start_date'] ? ' au ' . dateFr($c['end_date']) : '' ?></p>
    <p><?= e($c['description']) ?></p>
    <p class="muted"><?= e($c['centre']) ?><?= $c['location'] ? ' · ' . e($c['location']) : '' ?></p>
    <?php if (!empty($c['sponsor'])): ?><p><span class="badge badge-user">Parrainée par <?= e($c['sponsor']) ?></span></p><?php endif; ?>
    <a class="btn btn-secondary" href="index.php?page=reserve&amp;centre=<?= (int) $c['centre_id'] ?>">Je participe</a>
</article>
