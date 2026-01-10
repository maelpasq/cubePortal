<?php
declare(strict_types=1);

require __DIR__ . '/app/core.php';

$path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

function handle_login(string $method): void
{
    ensure_admin_seed();

    if ($method === 'POST') {
        if (!verify_csrf($_POST['csrf_token'] ?? null)) {
            render('login', ['error' => 'Requete invalide, merci de reessayer.']);
            return;
        }

        $email = strtolower(trim($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';

        if (!$email || !$password) {
            render('login', ['error' => 'Email et mot de passe obligatoires.']);
            return;
        }

        $stmt = db()->prepare('SELECT id, name, email, password_hash, role FROM users WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);
        $account = $stmt->fetch();

        if (!$account || !password_verify($password, $account['password_hash'])) {
            render('login', ['error' => 'Identifiants incorrects.']);
            return;
        }

        session_regenerate_id(true);
        $_SESSION['user_id'] = $account['id'];
        $_SESSION['role'] = $account['role'];
        $_SESSION['last_active'] = time();
        set_flash('success', 'Bienvenue ' . $account['name']);
        redirect('dashboard');
    }

    render('login');
}

function dashboard_page(): void
{
    require_auth();
    enforce_session_controls();

    $stats = [
        'user_count' => (int) db()->query('SELECT COUNT(*) FROM users')->fetchColumn(),
        'admin_count' => (int) db()->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn(),
    ];

    $recent = db()->query('SELECT name, email, role, created_at FROM users ORDER BY created_at DESC LIMIT 5')->fetchAll();
    $stats['recent_users'] = $recent ?: [];

    render('dashboard', ['stats' => $stats]);
}

function admin_page(string $method): void
{
    require_admin();
    enforce_session_controls();

    $formError = null;
    $formValues = [];

    if ($method === 'POST') {
        if (!verify_csrf($_POST['csrf_token'] ?? null)) {
            $formError = 'Token CSRF invalide.';
        } else {
            $formValues = [
                'name' => trim($_POST['name'] ?? ''),
                'email' => strtolower(trim($_POST['email'] ?? '')),
                'role' => $_POST['role'] === 'admin' ? 'admin' : 'member',
            ];
            $password = $_POST['password'] ?? '';

            if (!$formValues['name'] || !$formValues['email'] || !$password) {
                $formError = 'Tous les champs sont requis.';
            } elseif (!filter_var($formValues['email'], FILTER_VALIDATE_EMAIL)) {
                $formError = 'Email invalide.';
            } elseif (strlen($password) < 8) {
                $formError = 'Le mot de passe doit faire au moins 8 caracteres.';
            } else {
                try {
                    $stmt = db()->prepare('INSERT INTO users (name, email, password_hash, role) VALUES (:name, :email, :password_hash, :role)');
                    $stmt->execute([
                        ':name' => $formValues['name'],
                        ':email' => $formValues['email'],
                        ':password_hash' => password_hash($password, PASSWORD_DEFAULT),
                        ':role' => $formValues['role'],
                    ]);
                    set_flash('success', 'Compte cree avec succes.');
                    redirect('admin');
                } catch (PDOException $e) {
                    if ($e->getCode() === '23000') {
                        $formError = 'Un compte existe deja avec cet email.';
                    } else {
                        $formError = 'Erreur lors de la creation du compte.';
                    }
                }
            }
        }
    }

    $users = db()->query('SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC')->fetchAll() ?: [];

    render('admin', [
        'users' => $users,
        'formError' => $formError,
        'formValues' => $formValues,
    ]);
}

// Landing page
if ($path === '') {
    render('landing');
    exit;
}

// Authentication routes
if ($path === 'login') {
    handle_login($method);
    exit;
}

if ($path === 'logout') {
    logout_user();
    redirect('');
}

// Protected routes
if ($path === 'dashboard') {
    ensure_admin_seed();
    dashboard_page();
    exit;
}

if ($path === 'admin') {
    ensure_admin_seed();
    admin_page($method);
    exit;
}

http_response_code(404);
render('error', [
    'title' => 'Page introuvable',
    'message' => 'La page demandee nest pas disponible.',
]);
