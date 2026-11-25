<?php

class config
{
    private static $pdo = null;

    public static function getConnexion()
    {
        if (self::$pdo === null) {
            $p = [
                'host' => 'localhost',
                'port' => '3306',
                'name' => 'bloodlink',
                'user' => 'root',
                'pass' => '',
            ];
            if (is_file(__DIR__ . '/config.local.php')) {
                $p = array_merge($p, require __DIR__ . '/config.local.php');
            }

            try {
                self::$pdo = new PDO(
                    'mysql:host=' . $p['host'] . ';port=' . $p['port'] . ';dbname=' . $p['name'] . ';charset=utf8mb4',
                    $p['user'],
                    $p['pass'],
                    [
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES   => false,
                    ]
                );
            } catch (Exception $e) {
                error_log('Connexion BD impossible : ' . $e->getMessage());
                http_response_code(500);
                die('Base de données indisponible. Importez database/bloodlink.sql dans MySQL '
                    . 'et vérifiez les paramètres de config.php.');
            }
        }
        return self::$pdo;
    }
}
