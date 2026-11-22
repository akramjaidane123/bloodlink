<?php
/** Centre 1 --- N Réservation */
class Reservation extends Model
{
    public static function forUser(int $userId): array
    {
        return self::all(
            'SELECT r.*, c.name AS centre FROM reservations r JOIN centres c ON c.id = r.centre_id
             WHERE r.user_id = :u ORDER BY r.donation_date DESC',
            ['u' => $userId]
        );
    }

    public static function create(int $userId, int $centreId, string $date): void
    {
        self::run('INSERT INTO reservations (user_id, centre_id, donation_date) VALUES (:u, :c, :d)',
            ['u' => $userId, 'c' => $centreId, 'd' => $date]);
    }

    /** Le donneur ne peut annuler que ses propres réservations en attente ou confirmées. */
    public static function cancel(int $id, int $userId): bool
    {
        return self::run("UPDATE reservations SET status = 'cancelled' WHERE id = :id AND user_id = :u AND status IN ('pending','confirmed')",
            ['id' => $id, 'u' => $userId]) > 0;
    }
}
