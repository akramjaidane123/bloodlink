<?php
/**
 * Back office layout (admin only).
 * Variables: $title, $currentPage, $content, $extraScripts, $user (UserModel)
 */
require_once __DIR__ . '/partials/admin/head.php';
require_once __DIR__ . '/partials/admin/sidebar.php';
?>
<div class="main">
    <?php require_once __DIR__ . '/partials/admin/header.php'; ?>
    <div class="content">
        <?= $content ?>
    </div>
    <?php require_once __DIR__ . '/partials/admin/footer.php'; ?>
</div>
<?php
require_once __DIR__ . '/partials/admin/scripts.php';
