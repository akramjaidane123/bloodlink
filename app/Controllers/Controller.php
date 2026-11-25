<?php
/**
 * Base controller: renders a content view inside a layout.
 * (Template workshop, Part 2 - step 8/9)
 */
abstract class Controller
{
    /** Key of the current page, used by navbar/sidebar for the "active" state */
    protected string $page = 'home';

    /** Text shown in <title> */
    protected string $title = 'BloodLink';

    /** 'front' (public site) or 'admin' (back office) */
    protected string $layout = 'front';

    /**
     * Renders app/Views/pages/{$view}.php inside the layout.
     *
     * @param string      $view         view file name (without .php)
     * @param array       $data         variables extracted into the view
     * @param string|null $scriptsView  optional page-specific JS partial
     */
    protected function render(string $view, array $data = [], ?string $scriptsView = null): void
    {
        $content      = $this->capture(__DIR__ . '/../Views/pages/' . $view . '.php', $data);
        $extraScripts = $scriptsView
            ? $this->capture(__DIR__ . '/../Views/pages/' . $scriptsView . '.php', $data)
            : '';

        $title       = $this->title;
        $currentPage = $this->page;
        $isAdminLayout = $this->layout === 'admin';

        require_once __DIR__ . '/../Views/' . ($isAdminLayout ? 'layout_admin' : 'layout') . '.php';
    }

    /** Executes a view file and returns its HTML as a string. */
    private function capture(string $file, array $data): string
    {
        extract($data, EXTR_SKIP);
        ob_start();
        require $file;
        return (string) ob_get_clean();
    }

    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
}
