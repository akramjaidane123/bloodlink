<?php
require_once __DIR__ . '/../../../init.php';
require_once __DIR__ . '/../../../Controllers/GouvernoratC.php';

AuthC::requireRole('admin');

$ctrl = new GouvernoratC();
$erreurs = [];
$gouvernorat = new Gouvernorat();

if (isPost()) {
    if (!AuthC::validCsrf($_POST['csrf_token'] ?? null)) {
        $erreurs[] = 'Jeton de sécurité invalide. Veuillez réessayer.';
    } else {
        $gouvernorat = Gouvernorat::fromArray($_POST);
        $erreurs = $ctrl->valider($gouvernorat);
        if (!$erreurs) {
            if ($ctrl->addGouvernorat($gouvernorat)) {
                flash('ok', 'Enregistrement effectué.');
                redirect('Views/back/gouvernorats/liste_gouvernorats.php');
            }
            $erreurs[] = "Enregistrement impossible : ce gouvernorat existe déjà.";
        }
    }
}

$mode = 'ajouter';
$val = $gouvernorat->toArray();
$options = [];
$titre = 'Ajouter un gouvernorat | BloodLink Admin';
require_once __DIR__ . '/../partials/debut.php';
require __DIR__ . '/_form_gouvernorat.php';
require_once __DIR__ . '/../partials/fin.php';
