<?php
require_once __DIR__ . '/../../../init.php';
require_once __DIR__ . '/../../../Controllers/GouvernoratC.php';

AuthC::requireRole('admin');

$ctrl = new GouvernoratC();
$id = (int) ($_GET['id'] ?? 0);
$row = $ctrl->getGouvernorat($id);
if (!$row) {
    afficherErreur(404);
}

$erreurs = [];
$gouvernorat = Gouvernorat::fromArray($row);

if (isPost()) {
    if (!AuthC::validCsrf($_POST['csrf_token'] ?? null)) {
        $erreurs[] = 'Jeton de sécurité invalide. Veuillez réessayer.';
    } else {
        $gouvernorat = Gouvernorat::fromArray($_POST);
        $gouvernorat->setId($id);
        $erreurs = $ctrl->valider($gouvernorat);
        if (!$erreurs) {
            if ($ctrl->updateGouvernorat($gouvernorat, $id)) {
                flash('ok', 'Modification enregistrée.');
                redirect('Views/back/gouvernorats/liste_gouvernorats.php');
            }
            $erreurs[] = "Enregistrement impossible : ce gouvernorat existe déjà.";
        }
    }
}

$mode = 'modifier';
$val = $gouvernorat->toArray();
$options = [];
$titre = 'Modifier un gouvernorat | BloodLink Admin';
require_once __DIR__ . '/../partials/debut.php';
require __DIR__ . '/_form_gouvernorat.php';
require_once __DIR__ . '/../partials/fin.php';
