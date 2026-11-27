<div class="card form-card">
    <h2><?= $mode === 'ajouter' ? 'Ajouter' : 'Modifier' ?> une notification</h2>
    <?= afficherErreurs($erreurs) ?>
    <form method="POST" novalidate data-validate>
        <?= csrf_field() ?>

        <label for="f_title">Titre *</label>
        <input type="text" id="f_title" name="title" value="<?= e($val['title'] ?? '') ?>" maxlength="150" required data-label="Titre">

        <label for="f_message">Message (envoyé à tous les donneurs) *</label>
        <textarea id="f_message" name="message" rows="5" maxlength="5000" required data-label="Message"><?= e($val['message'] ?? '') ?></textarea>

        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Enregistrer</button>
            <a class="btn" href="liste_notifications.php">Annuler</a>
        </div>
    </form>
</div>
