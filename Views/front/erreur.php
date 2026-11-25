<?php

if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../../init.php';
    http_response_code(404);
}
$codeErreur  = $codeErreur  ?? 404;
$titreErreur = $titreErreur ?? 'Page introuvable';
$texteErreur = $texteErreur ?? "Cette page n'existe pas ou a été déplacée.";

$titre = $titreErreur . ' | BloodLink';
require_once __DIR__ . '/partials/head.php';
require_once __DIR__ . '/partials/spinner.php';
require_once __DIR__ . '/partials/navbar.php';
?>
<main class="site-main">
    <div class="auth-wrap">
        <div class="card auth-card text-center">
            <p class="error-code"><?= (int) $codeErreur ?></p>
            <h1><?= e($titreErreur) ?></h1>
            <p><?= e($texteErreur) ?></p>
            <a class="btn btn-primary" href="<?= e(front('accueil.php')) ?>">Retour à l'accueil</a>
        </div>
    </div>
</main>
<?php
require_once __DIR__ . '/partials/footer.php';
require_once __DIR__ . '/partials/scripts.php';
