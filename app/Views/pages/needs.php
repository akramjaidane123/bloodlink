<?php /** Attend : $needs, $groups, $gouvernorats, $blood, $gov */ ?>
<section class="wrap section">
    <h1>Besoins en sang</h1>
    <form class="filters" method="GET" action="index.php">
        <input type="hidden" name="page" value="needs">
        <select name="blood" aria-label="Groupe sanguin">
            <option value="">Tous les groupes</option>
            <?php foreach ($groups as $g): ?><option value="<?= e($g) ?>" <?= $blood === $g ? 'selected' : '' ?>><?= e($g) ?></option><?php endforeach; ?>
        </select>
        <select name="gov" aria-label="Gouvernorat">
            <option value="">Toute la Tunisie</option>
            <?php foreach ($gouvernorats as $g): ?><option value="<?= (int) $g['id'] ?>" <?= $gov === (int) $g['id'] ? 'selected' : '' ?>><?= e($g['name']) ?></option><?php endforeach; ?>
        </select>
        <button class="btn btn-secondary" type="submit">Filtrer</button>
    </form>
    <?php if (!$needs): ?><p>Aucun besoin ne correspond à votre recherche.</p><?php endif; ?>
    <div class="grid"><?php foreach ($needs as $n) { require __DIR__ . '/../partials/need_card.php'; } ?></div>
</section>
