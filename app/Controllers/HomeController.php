<?php
class HomeController extends Controller
{
    protected string $page  = 'home';
    protected string $title = 'BloodLink | Relie les donneurs. Sauve des vies.';

    public function index(): void
    {
        $data = ['needs' => [], 'campaigns' => [], 'articles' => [], 'dbError' => false];
        try {
            $data['needs']     = Need::filtered(null, null, 6);
            $data['campaigns'] = Campaign::upcoming(3);
            $data['articles']  = Article::latest(3);
        } catch (Throwable $e) {
            error_log($e->getMessage());
            $data['dbError'] = true; // la page reste affichée, avec un message
        }
        $this->render('home', $data);
    }
}
