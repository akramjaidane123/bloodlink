<?php
require_once __DIR__ . '/../../init.php';
require_once __DIR__ . '/../../Controllers/CampaignC.php';

$campagnes = (new CampaignC())->campagnesAVenir();

$titre = 'Campagnes de collecte | BloodLink';
require_once __DIR__ . '/partials/head.php';
require_once __DIR__ . '/partials/spinner.php';
require_once __DIR__ . '/partials/navbar.php';
?>
<main class="site-main">
    <section class="wrap section">
        <h1>Campagnes de collecte</h1>
        <?php if (!$campagnes): ?><p>Aucune campagne à venir pour le moment.</p><?php endif; ?>
        <div class="grid">
            <?php foreach ($campagnes as $c) { require __DIR__ . '/partials/carte_campagne.php'; } ?>
        </div>
    </section>
</main>
<?php
require_once __DIR__ . '/partials/footer.php';
require_once __DIR__ . '/partials/scripts.php';
