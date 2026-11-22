<?php
/** Besoins en sang publiés par les centres. */
class Need extends Model
{
    public static function filtered(?string $blood, ?int $gouvernorat, int $limit = 100): array
    {
        $sql = "SELECT n.*, c.name AS centre, c.id AS centre_id, g.name AS gouvernorat
                FROM needs n JOIN centres c ON c.id = n.centre_id JOIN gouvernorats g ON g.id = c.gouvernorat_id
                WHERE c.status = 'validated'";
        $params = [];
        if ($blood)       { $sql .= ' AND n.blood_group = :b'; $params['b'] = $blood; }
        if ($gouvernorat) { $sql .= ' AND g.id = :g';          $params['g'] = $gouvernorat; }
        $sql .= " ORDER BY FIELD(n.urgency, 'urgent', 'medium', 'low'), n.quantity DESC LIMIT " . (int) $limit;
        return self::all($sql, $params);
    }
}
