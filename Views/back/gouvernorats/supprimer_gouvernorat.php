<?php
require_once __DIR__ . '/../../../init.php';
require_once __DIR__ . '/../../../Controllers/GouvernoratC.php';

AuthC::requireRole('admin');

if (!isPost() || !AuthC::validCsrf($_POST['csrf_token'] ?? null)) {
    flash('error', 'Requête invalide.');
    redirect('Views/back/gouvernorats/liste_gouvernorats.php');
}

$ctrl = new GouvernoratC();
$id = (int) ($_POST['id'] ?? 0);

if ($ctrl->deleteGouvernorat($id)) {
    flash('ok', 'Élément supprimé.');
} else {
    flash('error', 'Suppression impossible : des données liées existent ou l\'élément est introuvable.');
}
redirect('Views/back/gouvernorats/liste_gouvernorats.php');
