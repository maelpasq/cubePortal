<?php
declare(strict_types=1);

function current_user(PDO $pdo): ?array
{
    if (empty($_SESSION['user_id'])) {
        return null;
    }

    $stmt = $pdo->prepare('SELECT id, email, name, role FROM users WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $_SESSION['user_id']]);
    $user = $stmt->fetch();

    return $user ?: null;
}

function require_auth(PDO $pdo): array
{
    $user = current_user($pdo);
    if (!$user) {
        header('Location: /connexion');
        exit;
    }
    return $user;
}

function require_admin(PDO $pdo): array
{
    $user = require_auth($pdo);
    if ($user['role'] !== 'admin') {
        header('Location: /dashboard');
        exit;
    }
    return $user;
}

function login_attempt_allowed(): bool
{
    $now = time();
    $window = 60; // seconds
    $maxAttempts = 5;

    $_SESSION['login_attempts'] = $_SESSION['login_attempts'] ?? [];
    $_SESSION['login_attempts'] = array_filter(
        $_SESSION['login_attempts'],
        static fn ($t) => ($now - $t) < $window
    );

    return count($_SESSION['login_attempts']) < $maxAttempts;
}

function record_login_attempt(): void
{
    $_SESSION['login_attempts'][] = time();
}
