<?php
// Authentication helpers and DB connection
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
        ini_set('session.cookie_secure', 1);
    }
    session_start();
}

function get_db()
{
    static $pdo = null;
    if ($pdo) return $pdo;
    $cfg = require __DIR__ . '/../config/.env.php';
    $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', $cfg['db_host'], $cfg['db_name'], $cfg['db_charset']);
    $pdo = new PDO($dsn, $cfg['db_user'], $cfg['db_pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    return $pdo;
}

function login_user($username, $password)
{
    $db = get_db();
    $stmt = $db->prepare('SELECT * FROM users WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $u = $stmt->fetch();
    if ($u && password_verify($password, $u['password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $u['id'];
        return true;
    }
    return false;
}

function require_auth()
{
    if (empty($_SESSION['user_id'])) {
        header('Location: /login');
        exit;
    }
}

function current_user()
{
    if (empty($_SESSION['user_id'])) return null;
    $db = get_db();
    $stmt = $db->prepare('SELECT id,username,email,role FROM users WHERE id = ? LIMIT 1');
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

function is_admin()
{
    $u = current_user();
    return $u && ($u['role'] ?? '') === 'admin';
}

function require_admin()
{
    require_auth();
    if (!is_admin()) {
        http_response_code(403);
        echo 'Accès refusé.';
        exit;
    }
}

function logout()
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        setcookie(session_name(), '', time() - 42000, '/');
    }
    session_destroy();
}

function create_user($username, $email, $password, $role = 'user')
{
    $db = get_db();
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $db->prepare('INSERT INTO users (username, email, password, role, created_at) VALUES (?,?,?,?,NOW())');
    $stmt->execute([$username, $email, $hash, $role]);
    return $db->lastInsertId();
}
