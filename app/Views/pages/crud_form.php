<?php /** Attend : $cfg, $entity, $fields, $row, $id, $errors, $options */ ?>
<div class="card form-card">
    <h2><?= $id === null ? 'Ajouter' : 'Modifier' ?> : <?= e($cfg['label']) ?></h2>
    <?php if ($errors): ?><div class="alert alert-error"><?php foreach ($errors as $er): ?><div><?= e($er) ?></div><?php endforeach; ?></div><?php endif; ?>
    <form method="POST" action="index.php?page=admin-<?= e($entity) ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="id" value="<?= $id === null ? '' : (int) $id ?>">
        <?php foreach ($fields as $name => $f): $val = (string) ($row[$name] ?? ''); $req = $f['required'] ? 'required' : ''; ?>
            <label for="f_<?= e($name) ?>"><?= e($f['label']) ?><?= $f['required'] ? ' *' : '' ?></label>
            <?php if ($f['type'] === 'textarea'): ?>
                <textarea id="f_<?= e($name) ?>" name="<?= e($name) ?>" rows="5" <?= $req ?>><?= e($val) ?></textarea>
            <?php elseif ($f['type'] === 'fk'): ?>
                <select id="f_<?= e($name) ?>" name="<?= e($name) ?>" <?= $req ?>>
                    <option value=""><?= !empty($f['nullable']) ? '— Aucun —' : 'Choisir…' ?></option>
                    <?php foreach ($options[$name] as $o): ?><option value="<?= (int) $o['id'] ?>" <?= $val === (string) $o['id'] ? 'selected' : '' ?>><?= e($o['label']) ?></option><?php endforeach; ?>
                </select>
            <?php elseif ($f['type'] === 'enum'): ?>
                <select id="f_<?= e($name) ?>" name="<?= e($name) ?>" <?= $req ?>>
                    <option value="">Choisir…</option>
                    <?php foreach (($f['options'] ?? labels()[$f['group']]) as $k => $label): ?><option value="<?= e($k) ?>" <?= $val === (string) $k ? 'selected' : '' ?>><?= e($label) ?></option><?php endforeach; ?>
                </select>
            <?php else: ?>
                <input id="f_<?= e($name) ?>" name="<?= e($name) ?>" value="<?= e($val) ?>" <?= $req ?>
                    type="<?= in_array($f['type'], ['date', 'number', 'email'], true) ? e($f['type']) : 'text' ?>" <?= $f['type'] === 'number' ? 'min="0" step="any"' : '' ?>>
            <?php endif; ?>
        <?php endforeach; ?>
        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Enregistrer</button>
            <a class="btn" href="index.php?page=admin-<?= e($entity) ?>">Annuler</a>
        </div>
    </form>
</div>
