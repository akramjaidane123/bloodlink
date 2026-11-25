<?php
/** Alertes envoyées aux donneurs (user_id NULL = tous). */
class Notification extends Model
{
    public static function forUser(int $userId, int $limit = 10): array
    {
        return self::all('SELECT * FROM notifications WHERE user_id IS NULL OR user_id = :u ORDER BY created_at DESC, id DESC LIMIT ' . (int) $limit,
            ['u' => $userId]);
    }
}
