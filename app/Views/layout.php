<?php
/**
 * Front office layout - assembles the shared blocks and injects the page content.
 * Variables (from Controller::render): $title, $currentPage, $content, $extraScripts
 */
require_once __DIR__ . '/partials/head.php';
require_once __DIR__ . '/partials/spinner.php';
require_once __DIR__ . '/partials/navbar.php';
?>
<main class="site-main">
    <?= $content ?>
</main>
<?php
require_once __DIR__ . '/partials/footer.php';
require_once __DIR__ . '/partials/scripts.php';
