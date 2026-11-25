<?php
/** Base des modèles : accès PDO (requêtes préparées uniquement). */
class Model
{
    public static function all(string $sql, array $params = []): array
    {
        $stmt = Database::getConnexion()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function one(string $sql, array $params = []): ?array
    {
        return self::all($sql, $params)[0] ?? null;
    }

    public static function value(string $sql, array $params = []): mixed
    {
        $stmt = Database::getConnexion()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    public static function run(string $sql, array $params = []): int
    {
        $stmt = Database::getConnexion()->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    /** Centre rattaché à un compte "centre" (null sinon). */
    public static function userCentre(int $userId): ?int
    {
        $id = self::value('SELECT centre_id FROM users WHERE id = :id', ['id' => $userId]);
        return $id ? (int) $id : null;
    }
}
