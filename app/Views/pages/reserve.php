<?php /** Attend : $centres, $selected, $errors */ ?>
<section class="wrap section narrow">
    <h1>Réserver un don</h1>
    <?php if ($errors): ?><div class="alert alert-error"><?php foreach ($errors as $er): ?><div><?= e($er) ?></div><?php endforeach; ?></div><?php endif; ?>
    <form class="card" method="POST" action="index.php?page=reserve">
        <?= csrf_field() ?>
        <label for="centre_id">Centre de collecte</label>
        <select id="centre_id" name="centre_id" required>
            <option value="">Choisir un centre</option>
            <?php foreach ($centres as $c): ?>
                <option value="<?= (int) $c['id'] ?>" <?= $selected === (int) $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?> (<?= e($c['gouvernorat']) ?>)</option>
            <?php endforeach; ?>
        </select>
        <label for="donation_date">Date souhaitée</label>
        <input type="date" id="donation_date" name="donation_date" min="<?= date('Y-m-d', strtotime('+1 day')) ?>" max="<?= date('Y-m-d', strtotime('+90 days')) ?>" required>
        <button class="btn btn-primary btn-block" type="submit">Confirmer la réservation</button>
    </form>
</section>
