<div class="card form-card">
    <h2><?= $mode === 'ajouter' ? 'Ajouter' : 'Modifier' ?> un besoin en sang</h2>
    <?= afficherErreurs($erreurs) ?>
    <form method="POST" novalidate data-validate>
        <?= csrf_field() ?>

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

        <label for="f_blood_group">Groupe sanguin *</label>
        <select id="f_blood_group" name="blood_group" required data-label="Groupe sanguin">
            <option value="">Choisir…</option>
            <?php foreach (labels()['blood'] as $k => $texte): ?>
            <option value="<?= e($k) ?>" <?= (string) ($val['blood_group'] ?? '') === (string) $k ? 'selected' : '' ?>><?= e($texte) ?></option>
            <?php endforeach; ?>
        </select>

        <label for="f_urgency">Niveau d'urgence *</label>
        <select id="f_urgency" name="urgency" required data-label="Niveau d'urgence">
            <option value="">Choisir…</option>
            <?php foreach (labels()['urgency'] as $k => $texte): ?>
            <option value="<?= e($k) ?>" <?= (string) ($val['urgency'] ?? '') === (string) $k ? 'selected' : '' ?>><?= e($texte) ?></option>
            <?php endforeach; ?>
        </select>

        <label for="f_quantity">Poches nécessaires *</label>
        <input type="number" id="f_quantity" name="quantity" value="<?= e($val['quantity'] ?? '') ?>" min="0" step="1" required data-label="Poches nécessaires">

        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Enregistrer</button>
            <a class="btn" href="liste_besoins.php">Annuler</a>
        </div>
    </form>
</div>
