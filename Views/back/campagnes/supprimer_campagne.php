<?php
require_once __DIR__ . '/../../../init.php';
require_once __DIR__ . '/../../../Controllers/CampaignC.php';

AuthC::requireStaff();
$scope = AuthC::centreScope();

if (!isPost() || !AuthC::validCsrf($_POST['csrf_token'] ?? null)) {
    flash('error', 'Requête invalide.');
    redirect('Views/back/campagnes/liste_campagnes.php');
}

$ctrl = new CampaignC();
$id = (int) ($_POST['id'] ?? 0);

if ($ctrl->deleteCampaign($id, $scope)) {
    flash('ok', 'Élément supprimé.');
} else {
    flash('error', 'Suppression impossible : des données liées existent ou l\'élément est introuvable.');
}
redirect('Views/back/campagnes/liste_campagnes.php');
