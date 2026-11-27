<?php
require_once __DIR__ . '/../../../init.php';
require_once __DIR__ . '/../../../Controllers/NotificationC.php';

AuthC::requireRole('admin');

$ctrl = new NotificationC();
$erreurs = [];
$notification = new Notification();

if (isPost()) {
    if (!AuthC::validCsrf($_POST['csrf_token'] ?? null)) {
        $erreurs[] = 'Jeton de sécurité invalide. Veuillez réessayer.';
    } else {
        $notification = Notification::fromArray($_POST);
        $notification->setUserId(null);
        $erreurs = $ctrl->valider($notification);
        if (!$erreurs) {
            if ($ctrl->addNotification($notification)) {
                flash('ok', 'Enregistrement effectué.');
                redirect('Views/back/notifications/liste_notifications.php');
            }
            $erreurs[] = "Enregistrement impossible : données invalides ou doublon.";
        }
    }
}

$mode = 'ajouter';
$val = $notification->toArray();
$options = [];
$titre = 'Ajouter une notification | BloodLink Admin';
require_once __DIR__ . '/../partials/debut.php';
require __DIR__ . '/_form_notification.php';
require_once __DIR__ . '/../partials/fin.php';
