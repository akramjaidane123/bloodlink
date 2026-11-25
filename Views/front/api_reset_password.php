<?php

// API JSON de la réinitialisation du mot de passe.
// Appelée en POST par assets/js/modules/reset_password.js (fetch).
// Actions : envoyer | renvoyer | verifier | changer

require_once __DIR__ . '/../../init.php';
require_once __DIR__ . '/../../Controllers/PasswordResetC.php';

header('Content-Type: application/json; charset=utf-8');

function repondre(array $donnees): void
{
    http_response_code($donnees['statut'] ?? 200);
    unset($donnees['statut'], $donnees['user_id']);
    echo json_encode($donnees, JSON_UNESCAPED_UNICODE);
    exit;
}

if (!isPost()) {
    repondre(['ok' => false, 'message' => 'Méthode non autorisée.', 'statut' => 405]);
}
if (!AuthC::validCsrf($_POST['csrf_token'] ?? null)) {
    repondre(['ok' => false, 'message' => 'Jeton de sécurité invalide. Rechargez la page.', 'statut' => 403]);
}

$prc    = new PasswordResetC();
$action = $_POST['action'] ?? '';
$etat   = $_SESSION['reset'] ?? null; // ['user_id' => int, 'canal' => string, 'verifie' => bool]

switch ($action) {
    case 'envoyer':
        $res = $prc->demanderCode((string) ($_POST['canal'] ?? ''), (string) ($_POST['identifiant'] ?? ''));
        if ($res['ok']) {
            $_SESSION['reset'] = ['user_id' => $res['user_id'], 'canal' => $res['canal'], 'verifie' => false];
        }
        repondre($res);

    case 'renvoyer':
        if (!$etat) {
            repondre(['ok' => false, 'message' => 'Session expirée. Recommencez.', 'statut' => 410]);
        }
        $res = $prc->renvoyerCode((int) $etat['user_id'], $etat['canal']);
        if ($res['ok']) {
            $res['message'] = 'Un nouveau code vous a été envoyé.';
        }
        repondre($res);

    case 'verifier':
        if (!$etat) {
            repondre(['ok' => false, 'message' => 'Session expirée. Recommencez.', 'statut' => 410]);
        }
        $res = $prc->verifierCode((int) $etat['user_id'], (string) ($_POST['code'] ?? ''));
        if ($res['ok']) {
            $_SESSION['reset']['verifie'] = true;
        }
        repondre($res);

    case 'changer':
        if (!$etat || empty($etat['verifie'])) {
            repondre(['ok' => false, 'message' => "Vérifiez d'abord le code reçu.", 'statut' => 403]);
        }
        $res = $prc->changerMotDePasse((int) $etat['user_id'], (string) ($_POST['password'] ?? ''), (string) ($_POST['password_confirm'] ?? ''));
        if ($res['ok']) {
            unset($_SESSION['reset']);
            $res['redirection'] = front('connexion.php?reinit=1');
        }
        repondre($res);

    default:
        repondre(['ok' => false, 'message' => 'Action inconnue.', 'statut' => 400]);
}
