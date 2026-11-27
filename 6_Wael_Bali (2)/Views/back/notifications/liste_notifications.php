<?php
require_once __DIR__ . '/../../../init.php';
require_once __DIR__ . '/../../../Controllers/NotificationC.php';

AuthC::requireRole('admin');

$ctrl = new NotificationC();
$lignes = $ctrl->listeNotifications();

$titre = 'Notifications | BloodLink Admin';
require_once __DIR__ . '/../partials/debut.php';
?>
<?= pullFlash() ?>
<section class="card" data-table>
    <div class="toolbar">
        <input type="search" class="t-search" placeholder="Rechercher…" aria-label="Rechercher">
        <a class="btn btn-primary" href="ajouter_notification.php">+ Ajouter</a>
    </div>
    <div class="table-scroll">
        <table>
            <thead>
                <tr>
                    <th data-sort>Titre</th><th data-sort>Message</th><th data-sort>Envoyée le</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lignes as $ligne): ?>
                <tr>
                    <td><?= e(mb_strimwidth((string) ($ligne['title'] ?? ''), 0, 70, '…')) ?></td>
                    <td><?= e(mb_strimwidth((string) ($ligne['message'] ?? ''), 0, 70, '…')) ?></td>
                    <td><?= e(dateFr($ligne['created_at'] ?? null)) ?></td>
                    <td class="actions">
                        <a class="btn btn-sm" href="modifier_notification.php?id=<?= (int) $ligne['id'] ?>">Modifier</a>
                        <form class="inline-form" method="POST" action="supprimer_notification.php" data-confirm="Supprimer cet élément ?">
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
