<?php
/** Back office : tableau de bord (admin + centre) et liste des comptes (admin). */
class AdminController extends Controller
{
    protected string $page   = 'admin';
    protected string $title  = 'Tableau de bord | BloodLink Admin';
    protected string $layout = 'admin';

    public function index(): void
    {
        $isAdmin = ($_SESSION['role'] ?? '') === 'admin';
        $cid = $isAdmin ? null : Model::userCentre((int) $_SESSION['user_id']);
        $w  = $cid ? ' WHERE centre_id = :c' : '';
        $wa = $cid ? ' AND centre_id = :c' : '';
        $p  = $cid ? ['c' => $cid] : [];

        $stats = [
            ['Donneurs inscrits', Model::value("SELECT COUNT(*) FROM users WHERE role = 'user'"), 'accent'],
            ['Besoins publiés', Model::value('SELECT COUNT(*) FROM needs' . $w, $p), 'red'],
            ['Campagnes', Model::value('SELECT COUNT(*) FROM campaigns' . $w, $p), ''],
            ['Réservations en attente', Model::value("SELECT COUNT(*) FROM reservations WHERE status = 'pending'" . $wa, $p), 'red'],
        ];
        if ($isAdmin) {
            $stats[] = ['Centres validés', Model::value("SELECT COUNT(*) FROM centres WHERE status = 'validated'"), 'accent'];
            $stats[] = ['Articles', Model::value('SELECT COUNT(*) FROM articles'), ''];
        }
        $this->render('admin_dashboard', [
            'stats'    => $stats,
            'byStatus' => Model::all('SELECT status, COUNT(*) AS total FROM reservations' . $w . ' GROUP BY status', $p),
            'byGroup'  => Model::all('SELECT blood_group, SUM(quantity) AS total FROM needs' . $w . ' GROUP BY blood_group ORDER BY total DESC', $p),
            'recent'   => Model::all('SELECT r.id, r.donation_date, r.status, u.username AS donor, c.name AS centre
                                      FROM reservations r JOIN users u ON u.id = r.user_id JOIN centres c ON c.id = r.centre_id'
                                      . ($cid ? ' WHERE r.centre_id = :c' : '') . ' ORDER BY r.created_at DESC LIMIT 5', $p),
            'pendingCentres' => $isAdmin ? Model::all("SELECT id, name FROM centres WHERE status = 'pending' ORDER BY name") : [],
        ]);
    }

    public function users(): void
    {
        $this->page  = 'admin-users';
        $this->title = 'Comptes | BloodLink Admin';
        $this->render('admin_users', ['allUsers' => (new AuthController())->getAllUsers()]);
    }
}
