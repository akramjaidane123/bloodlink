<?php /** Expects: $errors (array), $old (array of previous input) */ $old = $old ?? []; ?>
<div class="auth-wrap">
    <div class="card auth-card">
        <h1>Créer un compte</h1>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <?php foreach ($errors as $error): ?><div><?= e($error) ?></div><?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="index.php?page=register">
            <?= csrf_field() ?>
            <label for="username">Nom d'utilisateur</label>
            <input type="text" id="username" name="username" required value="<?= e($old['username'] ?? '') ?>">

            <label for="email">Adresse e-mail</label>
            <input type="email" id="email" name="email" required value="<?= e($old['email'] ?? '') ?>">

            <label for="password">Mot de passe (min. 8 caractères)</label>
            <input type="password" id="password" name="password" required autocomplete="new-password">

            <label for="password_confirm">Confirmer le mot de passe</label>
            <input type="password" id="password_confirm" name="password_confirm" required autocomplete="new-password">

            <button type="submit" class="btn btn-primary btn-block">Créer mon compte</button>
        </form>

        <p class="switch-link">Déjà un compte ? <a href="index.php?page=login">Se connecter</a></p>
    </div>
</div>
