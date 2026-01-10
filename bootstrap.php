<?php
declare(strict_types=1);

// Basic hardening headers for all responses.
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');

// Session settings before session_start.
ini_set('session.use_strict_mode', '1');
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Lax');
if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
    ini_set('session.cookie_secure', '1');
}

session_start();

require_once __DIR__ . '/lib/csrf.php';
require_once __DIR__ . '/lib/auth.php';
require_once __DIR__ . '/lib/helpers.php';

$configPath = __DIR__ . '/.config.php';
if (!file_exists($configPath)) {
    http_response_code(500);
    echo 'Config missing. Create .config.php first.';
    exit;
}

$config = require $configPath;
$debug = !empty($config['debug']);
try {
    $pdo = new PDO(
        $config['db']['dsn'],
        $config['db']['user'],
        $config['db']['pass'],
        $config['db']['options'] ?? []
    );
} catch (Throwable $e) {
    http_response_code(500);
    if ($debug) {
        echo 'Database connection failed: ' . $e->getMessage();
    } else {
        echo 'Database connection failed.';
    }
    exit;
}
