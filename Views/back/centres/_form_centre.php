<div class="card form-card">
    <h2><?= $mode === 'ajouter' ? 'Ajouter' : 'Modifier' ?> un centre</h2>
    <?= afficherErreurs($erreurs) ?>
    <form method="POST" novalidate data-validate>
        <?= csrf_field() ?>

        <label for="f_name">Nom *</label>
        <input type="text" id="f_name" name="name" value="<?= e($val['name'] ?? '') ?>" maxlength="120" required data-label="Nom">

        <label for="f_address">Adresse</label>
        <input type="text" id="f_address" name="address" value="<?= e($val['address'] ?? '') ?>" maxlength="255" data-label="Adresse">

        <label for="f_phone">Téléphone</label>
        <input type="tel" id="f_phone" name="phone" value="<?= e($val['phone'] ?? '') ?>" maxlength="30" data-label="Téléphone">

        <label for="f_status">Statut (validation) *</label>
        <select id="f_status" name="status" required data-label="Statut">
            <option value="">Choisir…</option>
            <?php foreach (labels()['centre'] as $k => $texte): ?>
            <option value="<?= e($k) ?>" <?= (string) ($val['status'] ?? '') === (string) $k ? 'selected' : '' ?>><?= e($texte) ?></option>
            <?php endforeach; ?>
        </select>

        <label for="f_gouvernorat_id">Gouvernorat *</label>
        <select id="f_gouvernorat_id" name="gouvernorat_id" required data-label="Gouvernorat">
            <option value="">Choisir…</option>
            <?php foreach ($options['gouvernorat_id'] as $o): ?>
            <option value="<?= (int) $o['id'] ?>" <?= (string) ($val['gouvernorat_id'] ?? '') === (string) $o['id'] ? 'selected' : '' ?>><?= e($o['label']) ?></option>
            <?php endforeach; ?>
        </select>

        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Enregistrer</button>
            <a class="btn" href="liste_centres.php">Annuler</a>
        </div>
    </form>
</div>
