<?php /** Attend : $code, $heading, $text */ ?>
<div class="auth-wrap">
    <div class="card auth-card text-center">
        <p class="error-code"><?= e($code) ?></p>
        <h1><?= e($heading) ?></h1>
        <p><?= e($text) ?></p>
        <a class="btn btn-primary" href="index.php?page=home">Retour à l'accueil</a>
    </div>
</div>
