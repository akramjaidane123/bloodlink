<div class="card form-card">
    <h2><?= $mode === 'ajouter' ? 'Ajouter' : 'Modifier' ?> une campagne</h2>
    <?= afficherErreurs($erreurs) ?>
    <form method="POST" novalidate data-validate>
        <?= csrf_field() ?>

        <label for="f_title">Titre *</label>
        <input type="text" id="f_title" name="title" value="<?= e($val['title'] ?? '') ?>" maxlength="150" required data-label="Titre">

        <label for="f_description">Description</label>
        <textarea id="f_description" name="description" rows="5" maxlength="5000" data-label="Description"><?= e($val['description'] ?? '') ?></textarea>

        <label for="f_location">Lieu</label>
        <input type="text" id="f_location" name="location" value="<?= e($val['location'] ?? '') ?>" maxlength="150" data-label="Lieu">

        <label for="f_start_date">Date de début *</label>
        <input type="date" id="f_start_date" name="start_date" value="<?= e($val['start_date'] ?? '') ?>" required data-label="Date de début">

        <label for="f_end_date">Date de fin *</label>
        <input type="date" id="f_end_date" name="end_date" value="<?= e($val['end_date'] ?? '') ?>" required data-label="Date de fin">

        <?php if ($scope === null): ?>
        <label for="f_centre_id">Centre *</label>
        <select id="f_centre_id" name="centre_id" required data-label="Centre">
            <option value="">Choisir…</option>
            <?php foreach ($options['centre_id'] as $o): ?>
            <option value="<?= (int) $o['id'] ?>" <?= (string) ($val['centre_id'] ?? '') === (string) $o['id'] ? 'selected' : '' ?>><?= e($o['label']) ?></option>
            <?php endforeach; ?>
        </select>
        <?php else: ?>
        <p class="muted">Centre : votre centre (rattachement automatique).</p>
        <?php endif; ?>

        <label for="f_sponsor_id">Sponsor</label>
        <select id="f_sponsor_id" name="sponsor_id" data-label="Sponsor">
            <option value="">— Aucun —</option>
            <?php foreach ($options['sponsor_id'] as $o): ?>
            <option value="<?= (int) $o['id'] ?>" <?= (string) ($val['sponsor_id'] ?? '') === (string) $o['id'] ? 'selected' : '' ?>><?= e($o['label']) ?></option>
            <?php endforeach; ?>
        </select>

        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Enregistrer</button>
            <a class="btn" href="liste_campagnes.php">Annuler</a>
        </div>
    </form>
</div>
