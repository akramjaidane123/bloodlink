<?php
/** Actualités : liste des articles, détail et commentaires (donneur connecté). */
class ArticleController extends Controller
{
    protected string $page  = 'news';
    protected string $title = 'Actualités | BloodLink';

    public function index(): void
    {
        $this->render('news', ['articles' => Article::latest()]);
    }

    public function show(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $article = Article::find($id);
        if (!$article) {
            (new ErrorController())->notFound();
            return;
        }
        $this->title = $article['title'] . ' | BloodLink';
        $error = null;

        if ($this->isPost()) {
            $content = trim((string) ($_POST['content'] ?? ''));
            if (!AuthController::isAuthenticated()) {
                redirect('login');
            } elseif (!AuthController::validCsrf($_POST['csrf_token'] ?? null)) {
                $error = 'Jeton de sécurité invalide. Veuillez réessayer.';
            } elseif (mb_strlen($content) < 3 || mb_strlen($content) > 1000) {
                $error = 'Le commentaire doit contenir entre 3 et 1000 caractères.';
            } else {
                Article::addComment($id, (int) $_SESSION['user_id'], $content);
                flash('ok', 'Commentaire publié.');
                header('Location: index.php?page=article&id=' . $id);
                exit;
            }
        }
        $this->render('article', ['article' => $article, 'comments' => Article::comments($id), 'error' => $error]);
    }
}
