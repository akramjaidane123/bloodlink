# Contribuer à BloodLink

## Branches
- `main` : version stable (protégée), `develop` : intégration.
- `feature/<module>-<description>`, `fix/<description>`, `docs/<description>`.

## Commits (Conventional Commits)
`feat: ajoute la page des collectes` · `fix: corrige le lien actif de la sidebar` · `docs: met à jour le README`

## Pull Requests
Une PR par fonctionnalité, relue par au moins un membre, liée à une issue (`Closes #12`).

## Ajouter une page de module
1. Table(s) avec clé étrangère dans `database/bloodlink.sql`.
2. Page de gestion (back office) : une entrée dans `app/config/entities.php` + un contrôleur `Admin<Nom>Controller extends CrudController`.
   Page publique : `app/Models/X.php` et `app/Controllers/XController.php` (étend `Controller`, appelle `$this->render('x', $data)`).
3. `app/Views/pages/x.php` : contenu HTML uniquement, valeurs échappées avec `e()`, formulaires avec `<?= csrf_field() ?>`.
4. Une ligne dans `app/routes.php`, un lien dans `partials/navbar.php` ou `partials/admin/sidebar.php`.

## Suivi
Chaque tâche est une issue ajoutée au GitHub Project (To do, In progress, Review, Done).
