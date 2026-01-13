<?php
declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
$path = rtrim($path, '/');
$path = $path === '' ? '/' : $path;

$routes = [
    '/' => __DIR__ . '/pages/landing.php',
    '/login' => __DIR__ . '/pages/login.php',
    '/dashboard' => __DIR__ . '/pages/dashboard/dashboard.php',
    '/documents' => __DIR__ . '/pages/documents/manage-documents.php',
    '/documents/download' => __DIR__ . '/pages/documents/document-download.php',
    '/admin' => __DIR__ . '/pages/admin/admin-area.php',
    '/logout' => __DIR__ . '/pages/logout.php',
];

if (!array_key_exists($path, $routes)) {
    if (preg_match('#^/integrations/([a-z0-9-]+)$#', $path, $matches)) {
        $integrationFile = __DIR__ . '/pages/integrations/' . $matches[1] . '.php';
        if (is_file($integrationFile)) {
            require $integrationFile;
            exit;
        }
    }

    http_response_code(404);
    echo 'Page not found.';
    exit;
}

require $routes[$path];
