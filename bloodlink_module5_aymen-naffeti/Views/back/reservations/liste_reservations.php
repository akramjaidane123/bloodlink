<?php
require_once __DIR__ . '/../../../init.php';
require_once __DIR__ . '/../../../Controllers/ReservationC.php';

AuthC::requireStaff();
$scope = AuthC::centreScope();

$ctrl = new ReservationC();
$lignes = $ctrl->listeReservations($scope);

$titre = 'Réservations | BloodLink Admin';
require_once __DIR__ . '/../partials/debut.php';
?>
<?= pullFlash() ?>
<section class="card" data-table>
    <div class="toolbar">
        <input type="search" class="t-search" placeholder="Rechercher…" aria-label="Rechercher">

    </div>
    <div class="table-scroll">
        <table>
            <thead>
                <tr>
                    <th data-sort>Donneur</th><th data-sort>Centre</th><th data-sort>Date du don</th><th data-sort>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lignes as $ligne): ?>
                <tr>
                    <td><?= e(mb_strimwidth((string) ($ligne['donor'] ?? ''), 0, 70, '…')) ?></td>
                    <td><?= e(mb_strimwidth((string) ($ligne['centre'] ?? ''), 0, 70, '…')) ?></td>
                    <td><?= e(dateFr($ligne['donation_date'] ?? null)) ?></td>
                    <td><?= badge('reservation', $ligne['status'] ?? null) ?></td>
                    <td class="actions">
                        <a class="btn btn-sm" href="modifier_reservation.php?id=<?= (int) $ligne['id'] ?>">Modifier</a>
                        <form class="inline-form" method="POST" action="supprimer_reservation.php" data-confirm="Supprimer cet élément ?">
                            <?= csrf_field() ?>
                            <input type="hidden" name="id" value="<?= (int) $ligne['id'] ?>">
                            <button class="btn btn-sm btn-danger" type="submit">Supprimer</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php if (!$lignes): ?><p class="muted">Aucun élément.</p><?php endif; ?>
    <div class="pager"></div>
</section>
<?php require_once __DIR__ . '/../partials/fin.php'; ?>
