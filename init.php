<?php

ini_set('session.use_strict_mode', '1');
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Lax');

session_start();

$dureeSession = 1800;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $dureeSession)) {
    $_SESSION = [];
    session_destroy();
    session_start();
}
$_SESSION['last_activity'] = time();

header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header("Content-Security-Policy: default-src 'self'; style-src 'self' 'unsafe-inline'; "
    . "script-src 'self'; img-src 'self' data:; connect-src 'self'; form-action 'self'; "
    . "base-uri 'self'; frame-ancestors 'none'");

$script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$pos = strpos($script, '/Views/');
define('BASE_URL', $pos !== false ? substr($script, 0, $pos) : rtrim(dirname($script), '/\\'));

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/Controllers/AuthC.php';
require_once __DIR__ . '/helpers.php';
