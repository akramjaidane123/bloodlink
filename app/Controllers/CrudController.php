<?php
/**
 * Contrôleur CRUD générique du back office, piloté par app/config/entities.php.
 * Chaque page de module étend cette classe en indiquant seulement sa clé $entity.
 * Sécurité : colonnes en liste blanche, requêtes préparées, CSRF, rôle vérifié côté serveur,
 * un utilisateur "centre" est limité aux lignes de son propre centre.
 */
abstract class CrudController extends Controller
{
    protected string $layout = 'admin';
    protected string $entity = '';
    private array $cfg = [];
    private ?int $scopeId = null;

    public function index(): void
    {
        $this->cfg   = entities()[$this->entity];
        $this->page  = 'admin-' . $this->entity;
        $this->title = $this->cfg['label'] . ' | BloodLink Admin';

        if ($this->cfg['access'] === 'admin') {
            AuthController::requireRole('admin');
        }
        if (($_SESSION['role'] ?? '') === 'centre' && !empty($this->cfg['scope'])) {
            $this->scopeId = Model::userCentre((int) $_SESSION['user_id']);
            if ($this->scopeId === null) {
                flash('error', "Votre compte n'est rattaché à aucun centre.");
                redirect('admin');
            }
        }

        if ($this->isPost()) {
            if (!AuthController::validCsrf($_POST['csrf_token'] ?? null)) {
                flash('error', 'Jeton de sécurité invalide. Veuillez réessayer.');
                $this->back();
            }
            ($_POST['action'] ?? '') === 'delete' ? $this->delete() : $this->save();
            return;
        }

        match ($_GET['action'] ?? 'list') {
            'new'   => $this->form(null),
            'edit'  => $this->form((int) ($_GET['id'] ?? 0)),
            default => $this->listing(),
        };
    }

    private function back(): never
    {
        header('Location: index.php?page=admin-' . $this->entity);
        exit;
    }

    private function scopeSql(string $alias = 't'): array
    {
        $col = $this->cfg['scope'] ?? null;
        return ($this->scopeId && $col) ? [" AND $alias.$col = :scope", ['scope' => $this->scopeId]] : ['', []];
    }

    private function listing(): void
    {
        [$where, $params] = $this->scopeSql();
        $c = $this->cfg;
        $rows = Model::all("SELECT {$c['select']} FROM {$c['table']} t {$c['join']} WHERE 1=1 $where ORDER BY {$c['order']}", $params);
        $this->render('crud_list', ['cfg' => $c, 'entity' => $this->entity, 'rows' => $rows, 'canCreate' => $c['create'] ?? true]);
    }

    /** Champs visibles dans le formulaire (le centre d'un compte "centre" est imposé). */
    private function fields(): array
    {
        return array_filter($this->cfg['fields'], fn($f) => !($this->scopeId && !empty($f['scope'])));
    }

    private function form(?int $id, array $errors = [], array $old = []): void
    {
        $c = $this->cfg;
        $row = [];
        if ($id !== null) {
            [$where, $params] = $this->scopeSql();
            $row = Model::one("SELECT t.* FROM {$c['table']} t WHERE t.id = :id $where", ['id' => $id] + $params);
            if (!$row) {
                (new ErrorController())->notFound();
                exit;
            }
        } elseif (!($c['create'] ?? true)) {
            $this->back();
        }
        $options = [];
        foreach ($this->fields() as $name => $f) {
            if ($f['type'] === 'fk') {
                $options[$name] = Model::all("SELECT id, {$f['label_col']} AS label FROM {$f['table']} ORDER BY label");
            }
        }
        $this->render('crud_form', ['cfg' => $c, 'entity' => $this->entity, 'fields' => $this->fields(), 'row' => $old ?: $row,
            'id' => $id, 'errors' => $errors, 'options' => $options]);
    }

    private function save(): void
    {
        $c = $this->cfg;
        $id = ($_POST['id'] ?? '') !== '' ? (int) $_POST['id'] : null;
        if ($id === null && !($c['create'] ?? true)) {
            $this->back();
        }
        $values = [];
        $errors = [];
        foreach ($this->fields() as $name => $f) {
            $v = trim((string) ($_POST[$name] ?? ''));
            if ($v === '') {
                if ($f['required']) { $errors[] = $f['label'] . ' est obligatoire.'; }
                $values[$name] = null;
                continue;
            }
            $ok = match ($f['type']) {
                'number' => is_numeric($v) && $v >= 0,
                'date'   => ($d = DateTime::createFromFormat('Y-m-d', $v)) && $d->format('Y-m-d') === $v,
                'email'  => (bool) filter_var($v, FILTER_VALIDATE_EMAIL),
                'fk'     => ctype_digit($v),
                'enum'   => array_key_exists($v, $f['options'] ?? (labels()[$f['group'] ?? ''] ?? [])),
                default  => mb_strlen($v) <= 5000,
            };
            if (!$ok) { $errors[] = $f['label'] . ' : valeur invalide.'; }
            $values[$name] = $v;
        }
        if (isset($values['start_date'], $values['end_date']) && $values['start_date'] > $values['end_date']) {
            $errors[] = 'La date de fin doit suivre la date de début.';
        }
        if ($errors) {
            $this->form($id, $errors, $values + ['id' => $id]);
            return;
        }
        if ($this->scopeId && !empty($c['scope'])) {
            $values[$c['scope']] = $this->scopeId;
        }
        try {
            if ($id === null) {
                if (!empty($c['auto_user'])) { $values[$c['auto_user']] = (int) $_SESSION['user_id']; }
                $cols = array_keys($values);
                Model::run("INSERT INTO {$c['table']} (" . implode(',', $cols) . ') VALUES (:' . implode(',:', $cols) . ')', $values);
            } else {
                [$where, $params] = $this->scopeSql('');
                $where = str_replace(' AND .', ' AND ', $where);
                $set = implode(', ', array_map(fn($k) => "$k = :$k", array_keys($values)));
                Model::run("UPDATE {$c['table']} SET $set WHERE id = :id $where", $values + ['id' => $id] + $params);
            }
            flash('ok', 'Enregistrement effectué.');
        } catch (PDOException $e) {
            error_log($e->getMessage());
            flash('error', 'Enregistrement impossible (doublon ou valeur invalide).');
        }
        $this->back();
    }

    private function delete(): void
    {
        [$where, $params] = $this->scopeSql('');
        $where = str_replace(' AND .', ' AND ', $where);
        try {
            Model::run("DELETE FROM {$this->cfg['table']} WHERE id = :id $where", ['id' => (int) ($_POST['id'] ?? 0)] + $params);
            flash('ok', 'Élément supprimé.');
        } catch (PDOException $e) {
            flash('error', 'Suppression impossible : des données liées existent.');
        }
        $this->back();
    }
}
