<?php
require_once __DIR__ . '/../../init.php';
require_once __DIR__ . '/../../Controllers/NeedC.php';
require_once __DIR__ . '/../../Controllers/GouvernoratC.php';

$groupes = array_keys(labels()['blood']);
$groupe  = in_array($_GET['blood'] ?? '', $groupes, true) ? $_GET['blood'] : null;
$gov     = (int) ($_GET['gov'] ?? 0) ?: null;

$besoins      = (new NeedC())->besoinsFiltres($groupe, $gov);
$gouvernorats = (new GouvernoratC())->options();

$titre = 'Besoins en sang | BloodLink';
require_once __DIR__ . '/partials/head.php';
require_once __DIR__ . '/partials/spinner.php';
require_once __DIR__ . '/partials/navbar.php';
?>
<main class="site-main">
    <section class="wrap section">
        <h1>Besoins en sang</h1>

        <form class="filters" id="form-besoins" method="GET" action="besoins.php" data-api="api_besoins.php">
            <select name="blood" aria-label="Groupe sanguin">
                <option value="">Tous les groupes</option>
                <?php foreach ($groupes as $g): ?>
                    <option value="<?= e($g) ?>" <?= $groupe === $g ? 'selected' : '' ?>><?= e($g) ?></option>
                <?php endforeach; ?>
            </select>
            <select name="gov" aria-label="Gouvernorat">
                <option value="">Toute la Tunisie</option>
                <?php foreach ($gouvernorats as $g): ?>
                    <option value="<?= (int) $g['id'] ?>" <?= $gov === (int) $g['id'] ? 'selected' : '' ?>><?= e($g['label']) ?></option>
                <?php endforeach; ?>
            </select>
            <button class="btn btn-secondary" type="submit">Filtrer</button>
        </form>

        <p id="info-besoins" class="muted" role="status" aria-live="polite"><?= count($besoins) ?> résultat(s)</p>
        <div class="grid" id="liste-besoins">
            <?php foreach ($besoins as $n) { require __DIR__ . '/partials/carte_besoin.php'; } ?>
        </div>
    </section>
</main>
<?php
require_once __DIR__ . '/partials/footer.php';
require_once __DIR__ . '/partials/scripts.php';
