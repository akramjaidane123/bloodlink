<?php
declare(strict_types=1);

// 1. Secure session configuration (ini_set BEFORE session_start)
ini_set('session.use_strict_mode', '1');
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Lax');
// ini_set('session.cookie_secure', '1'); // uncomment when served over HTTPS
session_start();

// 2. Session expiration after 30 minutes of inactivity
$sessionLifetime = 1800;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $sessionLifetime)) {
    $_SESSION = [];
    session_destroy();
    session_start();
}
$_SESSION['last_activity'] = time();

// 3. HTTP security headers
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header("Content-Security-Policy: default-src 'self'; "
    . "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net; "
    . "font-src 'self' https://fonts.gstatic.com https://cdn.jsdelivr.net; "
    . "script-src 'self' https://cdn.jsdelivr.net https://code.jquery.com; "
    . "img-src 'self' data:");

// 4. Load the application
$app = __DIR__ . '/../app';
require_once $app . '/config/Database.php';
require_once $app . '/helpers.php';
require_once $app . '/Models/Model.php';
foreach (glob($app . '/Models/*.php') as $file) {
    require_once $file;
}
require_once $app . '/Controllers/Controller.php';
require_once $app . '/Controllers/CrudController.php';
foreach (glob($app . '/Controllers/*Controller.php') as $file) {
    require_once $file;
}

// 5. Routing:  index.php?page=xxx
$routes = require $app . '/routes.php';
$page   = (string) ($_GET['page'] ?? 'home');

if (!isset($routes[$page])) {
    (new ErrorController())->notFound();
    exit;
}

[$class, $method, $access] = $routes[$page];

switch ($access) {
    case 'guest': // login / register: already logged in -> go to the right space
        if (AuthController::isAuthenticated()) {
            redirect(homeFor((string) ($_SESSION['role'] ?? '')));
        }
        break;
    case 'auth':
        AuthController::requireAuth();
        break;
    case 'staff':
        AuthController::requireStaff(); // administrateur ou personnel de centre
        break;
    case 'admin':
        AuthController::requireRole('admin'); // 403 for normal users
        break;
}

try {
    (new $class())->$method();
} catch (Throwable $e) {
    error_log($e->getMessage());
    (new ErrorController())->serverError();
}
