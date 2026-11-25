<?php
require_once __DIR__ . '/../../../init.php';
require_once __DIR__ . '/../../../Controllers/NeedC.php';

AuthC::requireStaff();
$scope = AuthC::centreScope();

if (!isPost() || !AuthC::validCsrf($_POST['csrf_token'] ?? null)) {
    flash('error', 'Requête invalide.');
    redirect('Views/back/besoins/liste_besoins.php');
}

$ctrl = new NeedC();
$id = (int) ($_POST['id'] ?? 0);

if ($ctrl->deleteNeed($id, $scope)) {
    flash('ok', 'Élément supprimé.');
} else {
    flash('error', 'Suppression impossible : des données liées existent ou l\'élément est introuvable.');
}
redirect('Views/back/besoins/liste_besoins.php');
