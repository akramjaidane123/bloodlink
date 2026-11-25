<?php /** Carte d'un besoin. Attend : $n (ligne de Need::filtered) */ ?>
<article class="card need-card" data-search="<?= e(strtolower($n['centre'] . ' ' . $n['gouvernorat'])) ?>" data-blood="<?= e($n['blood_group']) ?>">
    <div class="badge-blood"><?= e($n['blood_group']) ?></div>
    <h3><?= e($n['centre']) ?></h3>
    <p class="muted"><?= e($n['gouvernorat']) ?> · <?= (int) $n['quantity'] ?> poches</p>
    <p><?= badge('urgency', $n['urgency']) ?></p>
    <a class="btn btn-secondary" href="index.php?page=reserve&amp;centre=<?= (int) $n['centre_id'] ?>">Réserver un don</a>
</article>
