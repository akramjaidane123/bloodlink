<?php
/** Réservation d'un don (donneur connecté). */
class ReservationController extends Controller
{
    protected string $page  = 'reserve';
    protected string $title = 'Réserver un don | BloodLink';

    public function create(): void
    {
        $errors = [];
        $selected = (int) ($_GET['centre'] ?? 0);

        if ($this->isPost()) {
            $selected = (int) ($_POST['centre_id'] ?? 0);
            $date = (string) ($_POST['donation_date'] ?? '');
            $d = DateTime::createFromFormat('Y-m-d', $date);
            $min = (new DateTime('tomorrow'))->setTime(0, 0);
            $max = new DateTime('+90 days');

            if (!AuthController::validCsrf($_POST['csrf_token'] ?? null)) {
                $errors[] = 'Jeton de sécurité invalide. Veuillez réessayer.';
            }
            if (!Centre::isValidated($selected)) {
                $errors[] = 'Veuillez choisir un centre valide.';
            }
            if (!$d || $d->format('Y-m-d') !== $date || $d < $min || $d > $max) {
                $errors[] = 'Choisissez une date entre demain et les 90 prochains jours.';
            }
            if (!$errors) {
                Reservation::create((int) $_SESSION['user_id'], $selected, $date);
                flash('ok', 'Réservation enregistrée. Le centre la confirmera prochainement.');
                redirect('dashboard');
            }
        }
        $this->render('reserve', ['centres' => Centre::validated(), 'selected' => $selected, 'errors' => $errors]);
    }

    public function cancel(): void
    {
        if ($this->isPost() && AuthController::validCsrf($_POST['csrf_token'] ?? null)) {
            $ok = Reservation::cancel((int) ($_POST['id'] ?? 0), (int) $_SESSION['user_id']);
            flash($ok ? 'ok' : 'error', $ok ? 'Réservation annulée.' : 'Annulation impossible.');
        }
        redirect('dashboard');
    }
}
