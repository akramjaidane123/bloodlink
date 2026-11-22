<?php /** Attend : $user (UserModel), $reservations, $notifications */ ?>
<div class="wrap section">
    <?= pullFlash() ?>
    <h1>Bienvenue, <?= e($user->getUsername()) ?></h1>
    <p><span class="badge badge-user"><?= e($user->getEmail()) ?></span> <span class="muted">Membre depuis le <?= dateFr($user->getCreatedAt()) ?></span></p>

    <div class="card">
        <h2>Mes réservations</h2>
        <?php if (!$reservations): ?>
            <p>Aucune réservation. <a href="index.php?page=reserve">Réserver un don</a></p>
        <?php else: ?>
        <table>
            <thead><tr><th>Centre</th><th>Date</th><th>Statut</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($reservations as $r): ?>
                <tr><td><?= e($r['centre']) ?></td><td><?= dateFr($r['donation_date']) ?></td><td><?= badge('reservation', $r['status']) ?></td>
                    <td><?php if (in_array($r['status'], ['pending', 'confirmed'], true)): ?>
                        <form class="inline-form" method="POST" action="index.php?page=reserve-cancel" data-confirm="Annuler cette réservation ?">
                            <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
                            <button class="btn btn-secondary" type="submit">Annuler</button>
                        </form>
                    <?php endif; ?></td></tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

    <div class="card">
        <h2>Mes notifications</h2>
        <?php if (!$notifications): ?><p class="muted">Aucune notification.</p><?php endif; ?>
        <?php foreach ($notifications as $n): ?>
            <p><strong><?= e($n['title']) ?></strong> <span class="muted"><?= dateFr($n['created_at']) ?></span><br><?= e($n['message']) ?></p>
        <?php endforeach; ?>
    </div>
</div>
