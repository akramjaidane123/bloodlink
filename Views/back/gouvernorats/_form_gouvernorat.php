<div class="card form-card">
    <h2><?= $mode === 'ajouter' ? 'Ajouter' : 'Modifier' ?> un gouvernorat</h2>
    <?= afficherErreurs($erreurs) ?>
    <form method="POST" novalidate data-validate>
        <?= csrf_field() ?>

        <label for="f_name">Nom *</label>
        <input type="text" id="f_name" name="name" value="<?= e($val['name'] ?? '') ?>" maxlength="80" required data-label="Nom">

        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Enregistrer</button>
            <a class="btn" href="liste_gouvernorats.php">Annuler</a>
        </div>
    </form>
</div>
