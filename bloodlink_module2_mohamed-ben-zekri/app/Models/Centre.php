<?php
/** Centre N --- 1 Gouvernorat */
class Centre extends Model
{
    public static function validated(?int $gouvernorat = null): array
    {
        $sql = "SELECT c.*, g.name AS gouvernorat FROM centres c JOIN gouvernorats g ON g.id = c.gouvernorat_id WHERE c.status = 'validated'";
        $params = [];
        if ($gouvernorat) { $sql .= ' AND c.gouvernorat_id = :g'; $params['g'] = $gouvernorat; }
        return self::all($sql . ' ORDER BY g.name, c.name', $params);
    }

    public static function gouvernorats(): array
    {
        return self::all('SELECT id, name FROM gouvernorats ORDER BY name');
    }

    public static function isValidated(int $id): bool
    {
        return (bool) self::value("SELECT 1 FROM centres WHERE id = :id AND status = 'validated'", ['id' => $id]);
    }
}
