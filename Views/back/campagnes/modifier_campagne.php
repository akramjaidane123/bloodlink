<?php
require_once __DIR__ . '/../../../init.php';
require_once __DIR__ . '/../../../Controllers/CampaignC.php';
require_once __DIR__ . '/../../../Controllers/CentreC.php';
require_once __DIR__ . '/../../../Controllers/SponsorC.php';

AuthC::requireStaff();
$scope = AuthC::centreScope();

$ctrl = new CampaignC();
$id = (int) ($_GET['id'] ?? 0);
$row = $ctrl->getCampaign($id, $scope);
if (!$row) {
    afficherErreur(404);
}

$erreurs = [];
$campagne = Campaign::fromArray($row);

if (isPost()) {
    if (!AuthC::validCsrf($_POST['csrf_token'] ?? null)) {
        $erreurs[] = 'Jeton de sécurité invalide. Veuillez réessayer.';
    } else {
        $campagne = Campaign::fromArray($_POST);
        $campagne->setId($id);
        if ($scope !== null) {
            $campagne->setCentreId($scope);
        }
        $erreurs = $ctrl->valider($campagne);
        if (!$erreurs) {
            if ($ctrl->updateCampaign($campagne, $id, $scope)) {
                flash('ok', 'Modification enregistrée.');
                redirect('Views/back/campagnes/liste_campagnes.php');
            }
            $erreurs[] = "Enregistrement impossible : ce titre de campagne existe déjà.";
        }
    }
}

$mode = 'modifier';
$val = $campagne->toArray();
$options = [
    'centre_id' => (new CentreC())->options(),
    'sponsor_id' => (new SponsorC())->options(),
];
$titre = 'Modifier une campagne | BloodLink Admin';
require_once __DIR__ . '/../partials/debut.php';
require __DIR__ . '/_form_campagne.php';
require_once __DIR__ . '/../partials/fin.php';
