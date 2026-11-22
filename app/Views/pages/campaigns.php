<?php /** Attend : $campaigns */ ?>
<section class="wrap section">
    <h1>Campagnes de collecte</h1>
    <?php if (!$campaigns): ?><p>Aucune campagne à venir pour le moment.</p><?php endif; ?>
    <div class="grid"><?php foreach ($campaigns as $c) { require __DIR__ . '/../partials/campaign_card.php'; } ?></div>
</section>
