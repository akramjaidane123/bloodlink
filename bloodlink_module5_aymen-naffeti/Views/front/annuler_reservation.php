<?php
require_once __DIR__ . '/../../init.php';
require_once __DIR__ . '/../../Controllers/ReservationC.php';

AuthC::requireAuth();

if (isPost() && AuthC::validCsrf($_POST['csrf_token'] ?? null)) {
    $ok = (new ReservationC())->annulerReservation((int) ($_POST['id'] ?? 0), (int) $_SESSION['user_id']);
    flash($ok ? 'ok' : 'error', $ok ? 'Réservation annulée.' : 'Annulation impossible.');
}
redirect('Views/front/mon_compte.php');
