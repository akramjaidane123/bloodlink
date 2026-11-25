<?php
class CentreController extends Controller
{
    protected string $page  = 'centres';
    protected string $title = 'Centres de collecte | BloodLink';

    public function index(): void
    {
        $gov = (int) ($_GET['gov'] ?? 0) ?: null;
        $this->render('centres', ['centres' => Centre::validated($gov), 'gouvernorats' => Centre::gouvernorats(), 'gov' => $gov]);
    }
}
