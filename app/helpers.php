<?php
/**
 * app/helpers.php - small view helpers
 */

/** Escape output (XSS protection). Use on EVERY value printed in a view. */
function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/** Returns 'active' when $page is the current page (navbar / sidebar). */
function navActive(string $page, string $currentPage): string
{
    return $page === $currentPage ? 'active' : '';
}

/** Hidden CSRF input to put right after every <form ...> */
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(AuthController::getCsrfToken()) . '">';
}

/** Redirect and stop. */
function redirect(string $page): void
{
    header('Location: index.php?page=' . urlencode($page));
    exit;
}

/** Entités du back office (chargées une seule fois). */
function entities(): array
{
    static $entities = null;
    return $entities ??= require __DIR__ . '/config/entities.php';
}

/** Libellés français des statuts (groupe => valeur => libellé). */
function labels(): array
{
    return [
        'reservation' => ['pending' => 'En attente', 'confirmed' => 'Confirmée', 'refused' => 'Refusée', 'cancelled' => 'Annulée', 'done' => 'Effectuée'],
        'urgency'     => ['low' => 'Faible', 'medium' => 'Moyen', 'urgent' => 'Urgent'],
        'centre'      => ['pending' => 'En attente', 'validated' => 'Validé'],
        'contract'    => ['pending' => 'En attente', 'active' => 'Actif', 'expired' => 'Expiré'],
        'comment'     => ['visible' => 'Visible', 'hidden' => 'Masqué'],
    ];
}

/** Pastille colorée d'un statut. */
function badge(string $group, ?string $value): string
{
    $text = labels()[$group][$value] ?? (string) $value;
    return '<span class="badge badge-' . e($value) . '">' . e($text) . '</span>';
}

/** Page d'accueil après connexion selon le rôle. */
function homeFor(string $role): string
{
    return in_array($role, ['admin', 'centre'], true) ? 'admin' : 'dashboard';
}

/** Message flash (affiché une seule fois). */
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

/** 2026-11-28 -> 28/11/2026 */
function dateFr(?string $date): string
{
    return $date ? date('d/m/Y', strtotime($date)) : '';
}
