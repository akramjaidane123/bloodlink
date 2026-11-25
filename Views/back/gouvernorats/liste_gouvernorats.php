<?php
require_once __DIR__ . '/../../../init.php';
require_once __DIR__ . '/../../../Controllers/GouvernoratC.php';

AuthC::requireRole('admin');

$ctrl = new GouvernoratC();
$lignes = $ctrl->listeGouvernorats();

$titre = 'Gouvernorats | BloodLink Admin';
require_once __DIR__ . '/../partials/debut.php';
?>
<?= pullFlash() ?>
<section class="card" data-table>
    <div class="toolbar">
        <input type="search" class="t-search" placeholder="Rechercher…" aria-label="Rechercher">
        <a class="btn btn-primary" href="ajouter_gouvernorat.php">+ Ajouter</a>
    </div>
    <div class="table-scroll">
        <table>
            <thead>
                <tr>
                    <th data-sort>Nom</th><th data-sort>Centres</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lignes as $ligne): ?>
                <tr>
                    <td><?= e(mb_strimwidth((string) ($ligne['name'] ?? ''), 0, 70, '…')) ?></td>
                    <td><?= (int) ($ligne['nb_centres'] ?? 0) ?></td>
                    <td class="actions">
                        <a class="btn btn-sm" href="modifier_gouvernorat.php?id=<?= (int) $ligne['id'] ?>">Modifier</a>
                        <form class="inline-form" method="POST" action="supprimer_gouvernorat.php" data-confirm="Supprimer cet élément ?">
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
