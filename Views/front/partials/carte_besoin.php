<article class="card need-card" data-blood="<?= e($n['blood_group']) ?>">
    <div class="badge-blood"><?= e($n['blood_group']) ?></div>
    <h3><?= e($n['centre']) ?></h3>
    <p class="muted"><?= e($n['gouvernorat']) ?> · <?= (int) $n['quantity'] ?> poches</p>
    <p><?= badge('urgency', $n['urgency']) ?></p>
    <a class="btn btn-secondary" href="<?= e(front('reserver.php?centre=' . (int) $n['centre_id'])) ?>">Réserver un don</a>
</article>
