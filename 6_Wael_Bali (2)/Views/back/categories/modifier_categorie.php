<?php
require_once __DIR__ . '/../../../init.php';
require_once __DIR__ . '/../../../Controllers/CategoryC.php';

AuthC::requireRole('admin');

$ctrl = new CategoryC();
$id = (int) ($_GET['id'] ?? 0);
$row = $ctrl->getCategory($id);
if (!$row) {
    afficherErreur(404);
}

$erreurs = [];
$categorie = Category::fromArray($row);

if (isPost()) {
    if (!AuthC::validCsrf($_POST['csrf_token'] ?? null)) {
        $erreurs[] = 'Jeton de sécurité invalide. Veuillez réessayer.';
    } else {
        $categorie = Category::fromArray($_POST);
        $categorie->setId($id);
        $erreurs = $ctrl->valider($categorie);
        if (!$erreurs) {
            if ($ctrl->updateCategory($categorie, $id)) {
                flash('ok', 'Modification enregistrée.');
                redirect('Views/back/categories/liste_categories.php');
            }
            $erreurs[] = "Enregistrement impossible : cette catégorie existe déjà.";
        }
    }
}

$mode = 'modifier';
$val = $categorie->toArray();
$options = [];
$titre = 'Modifier une catégorie | BloodLink Admin';
require_once __DIR__ . '/../partials/debut.php';
require __DIR__ . '/_form_categorie.php';
require_once __DIR__ . '/../partials/fin.php';
