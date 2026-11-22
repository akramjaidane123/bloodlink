<?php /** Attend : $article, $comments, $error */ ?>
<section class="wrap section narrow">
    <?= pullFlash() ?>
    <span class="badge badge-user"><?= e($article['category']) ?></span>
    <h1><?= e($article['title']) ?></h1>
    <p class="muted">Publié le <?= dateFr($article['created_at']) ?><?= $article['author'] ? ' par ' . e($article['author']) : '' ?></p>
    <div class="prose"><?= nl2br(e($article['content'])) ?></div>

    <h2>Commentaires (<?= count($comments) ?>)</h2>
    <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
    <?php if (AuthController::isAuthenticated()): ?>
        <form method="POST" action="index.php?page=article&amp;id=<?= (int) $article['id'] ?>">
            <?= csrf_field() ?>
            <label for="content">Votre commentaire</label>
            <textarea id="content" name="content" rows="3" maxlength="1000" required></textarea>
            <button class="btn btn-primary" type="submit">Publier</button>
        </form>
    <?php else: ?>
        <p><a href="index.php?page=login">Connectez-vous</a> pour commenter.</p>
    <?php endif; ?>
    <?php foreach ($comments as $m): ?>
        <div class="card comment"><strong><?= e($m['username']) ?></strong> <span class="muted"><?= dateFr($m['created_at']) ?></span>
            <p><?= nl2br(e($m['content'])) ?></p></div>
    <?php endforeach; ?>
</section>
