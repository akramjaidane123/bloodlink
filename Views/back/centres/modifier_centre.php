<?php
require_once __DIR__ . '/../../../init.php';
require_once __DIR__ . '/../../../Controllers/CentreC.php';
require_once __DIR__ . '/../../../Controllers/GouvernoratC.php';

AuthC::requireRole('admin');

$ctrl = new CentreC();
$id = (int) ($_GET['id'] ?? 0);
$row = $ctrl->getCentre($id);
if (!$row) {
    afficherErreur(404);
}

$erreurs = [];
$centre = Centre::fromArray($row);

if (isPost()) {
    if (!AuthC::validCsrf($_POST['csrf_token'] ?? null)) {
        $erreurs[] = 'Jeton de sécurité invalide. Veuillez réessayer.';
    } else {
        $centre = Centre::fromArray($_POST);
        $centre->setId($id);
        $erreurs = $ctrl->valider($centre);
        if (!$erreurs) {
            if ($ctrl->updateCentre($centre, $id)) {
                flash('ok', 'Modification enregistrée.');
                redirect('Views/back/centres/liste_centres.php');
            }
            $erreurs[] = "Enregistrement impossible : ce nom de centre existe déjà.";
        }
    }
}

$mode = 'modifier';
$val = $centre->toArray();
$options = [
    'gouvernorat_id' => (new GouvernoratC())->options(),
];
$titre = 'Modifier un centre | BloodLink Admin';
require_once __DIR__ . '/../partials/debut.php';
require __DIR__ . '/_form_centre.php';
require_once __DIR__ . '/../partials/fin.php';
