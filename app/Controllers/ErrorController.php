<?php
/** Pages d'erreur rendues dans le layout du site (404, 403, 500). */
class ErrorController extends Controller
{
    protected string $page = 'error';

    public function notFound(): void
    {
        http_response_code(404);
        $this->show('404', 'Page introuvable', "Cette page n'existe pas ou a été déplacée.");
    }

    public function forbidden(): void
    {
        http_response_code(403);
        $this->show('403', 'Accès refusé', "Vous n'avez pas les droits nécessaires pour voir cette page.");
    }

    public function serverError(): void
    {
        http_response_code(500);
        $this->show('500', 'Erreur serveur', "Une erreur est survenue. Vérifiez que la base de données est importée (database/bloodlink.sql) et que app/config/config.php est correct.");
    }

    private function show(string $code, string $heading, string $text): void
    {
        $this->title = $heading . ' | BloodLink';
        $this->render('error', compact('code', 'heading', 'text'));
    }
}
