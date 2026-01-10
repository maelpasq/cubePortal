<?php
declare(strict_types=1);

$configPath = __DIR__ . '/../config/.env.php';
if (!file_exists($configPath)) {
    $configPath = __DIR__ . '/../config/.env.example.php';
}

$GLOBALS['cube_config'] = require $configPath;
$GLOBALS['cube_base_url'] = rtrim($GLOBALS['cube_config']['app']['base_url'] ?? '/', '/') ?: '/';

$secureCookie = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['SERVER_PORT'] ?? '') === '443');
session_set_cookie_params([
    'lifetime' => 0,
    'path' => $GLOBALS['cube_base_url'] === '/' ? '/' : $GLOBALS['cube_base_url'],
    'domain' => '',
    'secure' => $secureCookie,
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_name('cube_portal_sess');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data:; connect-src 'self';");

function config_value(string $path, $default = null)
{
    $config = $GLOBALS['cube_config'] ?? [];
    $segments = explode('.', $path);
    foreach ($segments as $segment) {
        if (!is_array($config) || !array_key_exists($segment, $config)) {
            return $default;
        }
        $config = $config[$segment];
    }
    return $config;
}

function base_url(string $path = ''): string
{
    $base = $GLOBALS['cube_base_url'] ?? '/';
    $clean = ltrim($path, '/');
    if ($base === '/') {
        return '/' . $clean;
    }
    return rtrim($base, '/') . '/' . $clean;
}

function db(): PDO
{
    static $pdo;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = config_value('db.dsn');
    $user = config_value('db.user');
    $password = config_value('db.password');

    try {
        $pdo = new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo '<h1>Connexion base de donnees indisponible</h1>';
        echo '<p>Verifiez config/.env.php et importez database.sql.</p>';
        exit;
    }

    return $pdo;
}

function ensure_admin_seed(): void
{
    try {
        $count = (int) db()->query('SELECT COUNT(*) FROM users')->fetchColumn();
    } catch (PDOException $e) {
        if ($e->getCode() === '42S02') {
            http_response_code(500);
            echo '<h1>Base non initialisee</h1>';
            echo '<p>Importez database.sql via phpMyAdmin puis rechargez la page.</p>';
            exit;
        }
        throw $e;
    }

    if ($count > 0) {
        return;
    }

    $email = config_value('security.initial_admin_email', 'admin@cube-portal.local');
    $password = config_value('security.initial_admin_password', 'ChangeMe123!');
    $name = config_value('security.initial_admin_name', 'Admin Cube');

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = db()->prepare('INSERT INTO users (name, email, password_hash, role) VALUES (:name, :email, :password_hash, :role)');
    $stmt->execute([
        ':name' => $name,
        ':email' => $email,
        ':password_hash' => $hash,
        ':role' => 'admin',
    ]);
}

function enforce_session_controls(): void
{
    $now = time();
    $timeout = 60 * 30;
    $fingerprint = hash('sha256', ($_SERVER['REMOTE_ADDR'] ?? '') . ($_SERVER['HTTP_USER_AGENT'] ?? '') . config_value('security.app_key', 'cube-portal'));

    if (isset($_SESSION['last_active']) && ($now - (int) $_SESSION['last_active']) > $timeout) {
        logout_user();
        redirect('login?timeout=1');
    }

    if (isset($_SESSION['fingerprint']) && !hash_equals($_SESSION['fingerprint'], $fingerprint)) {
        logout_user();
        redirect('login');
    }

    $_SESSION['fingerprint'] = $fingerprint;
    $_SESSION['last_active'] = $now;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf(?string $token): bool
{
    if (!$token) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): void
{
    header('Location: ' . base_url($path));
    exit;
}

function set_flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function flash_messages(): array
{
    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $messages;
}

function current_user(): ?array
{
    static $user;
    if ($user !== null) {
        return $user;
    }

    if (empty($_SESSION['user_id'])) {
        return $user = null;
    }

    try {
        $stmt = db()->prepare('SELECT id, name, email, role, created_at FROM users WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $_SESSION['user_id']]);
        $found = $stmt->fetch();
    } catch (PDOException $e) {
        return $user = null;
    }

    if (!$found) {
        logout_user();
        return $user = null;
    }

    return $user = $found;
}

function require_auth(): void
{
    if (!current_user()) {
        set_flash('warning', 'Connectez-vous pour acceder a cette page.');
        redirect('login');
    }
}

function require_admin(): void
{
    $user = current_user();
    if (!$user || $user['role'] !== 'admin') {
        http_response_code(403);
        render('error', [
            'title' => 'Acces refuse',
            'message' => "Vous n'avez pas les droits suffisants pour cette zone.",
        ]);
        exit;
    }
}

function logout_user(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}

function render(string $view, array $data = []): void
{
    $viewFile = __DIR__ . '/../views/' . $view . '.php';
    if (!file_exists($viewFile)) {
        http_response_code(404);
        echo 'Vue introuvable';
        exit;
    }

    $user = current_user();
    $flashes = flash_messages();
    $viewName = $view;

    extract($data, EXTR_OVERWRITE);
    ob_start();
    include $viewFile;
    $content = ob_get_clean();
    include __DIR__ . '/../views/layout.php';
}
