<?php
/** Besoins en sang : liste filtrable par groupe sanguin et gouvernorat. */
class NeedController extends Controller
{
    protected string $page  = 'needs';
    protected string $title = 'Besoins en sang | BloodLink';

    public function index(): void
    {
        $groups = ['O+', 'O-', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-'];
        $blood  = in_array($_GET['blood'] ?? '', $groups, true) ? $_GET['blood'] : null;
        $gov    = (int) ($_GET['gov'] ?? 0) ?: null;
        $this->render('needs', [
            'needs' => Need::filtered($blood, $gov), 'groups' => $groups, 'gouvernorats' => Centre::gouvernorats(),
            'blood' => $blood, 'gov' => $gov,
        ]);
    }
}
