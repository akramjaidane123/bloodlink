<?php
/**
 * app/routes.php : page => [Contrôleur, méthode, accès]
 * accès : public | guest (visiteur non connecté) | auth (connecté)
 *         staff (admin + centre) | admin (administrateur seul, 403 sinon)
 * Pour ajouter une page : contrôleur + vue, puis une ligne ici.
 * Les pages CRUD du back office sont générées depuis app/config/entities.php.
 */
require_once __DIR__ . '/helpers.php';

$routes = [
    // --- Front Office ---
    'home'        => ['HomeController',        'index',   'public'],
    'needs'       => ['NeedController',        'index',   'public'],
    'campaigns'   => ['CampaignController',    'index',   'public'],
    'centres'     => ['CentreController',      'index',   'public'],
    'news'        => ['ArticleController',     'index',   'public'],
    'article'     => ['ArticleController',     'show',    'public'],
    'reserve'     => ['ReservationController', 'create',  'auth'],
    'reserve-cancel' => ['ReservationController', 'cancel', 'auth'],
    'login'       => ['AuthController',        'login',   'guest'],
    'register'    => ['AuthController',        'register','guest'],
    'logout'      => ['AuthController',        'logout',  'auth'],
    'dashboard'   => ['DashboardController',   'index',   'auth'],
    // --- Back Office ---
    'admin'       => ['AdminController',       'index',   'staff'],
    'admin-users' => ['AdminController',       'users',   'admin'],
];
foreach (entities() as $key => $cfg) {
    $routes['admin-' . $key] = ['Admin' . ucfirst($key) . 'Controller', 'index', $cfg['access']];
}
return $routes;
