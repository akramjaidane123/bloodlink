<?php
require_once __DIR__ . '/../../init.php';
require_once __DIR__ . '/../../Controllers/CentreC.php';
require_once __DIR__ . '/../../Controllers/ReservationC.php';

AuthC::requireAuth();

$cc      = new CentreC();
$rc      = new ReservationC();
$erreurs = [];
$choisi  = (int) ($_GET['centre'] ?? 0);

if (isPost()) {
    $choisi = (int) ($_POST['centre_id'] ?? 0);
    $date   = (string) ($_POST['donation_date'] ?? '');

    if (!AuthC::validCsrf($_POST['csrf_token'] ?? null)) {
        $erreurs[] = 'Jeton de sécurité invalide. Veuillez réessayer.';
    } else {
        $erreurs = $rc->validerDemande($choisi, $date);
    }
    if (!$erreurs) {
        $reservation = new Reservation((int) $_SESSION['user_id'], $choisi, $date, 'pending');
        if ($rc->addReservation($reservation)) {
            flash('ok', 'Réservation enregistrée. Le centre la confirmera prochainement.');
            redirect('Views/front/mon_compte.php');
        }
        $erreurs[] = "Impossible d'enregistrer la réservation.";
    }
}
$centres = $cc->centresValides();

$titre = 'Réserver un don | BloodLink';
require_once __DIR__ . '/partials/head.php';
require_once __DIR__ . '/partials/spinner.php';
require_once __DIR__ . '/partials/navbar.php';
?>
<main class="site-main">
    <section class="wrap section narrow">
        <h1>Réserver un don</h1>
        <?= afficherErreurs($erreurs) ?>
        <form class="card" method="POST" novalidate data-validate>
            <?= csrf_field() ?>
            <label for="centre_id">Centre de collecte</label>
            <select id="centre_id" name="centre_id" required data-label="Centre">
                <option value="">Choisir un centre</option>
                <?php foreach ($centres as $c): ?>
                    <option value="<?= (int) $c['id'] ?>" <?= $choisi === (int) $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?> (<?= e($c['gouvernorat']) ?>)</option>
                <?php endforeach; ?>
            </select>

            <label for="donation_date">Date souhaitée</label>
            <input type="date" id="donation_date" name="donation_date" required data-label="Date"
                   min="<?= e(date('Y-m-d', strtotime('+1 day'))) ?>" max="<?= e(date('Y-m-d', strtotime('+90 days'))) ?>">

            <button class="btn btn-primary btn-block" type="submit">Confirmer la réservation</button>
        </form>
    </section>
</main>
<?php
require_once __DIR__ . '/partials/footer.php';
require_once __DIR__ . '/partials/scripts.php';
