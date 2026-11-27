<?php
require_once __DIR__ . '/../../../init.php';
require_once __DIR__ . '/../../../Controllers/NotificationC.php';

AuthC::requireRole('admin');

$ctrl = new NotificationC();
$id = (int) ($_GET['id'] ?? 0);
$row = $ctrl->getNotification($id);
if (!$row) {
    afficherErreur(404);
}

$erreurs = [];
$notification = Notification::fromArray($row);

if (isPost()) {
    if (!AuthC::validCsrf($_POST['csrf_token'] ?? null)) {
        $erreurs[] = 'Jeton de sécurité invalide. Veuillez réessayer.';
    } else {
        $notification = Notification::fromArray($_POST);
        $notification->setId($id);
        $erreurs = $ctrl->valider($notification);
        if (!$erreurs) {
            if ($ctrl->updateNotification($notification, $id)) {
                flash('ok', 'Modification enregistrée.');
                redirect('Views/back/notifications/liste_notifications.php');
            }
            $erreurs[] = "Enregistrement impossible : données invalides ou doublon.";
        }
    }
}

$mode = 'modifier';
$val = $notification->toArray();
$options = [];
$titre = 'Modifier une notification | BloodLink Admin';
require_once __DIR__ . '/../partials/debut.php';
require __DIR__ . '/_form_notification.php';
require_once __DIR__ . '/../partials/fin.php';
