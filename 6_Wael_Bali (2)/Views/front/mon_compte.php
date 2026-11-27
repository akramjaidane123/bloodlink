<?php
require_once __DIR__ . '/../../init.php';
require_once __DIR__ . '/../../Controllers/UserC.php';
require_once __DIR__ . '/../../Controllers/ReservationC.php';
require_once __DIR__ . '/../../Controllers/NotificationC.php';

AuthC::requireAuth();

$user = (new UserC())->getUser((int) $_SESSION['user_id']);
if (!$user) {
    (new AuthC())->logout();
    redirect('Views/front/connexion.php');
}
$reservations  = (new ReservationC())->reservationsDuDonneur((int) $user['id']);
$notifications = (new NotificationC())->notificationsPour((int) $user['id']);

$titre = 'Mon compte | BloodLink';
require_once __DIR__ . '/partials/head.php';
require_once __DIR__ . '/partials/spinner.php';
require_once __DIR__ . '/partials/navbar.php';
?>
<main class="site-main">
    <div class="wrap section">
        <?= pullFlash() ?>
        <h1>Bienvenue, <?= e($user['username']) ?></h1>
        <p>
            <span class="badge badge-user"><?= e($user['email']) ?></span>
            <span class="muted">Membre depuis le <?= e(dateFr($user['created_at'])) ?></span>
        </p>

        <section class="card">
            <h2>Mes réservations</h2>
            <?php if (!$reservations): ?>
                <p>Aucune réservation. <a href="<?= e(front('reserver.php')) ?>">Réserver un don</a></p>
            <?php else: ?>
                <div class="table-scroll">
                    <table>
                        <thead><tr><th>Centre</th><th>Date</th><th>Statut</th><th></th></tr></thead>
                        <tbody>
                            <?php foreach ($reservations as $r): ?>
                                <tr>
                                    <td><?= e($r['centre']) ?></td>
                                    <td><?= e(dateFr($r['donation_date'])) ?></td>
                                    <td><?= badge('reservation', $r['status']) ?></td>
                                    <td>
                                        <?php if (in_array($r['status'], ['pending', 'confirmed'], true)): ?>
                                            <form class="inline-form" method="POST" action="annuler_reservation.php" data-confirm="Annuler cette réservation ?">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
                                                <button class="btn btn-secondary" type="submit">Annuler</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>

        <section class="card">
            <h2>Mes notifications</h2>
            <?php if (!$notifications): ?><p class="muted">Aucune notification.</p><?php endif; ?>
            <?php foreach ($notifications as $n): ?>
                <p>
                    <strong><?= e($n['title']) ?></strong> <span class="muted"><?= e(dateFr($n['created_at'])) ?></span><br>
                    <?= e($n['message']) ?>
                </p>
            <?php endforeach; ?>
        </section>
    </div>
</main>
<?php
require_once __DIR__ . '/partials/footer.php';
require_once __DIR__ . '/partials/scripts.php';
