<?php
/**
 * AuthController - ALL authentication business logic (security workshop):
 * register, login, logout, sessions, roles, CSRF.
 * Page actions (login(), register(), logout()) are called by public/index.php.
 */
class AuthController extends Controller
{
    private ?PDO $db = null;

    /** Lazy connection: pages that don't need the DB (GET forms) work without it. */
    private function db(): PDO
    {
        return $this->db ??= Database::getConnexion();
    }

    // ================================================================
    // SESSION STATE & ROLES
    // ================================================================

    public static function isAuthenticated(): bool
    {
        return !empty($_SESSION['user_id']);
    }

    public static function hasRole(string $role): bool
    {
        return self::isAuthenticated() && ($_SESSION['role'] ?? null) === $role;
    }

    public static function requireAuth(): void
    {
        if (!self::isAuthenticated()) {
            redirect('login');
        }
    }

    public static function requireStaff(): void
    {
        self::requireAuth();
        if (!in_array($_SESSION['role'] ?? '', ['admin', 'centre'], true)) {
            (new ErrorController())->forbidden();
            exit;
        }
    }

    /** Role is checked server-side on EVERY sensitive page. */
    public static function requireRole(string $role): void
    {
        self::requireAuth();
        if (($_SESSION['role'] ?? null) !== $role) {
            (new ErrorController())->forbidden();
            exit;
        }
    }

    // ================================================================
    // CSRF
    // ================================================================

    public static function validCsrf(?string $token): bool
    {
        return $token && !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    public static function getCsrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    private function verifyCsrfToken(?string $token): bool
    {
        if (!$token || empty($_SESSION['csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    // ================================================================
    // DATA ACCESS (prepared statements = SQL injection protection)
    // ================================================================

    private function existsByEmailOrUsername(string $email, string $username): bool
    {
        $stmt = $this->db()->prepare('SELECT id FROM users WHERE email = :email OR username = :username LIMIT 1');
        $stmt->execute(['email' => $email, 'username' => $username]);
        return (bool) $stmt->fetch();
    }

    private function insertUser(UserModel $user): bool
    {
        $stmt = $this->db()->prepare(
            'INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, :role)'
        );
        return $stmt->execute([
            'username' => $user->getUsername(),
            'email'    => $user->getEmail(),
            'password' => $user->getPassword(),
            'role'     => $user->getRole(),
        ]);
    }

    private function findByEmail(string $email): ?UserModel
    {
        $stmt = $this->db()->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch();
        return $row ? UserModel::fromArray($row) : null;
    }

    public function findById(int $id): ?UserModel
    {
        $stmt = $this->db()->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ? UserModel::fromArray($row) : null;
    }

    /** @return UserModel[] */
    public function getAllUsers(): array
    {
        $rows = $this->db()->query('SELECT * FROM users ORDER BY id ASC')->fetchAll();
        return array_map(fn(array $row) => UserModel::fromArray($row), $rows);
    }

    public function countByRole(string $role): int
    {
        $stmt = $this->db()->prepare('SELECT COUNT(*) FROM users WHERE role = :role');
        $stmt->execute(['role' => $role]);
        return (int) $stmt->fetchColumn();
    }

    public function countAllUsers(): int
    {
        return (int) $this->db()->query('SELECT COUNT(*) FROM users')->fetchColumn();
    }

    // ================================================================
    // REGISTER
    // ================================================================

    /** Validates and creates the account. Returns errors (empty array = success). */
    public function doRegister(array $post): array
    {
        $errors = [];

        if (!$this->verifyCsrfToken($post['csrf_token'] ?? null)) {
            return ['Jeton de sécurité invalide. Veuillez réessayer.'];
        }

        $username        = trim($post['username'] ?? '');
        $email           = trim($post['email'] ?? '');
        $password        = $post['password'] ?? '';
        $passwordConfirm = $post['password_confirm'] ?? '';

        if ($username === '' || $email === '' || $password === '') {
            $errors[] = 'Tous les champs sont obligatoires.';
        }
        if (!preg_match('/^[a-zA-Z0-9_\-]{3,50}$/', $username)) {
            $errors[] = 'Le nom doit contenir 3 à 50 caractères (lettres, chiffres, _ ou -).';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Adresse e-mail invalide.';
        }
        if (strlen($password) < 8) {
            $errors[] = 'Le mot de passe doit contenir au moins 8 caractères.';
        }
        if ($password !== $passwordConfirm) {
            $errors[] = 'Les mots de passe ne correspondent pas.';
        }
        if (!empty($errors)) {
            return $errors;
        }

        if ($this->existsByEmailOrUsername($email, $username)) {
            return ['Cet e-mail ou ce nom est déjà utilisé.'];
        }

        $user = new UserModel();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPassword(password_hash($password, PASSWORD_DEFAULT));
        $user->setRole('user'); // forced: no self-promotion to admin

        if (!$this->insertUser($user)) {
            $errors[] = 'Erreur lors de la création du compte.';
        }
        return $errors;
    }

    // ================================================================
    // LOGIN / LOGOUT
    // ================================================================

    /** Checks credentials and opens the session. Returns errors (empty = success). */
    public function doLogin(array $post): array
    {
        if (!$this->verifyCsrfToken($post['csrf_token'] ?? null)) {
            return ['Jeton de sécurité invalide. Veuillez réessayer.'];
        }

        $email    = trim($post['email'] ?? '');
        $password = $post['password'] ?? '';

        if ($email === '' || $password === '') {
            return ['Veuillez saisir votre e-mail et votre mot de passe.'];
        }

        $user = $this->findByEmail($email);

        // Deliberately generic message (never reveal whether the email exists)
        if (!$user || !password_verify($password, $user->getPassword())) {
            return ['E-mail ou mot de passe incorrect.'];
        }

        session_regenerate_id(true); // protects against session fixation

        $_SESSION['user_id']       = $user->getId();
        $_SESSION['username']      = $user->getUsername();
        $_SESSION['role']          = $user->getRole();
        $_SESSION['last_activity'] = time();

        return [];
    }

    public function doLogout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }

    // ================================================================
    // PAGE ACTIONS (called by the router)
    // ================================================================

    public function register(): void
    {
        $this->page  = 'register';
        $this->title = 'Inscription | BloodLink';
        $errors = [];
        $old    = [];

        if ($this->isPost()) {
            $errors = $this->doRegister($_POST);
            $old    = ['username' => $_POST['username'] ?? '', 'email' => $_POST['email'] ?? ''];
            if (empty($errors)) {
                header('Location: index.php?page=login&registered=1');
                exit;
            }
        }
        $this->render('register', compact('errors', 'old'));
    }

    public function login(): void
    {
        $this->page  = 'login';
        $this->title = 'Connexion | BloodLink';
        $errors = [];

        if ($this->isPost()) {
            $errors = $this->doLogin($_POST);
            if (empty($errors)) {
                redirect(homeFor((string) ($_SESSION['role'] ?? '')));
            }
        }
        $registered = !empty($_GET['registered']);
        $this->render('login', compact('errors', 'registered'));
    }

    /** Logout only via POST + CSRF (a plain link could be triggered by another site). */
    public function logout(): void
    {
        if ($this->isPost() && $this->verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->doLogout();
        }
        redirect('login');
    }
}
