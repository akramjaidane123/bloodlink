<?php
/** Campagne N --- 1 Sponsor */
class Campaign extends Model
{
    public static function upcoming(int $limit = 50): array
    {
        return self::all(
            'SELECT ca.*, c.name AS centre, s.name AS sponsor
             FROM campaigns ca JOIN centres c ON c.id = ca.centre_id LEFT JOIN sponsors s ON s.id = ca.sponsor_id
             WHERE ca.end_date >= CURDATE() ORDER BY ca.start_date LIMIT ' . (int) $limit
        );
    }
}
