<?php
require_once __DIR__ . '/../../../init.php';
require_once __DIR__ . '/../../../Controllers/ReservationC.php';

AuthC::requireStaff();
$scope = AuthC::centreScope();

if (!isPost() || !AuthC::validCsrf($_POST['csrf_token'] ?? null)) {
    flash('error', 'Requête invalide.');
    redirect('Views/back/reservations/liste_reservations.php');
}

$ctrl = new ReservationC();
$id = (int) ($_POST['id'] ?? 0);

if ($ctrl->deleteReservation($id, $scope)) {
    flash('ok', 'Élément supprimé.');
} else {
    flash('error', 'Suppression impossible : des données liées existent ou l\'élément est introuvable.');
}
redirect('Views/back/reservations/liste_reservations.php');
