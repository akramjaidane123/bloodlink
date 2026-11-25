<?php /** Attend : $cfg, $entity, $rows, $canCreate */ ?>
<?= pullFlash() ?>
<div class="card" data-table>
    <div class="toolbar">
        <input type="search" class="t-search" placeholder="Rechercher…" aria-label="Rechercher">
        <?php if ($canCreate): ?><a class="btn btn-primary" href="index.php?page=admin-<?= e($entity) ?>&amp;action=new">+ Ajouter</a><?php endif; ?>
    </div>
    <div class="table-scroll">
    <table>
        <thead><tr>
            <?php foreach ($cfg['list'] as $label): ?><th data-sort><?= e($label) ?></th><?php endforeach; ?>
            <th>Actions</th>
        </tr></thead>
        <tbody>
        <?php foreach ($rows as $row): ?>
            <tr>
                <?php foreach ($cfg['list'] as $col => $label): $v = $row[$col] ?? ''; ?>
                    <td><?php
                        if (isset($cfg['badges'][$col])) { echo badge($cfg['badges'][$col], $v); }
                        elseif (preg_match('/^\d{4}-\d{2}-\d{2}/', (string) $v)) { echo e(dateFr((string) $v)); }
                        else { echo e(mb_strimwidth((string) $v, 0, 70, '…')); }
                    ?></td>
                <?php endforeach; ?>
                <td class="actions">
                    <a class="btn btn-sm" href="index.php?page=admin-<?= e($entity) ?>&amp;action=edit&amp;id=<?= (int) $row['id'] ?>">Modifier</a>
                    <form class="inline-form" method="POST" action="index.php?page=admin-<?= e($entity) ?>" data-confirm="Supprimer cet élément ?">
                        <?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                        <button class="btn btn-sm btn-danger" type="submit">Supprimer</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?php if (!$rows): ?><p class="muted">Aucun élément.</p><?php endif; ?>
    <div class="pager"></div>
</div>
