<?php
require_once __DIR__ . '/../../../init.php';
require_once __DIR__ . '/../../../Controllers/CategoryC.php';

AuthC::requireRole('admin');

if (!isPost() || !AuthC::validCsrf($_POST['csrf_token'] ?? null)) {
    flash('error', 'Requête invalide.');
    redirect('Views/back/categories/liste_categories.php');
}

$ctrl = new CategoryC();
$id = (int) ($_POST['id'] ?? 0);

if ($ctrl->deleteCategory($id)) {
    flash('ok', 'Élément supprimé.');
} else {
    flash('error', 'Suppression impossible : des données liées existent ou l\'élément est introuvable.');
}
redirect('Views/back/categories/liste_categories.php');
