<?php
require_once __DIR__ . '/../../../init.php';
require_once __DIR__ . '/../../../Controllers/CampaignC.php';
require_once __DIR__ . '/../../../Controllers/CentreC.php';
require_once __DIR__ . '/../../../Controllers/SponsorC.php';

AuthC::requireStaff();
$scope = AuthC::centreScope();

$ctrl = new CampaignC();
$erreurs = [];
$campagne = new Campaign();

if (isPost()) {
    if (!AuthC::validCsrf($_POST['csrf_token'] ?? null)) {
        $erreurs[] = 'Jeton de sécurité invalide. Veuillez réessayer.';
    } else {
        $campagne = Campaign::fromArray($_POST);
        if ($scope !== null) {
            $campagne->setCentreId($scope);
        }
        $erreurs = $ctrl->valider($campagne);
        if (!$erreurs) {
            if ($ctrl->addCampaign($campagne)) {
                flash('ok', 'Enregistrement effectué.');
                redirect('Views/back/campagnes/liste_campagnes.php');
            }
            $erreurs[] = "Enregistrement impossible : ce titre de campagne existe déjà.";
        }
    }
}

$mode = 'ajouter';
$val = $campagne->toArray();
$options = [
    'centre_id' => (new CentreC())->options(),
    'sponsor_id' => (new SponsorC())->options(),
];
$titre = 'Ajouter une campagne | BloodLink Admin';
require_once __DIR__ . '/../partials/debut.php';
require __DIR__ . '/_form_campagne.php';
require_once __DIR__ . '/../partials/fin.php';
