<?php
require_once __DIR__ . '/../../../init.php';
require_once __DIR__ . '/../../../Controllers/CentreC.php';

AuthC::requireRole('admin');

if (!isPost() || !AuthC::validCsrf($_POST['csrf_token'] ?? null)) {
    flash('error', 'Requête invalide.');
    redirect('Views/back/centres/liste_centres.php');
}

$ctrl = new CentreC();
$id = (int) ($_POST['id'] ?? 0);

if ($ctrl->deleteCentre($id)) {
    flash('ok', 'Élément supprimé.');
} else {
    flash('error', 'Suppression impossible : des données liées existent ou l\'élément est introuvable.');
}
redirect('Views/back/centres/liste_centres.php');
