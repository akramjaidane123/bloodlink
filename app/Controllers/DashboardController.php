<?php
/** Mon compte (front office) : profil, réservations, notifications. */
class DashboardController extends Controller
{
    protected string $page  = 'dashboard';
    protected string $title = 'Mon compte | BloodLink';

    public function index(): void
    {
        $auth = new AuthController();
        $user = $auth->findById((int) $_SESSION['user_id']);
        if (!$user) { // compte supprimé alors que la session est active
            $auth->doLogout();
            redirect('login');
        }
        $this->render('dashboard', [
            'user' => $user,
            'reservations'  => Reservation::forUser($user->getId()),
            'notifications' => Notification::forUser($user->getId()),
        ]);
    }
}
