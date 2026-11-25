<?php
require_once __DIR__ . '/../../../init.php';
require_once __DIR__ . '/../../../Controllers/NeedC.php';

AuthC::requireStaff();
$scope = AuthC::centreScope();

$ctrl = new NeedC();
$lignes = $ctrl->listeNeeds($scope);

$titre = 'Besoins en sang | BloodLink Admin';
require_once __DIR__ . '/../partials/debut.php';
?>
<?= pullFlash() ?>
<section class="card" data-table>
    <div class="toolbar">
        <input type="search" class="t-search" placeholder="Rechercher…" aria-label="Rechercher">
        <a class="btn btn-primary" href="ajouter_besoin.php">+ Ajouter</a>
    </div>
    <div class="table-scroll">
        <table>
            <thead>
                <tr>
                    <th data-sort>Centre</th><th data-sort>Groupe</th><th data-sort>Urgence</th><th data-sort>Poches</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lignes as $ligne): ?>
                <tr>
                    <td><?= e(mb_strimwidth((string) ($ligne['centre'] ?? ''), 0, 70, '…')) ?></td>
                    <td><?= e(mb_strimwidth((string) ($ligne['blood_group'] ?? ''), 0, 70, '…')) ?></td>
                    <td><?= badge('urgency', $ligne['urgency'] ?? null) ?></td>
                    <td><?= (int) ($ligne['quantity'] ?? 0) ?></td>
                    <td class="actions">
                        <a class="btn btn-sm" href="modifier_besoin.php?id=<?= (int) $ligne['id'] ?>">Modifier</a>
                        <form class="inline-form" method="POST" action="supprimer_besoin.php" data-confirm="Supprimer cet élément ?">
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
