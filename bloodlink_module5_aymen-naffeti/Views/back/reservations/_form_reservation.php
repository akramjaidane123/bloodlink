<div class="card contexte">
        <p class="muted">Donneur : <strong><?= e($row['donor'] ?? '') ?></strong> · Centre : <strong><?= e($row['centre'] ?? '') ?></strong></p>
    </div>
<div class="card form-card">
    <h2><?= $mode === 'ajouter' ? 'Ajouter' : 'Modifier' ?> une réservation</h2>
    <?= afficherErreurs($erreurs) ?>
    <form method="POST" novalidate data-validate>
        <?= csrf_field() ?>

        <label for="f_donation_date">Date du don *</label>
        <input type="date" id="f_donation_date" name="donation_date" value="<?= e($val['donation_date'] ?? '') ?>" required data-label="Date du don">

        <label for="f_status">Statut *</label>
        <select id="f_status" name="status" required data-label="Statut">
            <option value="">Choisir…</option>
            <?php foreach (labels()['reservation'] as $k => $texte): ?>
            <option value="<?= e($k) ?>" <?= (string) ($val['status'] ?? '') === (string) $k ? 'selected' : '' ?>><?= e($texte) ?></option>
            <?php endforeach; ?>
        </select>

        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Enregistrer</button>
            <a class="btn" href="liste_reservations.php">Annuler</a>
        </div>
    </form>
</div>
