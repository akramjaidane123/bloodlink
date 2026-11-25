<?php /** Attend : $stats, $byStatus, $byGroup, $recent, $pendingCentres */ ?>
<?= pullFlash() ?>
<div class="stats-grid">
    <?php foreach ($stats as [$label, $value, $tone]): ?>
        <div class="stat-card"><div class="label"><?= e($label) ?></div><div class="value <?= e($tone) ?>"><?= e($value) ?></div></div>
    <?php endforeach; ?>
</div>
<div class="two-cols">
    <div class="card"><h2>Besoins par groupe sanguin (poches)</h2>
        <?php $max = max(array_column($byGroup, 'total') ?: [1]); ?>
        <?php foreach ($byGroup as $g): ?>
            <div class="bar-row"><span><?= e($g['blood_group']) ?></span><div class="bar"><i style="width:<?= (int) round($g['total'] / $max * 100) ?>%"></i></div><b><?= (int) $g['total'] ?></b></div>
        <?php endforeach; ?>
        <?php if (!$byGroup): ?><p class="muted">Aucune donnée.</p><?php endif; ?>
    </div>
    <div class="card"><h2>Réservations par statut</h2>
        <?php $max = max(array_column($byStatus, 'total') ?: [1]); ?>
        <?php foreach ($byStatus as $s): ?>
            <div class="bar-row"><span><?= badge('reservation', $s['status']) ?></span><div class="bar"><i style="width:<?= (int) round($s['total'] / $max * 100) ?>%"></i></div><b><?= (int) $s['total'] ?></b></div>
        <?php endforeach; ?>
        <?php if (!$byStatus): ?><p class="muted">Aucune donnée.</p><?php endif; ?>
    </div>
</div>
<div class="card"><h2>Dernières réservations</h2>
    <table><thead><tr><th>Donneur</th><th>Centre</th><th>Date</th><th>Statut</th></tr></thead><tbody>
    <?php foreach ($recent as $r): ?>
        <tr><td><?= e($r['donor']) ?></td><td><?= e($r['centre']) ?></td><td><?= dateFr($r['donation_date']) ?></td><td><?= badge('reservation', $r['status']) ?></td></tr>
    <?php endforeach; ?>
    </tbody></table>
</div>
<?php if ($pendingCentres): ?>
<div class="card"><h2>Centres à valider</h2>
    <?php foreach ($pendingCentres as $c): ?>
        <p><?= e($c['name']) ?> <a class="btn btn-sm" href="index.php?page=admin-centres&amp;action=edit&amp;id=<?= (int) $c['id'] ?>">Examiner</a></p>
    <?php endforeach; ?>
</div>
<?php endif; ?>
