<?php
/**
 * app/config/Database.php
 * Connexion PDO sécurisée (Singleton).
 * Lit app/config/config.php s'il existe, sinon app/config/config.example.php.
 * En cas d'échec, lance une RuntimeException (gérée par public/index.php).
 */
class Database
{
    private static ?PDO $pdo = null;

    public static function getConnexion(): PDO
    {
        if (self::$pdo === null) {
            $file = is_file(__DIR__ . '/config.php') ? __DIR__ . '/config.php' : __DIR__ . '/config.example.php';
            $c = require $file;
            try {
                self::$pdo = new PDO(
                    'mysql:host=' . $c['host'] . ';dbname=' . $c['name'] . ';charset=utf8mb4',
                    $c['user'],
                    $c['pass'],
                    [
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES   => false, // vraies requêtes préparées
                    ]
                );
            } catch (PDOException $e) {
                error_log('DB connection failed: ' . $e->getMessage());
                throw new RuntimeException('Base de données indisponible.', 0, $e);
            }
        }
        return self::$pdo;
    }
}
