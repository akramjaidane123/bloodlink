<?php
require_once __DIR__ . '/../../init.php';
require_once __DIR__ . '/../../Controllers/DashboardC.php';

AuthC::requireStaff();
$estAdmin = AuthC::hasRole('admin');
$scope = AuthC::centreScope(false);

$dc = new DashboardC();
$stats = $dc->statistiques($estAdmin, $scope);
$parStatut = $dc->reservationsParStatut($scope);
$parGroupe = $dc->besoinsParGroupe($scope);
$dernieres = $dc->dernieresReservations($scope);
$centresEnAttente = $estAdmin ? $dc->centresEnAttente() : [];

$titre = 'Tableau de bord | BloodLink Admin';
require_once __DIR__ . '/partials/debut.php';
?>
<?= pullFlash() ?>
<?php if (!$estAdmin && $scope === null): ?>
    <div class="alert alert-error">Votre compte n'est rattaché à aucun centre : demandez à l'administrateur de l'associer à votre centre (Comptes &rarr; Modifier).</div>
<?php endif; ?>

<section class="stats-grid">
    <?php foreach ($stats as [$libelle, $valeur, $ton]): ?>
        <div class="stat-card">
            <div class="label"><?= e($libelle) ?></div>
            <div class="value <?= e($ton) ?>"><?= (int) $valeur ?></div>
        </div>
    <?php endforeach; ?>
</section>

<section class="two-cols">
    <div class="card">
        <h2>Besoins par groupe sanguin (poches)</h2>
        <?php $max = max(array_column($parGroupe, 'total') ?: [1]); ?>
        <?php foreach ($parGroupe as $g): ?>
            <div class="bar-row">
                <span><?= e($g['blood_group']) ?></span>
                <div class="bar"><i style="width:<?= (int) round($g['total'] / $max * 100) ?>%"></i></div>
                <b><?= (int) $g['total'] ?></b>
            </div>
        <?php endforeach; ?>
        <?php if (!$parGroupe): ?><p class="muted">Aucune donnée.</p><?php endif; ?>
    </div>
    <div class="card">
        <h2>Réservations par statut</h2>
        <?php $max = max(array_column($parStatut, 'total') ?: [1]); ?>
        <?php foreach ($parStatut as $s): ?>
            <div class="bar-row">
                <span><?= badge('reservation', $s['status']) ?></span>
                <div class="bar"><i style="width:<?= (int) round($s['total'] / $max * 100) ?>%"></i></div>
                <b><?= (int) $s['total'] ?></b>
            </div>
        <?php endforeach; ?>
        <?php if (!$parStatut): ?><p class="muted">Aucune donnée.</p><?php endif; ?>
    </div>
</section>

<section class="card">
    <h2>Dernières réservations</h2>
    <div class="table-scroll">
        <table>
            <thead><tr><th>Donneur</th><th>Centre</th><th>Date</th><th>Statut</th></tr></thead>
            <tbody>
                <?php foreach ($dernieres as $r): ?>
                    <tr>
                        <td><?= e($r['donor']) ?></td>
                        <td><?= e($r['centre']) ?></td>
                        <td><?= e(dateFr($r['donation_date'])) ?></td>
                        <td><?= badge('reservation', $r['status']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php if (!$dernieres): ?><p class="muted">Aucune réservation.</p><?php endif; ?>
</section>

<?php if ($centresEnAttente): ?>
<section class="card">
    <h2>Centres à valider</h2>
    <?php foreach ($centresEnAttente as $c): ?>
        <p>
            <?= e($c['name']) ?>
            <a class="btn btn-sm" href="<?= e(back('centres/modifier_centre.php?id=' . (int) $c['id'])) ?>">Examiner</a>
        </p>
    <?php endforeach; ?>
</section>
<?php endif; ?>
<?php require_once __DIR__ . '/partials/fin.php'; ?>
