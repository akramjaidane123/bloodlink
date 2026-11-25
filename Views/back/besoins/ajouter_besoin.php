<?php
require_once __DIR__ . '/../../../init.php';
require_once __DIR__ . '/../../../Controllers/NeedC.php';
require_once __DIR__ . '/../../../Controllers/CentreC.php';

AuthC::requireStaff();
$scope = AuthC::centreScope();

$ctrl = new NeedC();
$erreurs = [];
$besoin = new Need();

if (isPost()) {
    if (!AuthC::validCsrf($_POST['csrf_token'] ?? null)) {
        $erreurs[] = 'Jeton de sécurité invalide. Veuillez réessayer.';
    } else {
        $besoin = Need::fromArray($_POST);
        if ($scope !== null) {
            $besoin->setCentreId($scope);
        }
        $erreurs = $ctrl->valider($besoin);
        if (!$erreurs) {
            if ($ctrl->addNeed($besoin)) {
                flash('ok', 'Enregistrement effectué.');
                redirect('Views/back/besoins/liste_besoins.php');
            }
            $erreurs[] = "Enregistrement impossible : ce centre a déjà un besoin pour ce groupe sanguin.";
        }
    }
}

$mode = 'ajouter';
$val = $besoin->toArray();
$options = [
    'centre_id' => (new CentreC())->options(),
];
$titre = 'Ajouter un besoin en sang | BloodLink Admin';
require_once __DIR__ . '/../partials/debut.php';
require __DIR__ . '/_form_besoin.php';
require_once __DIR__ . '/../partials/fin.php';
