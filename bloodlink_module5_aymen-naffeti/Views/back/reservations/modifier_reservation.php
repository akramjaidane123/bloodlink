<?php
require_once __DIR__ . '/../../../init.php';
require_once __DIR__ . '/../../../Controllers/ReservationC.php';

AuthC::requireStaff();
$scope = AuthC::centreScope();

$ctrl = new ReservationC();
$id = (int) ($_GET['id'] ?? 0);
$row = $ctrl->getReservation($id, $scope);
if (!$row) {
    afficherErreur(404);
}

$erreurs = [];
$reservation = Reservation::fromArray($row);

if (isPost()) {
    if (!AuthC::validCsrf($_POST['csrf_token'] ?? null)) {
        $erreurs[] = 'Jeton de sécurité invalide. Veuillez réessayer.';
    } else {
        $reservation = Reservation::fromArray($_POST);
        $reservation->setId($id);
        $erreurs = $ctrl->valider($reservation);
        if (!$erreurs) {
            if ($ctrl->updateReservation($reservation, $id, $scope)) {
                flash('ok', 'Modification enregistrée.');
                redirect('Views/back/reservations/liste_reservations.php');
            }
            $erreurs[] = "Enregistrement impossible : données invalides ou doublon.";
        }
    }
}

$mode = 'modifier';
$val = $reservation->toArray();
$options = [];
$titre = 'Modifier une réservation | BloodLink Admin';
require_once __DIR__ . '/../partials/debut.php';
require __DIR__ . '/_form_reservation.php';
require_once __DIR__ . '/../partials/fin.php';
