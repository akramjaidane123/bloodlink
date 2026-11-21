<?php /** Expects: $errors (array), $registered (bool) */ ?>
<div class="auth-wrap">
    <div class="card auth-card">
        <h1>Connexion</h1>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <?php foreach ($errors as $error): ?><div><?= e($error) ?></div><?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($registered)): ?>
            <div class="alert alert-success">Compte créé avec succès, vous pouvez maintenant vous connecter.</div>
        <?php endif; ?>

        <form method="POST" action="index.php?page=login">
            <?= csrf_field() ?>
            <label for="email">Adresse e-mail</label>
            <input type="email" id="email" name="email" required autocomplete="email">

            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" required autocomplete="current-password">

            <button type="submit" class="btn btn-primary btn-block">Se connecter</button>
        </form>

        <p class="switch-link">Pas encore de compte ? <a href="index.php?page=register">S'inscrire</a></p>
    </div>
</div>
