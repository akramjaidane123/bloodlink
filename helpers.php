<?php

function e($valeur): string
{
    return htmlspecialchars((string) $valeur, ENT_QUOTES, 'UTF-8');
}

function url(string $chemin): string
{
    return BASE_URL . '/' . ltrim($chemin, '/');
}

function front(string $page): string
{
    return url('Views/front/' . $page);
}

function back(string $page): string
{
    return url('Views/back/' . $page);
}

function redirect(string $chemin): void
{
    header('Location: ' . url($chemin));
    exit;
}

function isPost(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(AuthC::getCsrfToken()) . '">';
}

function navActive(string $fichier): string
{
    return basename($_SERVER['SCRIPT_NAME'] ?? '') === $fichier ? 'active' : '';
}

function navActiveBack(string $dossier): string
{
    return basename(dirname($_SERVER['SCRIPT_NAME'] ?? '')) === $dossier ? 'active' : '';
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'] = [$type, $message];
}

function pullFlash(): string
{
    if (empty($_SESSION['flash'])) {
        return '';
    }
    [$type, $message] = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return '<div class="alert alert-' . ($type === 'ok' ? 'success' : 'error') . '">' . e($message) . '</div>';
}

function afficherErreurs(array $erreurs): string
{
    if (!$erreurs) {
        return '';
    }
    $html = '<div class="alert alert-error" role="alert">';
    foreach ($erreurs as $erreur) {
        $html .= '<div>' . e($erreur) . '</div>';
    }
    return $html . '</div>';
}

function labels(): array
{
    return [
        'reservation' => ['pending' => 'En attente', 'confirmed' => 'Confirmée', 'refused' => 'Refusée', 'cancelled' => 'Annulée', 'done' => 'Effectuée'],
        'urgency'     => ['low' => 'Faible', 'medium' => 'Moyen', 'urgent' => 'Urgent'],
        'centre'      => ['pending' => 'En attente', 'validated' => 'Validé'],
        'contract'    => ['pending' => 'En attente', 'active' => 'Actif', 'expired' => 'Expiré'],
        'comment'     => ['visible' => 'Visible', 'hidden' => 'Masqué'],
        'role'        => ['user' => 'Donneur', 'centre' => 'Centre', 'admin' => 'Administrateur'],
        'blood'       => array_combine(['O+', 'O-', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-'], ['O+', 'O-', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-']),
    ];
}

function badge(string $groupe, ?string $valeur): string
{
    $texte = labels()[$groupe][$valeur] ?? (string) $valeur;
    return '<span class="badge badge-' . e($valeur) . '">' . e($texte) . '</span>';
}

function dateFr(?string $date): string
{
    return $date ? date('d/m/Y', strtotime($date)) : '';
}

function accueilPour(string $role): string
{
    return in_array($role, ['admin', 'centre'], true) ? 'Views/back/tableau_de_bord.php' : 'Views/front/mon_compte.php';
}

function afficherErreur(int $code): void
{
    $messages = [
        403 => ['Accès refusé', "Vous n'avez pas les droits nécessaires pour voir cette page."],
        404 => ['Page introuvable', "Cette page n'existe pas ou a été déplacée."],
        500 => ['Erreur serveur', "Une erreur est survenue. Vérifiez que la base de données est importée (database/bloodlink.sql)."],
    ];
    http_response_code($code);
    [$titreErreur, $texteErreur] = $messages[$code] ?? $messages[500];
    $codeErreur = $code;
    require __DIR__ . '/Views/front/erreur.php';
    exit;
}
