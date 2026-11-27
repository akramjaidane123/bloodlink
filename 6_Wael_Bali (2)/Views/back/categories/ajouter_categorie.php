<?php
require_once __DIR__ . '/../../../init.php';
require_once __DIR__ . '/../../../Controllers/CategoryC.php';

AuthC::requireRole('admin');

$ctrl = new CategoryC();
$erreurs = [];
$categorie = new Category();

if (isPost()) {
    if (!AuthC::validCsrf($_POST['csrf_token'] ?? null)) {
        $erreurs[] = 'Jeton de sécurité invalide. Veuillez réessayer.';
    } else {
        $categorie = Category::fromArray($_POST);
        $erreurs = $ctrl->valider($categorie);
        if (!$erreurs) {
            if ($ctrl->addCategory($categorie)) {
                flash('ok', 'Enregistrement effectué.');
                redirect('Views/back/categories/liste_categories.php');
            }
            $erreurs[] = "Enregistrement impossible : cette catégorie existe déjà.";
        }
    }
}

$mode = 'ajouter';
$val = $categorie->toArray();
$options = [];
$titre = 'Ajouter une catégorie | BloodLink Admin';
require_once __DIR__ . '/../partials/debut.php';
require __DIR__ . '/_form_categorie.php';
require_once __DIR__ . '/../partials/fin.php';
