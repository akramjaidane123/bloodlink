<?php /** Expects: $allUsers (UserModel[]) */ ?>
<div class="card">
    <h2>Tous les utilisateurs</h2>
    <table>
        <thead><tr><th>ID</th><th>Utilisateur</th><th>E-mail</th><th>Rôle</th><th>Créé le</th></tr></thead>
        <tbody>
        <?php foreach ($allUsers as $u): ?>
            <tr>
                <td><?= e($u->getId()) ?></td>
                <td><?= e($u->getUsername()) ?></td>
                <td><?= e($u->getEmail()) ?></td>
                <td><span class="badge <?= $u->getRole() === 'admin' ? 'badge-admin' : 'badge-user' ?>"><?= e(['admin' => 'Administrateur', 'centre' => 'Centre', 'user' => 'Donneur'][$u->getRole()] ?? $u->getRole()) ?></span></td>
                <td><?= e($u->getCreatedAt()) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
