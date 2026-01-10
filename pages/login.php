<?php
$title = 'Cube Portal - Connexion';
$bodyClass = 'auth';

if (is_post()) {
    if (!csrf_verify($_POST['csrf_token'] ?? null)) {
        $error = 'Jeton de securite invalide.';
    } elseif (!login_attempt_allowed()) {
        $error = 'Trop de tentatives. Reessayez dans une minute.';
    } else {
        $email = trim((string)($_POST['email'] ?? ''));
        $password = (string)($_POST['password'] ?? '');

        $stmt = $pdo->prepare('SELECT id, email, name, password_hash, role, is_active FROM users WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if ($user && (int)$user['is_active'] === 1 && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            unset($_SESSION['login_attempts']);

            $update = $pdo->prepare('UPDATE users SET last_login_at = NOW() WHERE id = :id');
            $update->execute([':id' => $user['id']]);

            header('Location: /dashboard');
            exit;
        }

        record_login_attempt();
        $error = 'Identifiants invalides.';
    }
}

require __DIR__ . '/../templates/head.php';
?>
<div class="auth__container">
    <div class="auth__card">
        <div class="auth__header">
            <div class="logo">Cube Portal</div>
            <p>Connexion a votre espace securise</p>
        </div>
        <?php if (!empty($error)): ?>
            <div class="alert alert--error"><?= e($error) ?></div>
        <?php endif; ?>
        <form method="post" class="form">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <label class="field">
                <span>Email</span>
                <input type="email" name="email" required autocomplete="email">
            </label>
            <label class="field">
                <span>Mot de passe</span>
                <input type="password" name="password" required autocomplete="current-password">
            </label>
            <button class="btn btn--primary" type="submit">Se connecter</button>
        </form>
        <div class="auth__footer">
            <a href="/">Retour a l'accueil</a>
        </div>
    </div>
</div>
<?php require __DIR__ . '/../templates/footer.php'; ?>
