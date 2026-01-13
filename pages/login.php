<?php
$title = 'Cube Portal - Connexion';
$bodyClass = '';
$useTailwind = true;
$loginImage = '/assets/img/login-image-v1.png';

if (current_user($pdo)) {
    header('Location: /dashboard');
    exit;
}

if (isset($_GET['inactive'])) {
    $error = 'Votre compte est désactivé. Contactez un administrateur.';
}

if (is_post()) {
    if (!csrf_verify($_POST['csrf_token'] ?? null)) {
    $error = 'Jeton de sécurité invalide.';
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
        } elseif ($user && (int)$user['is_active'] !== 1) {
            $error = 'Votre compte est désactivé. Contactez un administrateur.';
        } else {
            record_login_attempt();
            $error = 'Identifiants invalides.';
        }
    }
}

require __DIR__ . '/../templates/head.php';
?>
<div class="min-h-screen w-full bg-[#f9f6f2] text-[#0f0f0f]">
    <div class="grid min-h-screen w-full grid-cols-1 lg:grid-cols-2">
        <div class="flex items-center justify-center px-6 py-12 sm:px-12 lg:px-16">
            <div class="w-full max-w-md">
                <div class="mb-8">
                    <p class="text-xs uppercase tracking-[0.4em] text-[#6d6258]">Cube Portal</p>
                    <h1 class="mt-3 text-4xl font-semibold leading-tight text-[#0f0f0f]">Connexion</h1>
                    <p class="mt-3 text-sm text-[#6d6258]">Accedez a votre espace securise et gerez les projets Cube.</p>
                </div>
                <?php if (!empty($error)): ?>
                    <div class="mb-6 rounded-2xl border border-[#f2b1b1] bg-[#ffe5e5] px-4 py-3 text-sm">
                        <?= e($error) ?>
                    </div>
                <?php endif; ?>
                <form method="post" class="space-y-4">
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <label class="block text-sm font-medium text-[#2b2723]">
                        Email
                        <input type="email" name="email" required autocomplete="email"
                               class="mt-2 w-full rounded-2xl border border-[#e3d7cc] bg-white px-4 py-3 text-sm focus:border-[#1f2d3a] focus:outline-none">
                    </label>
                    <label class="block text-sm font-medium text-[#2b2723]">
                        Mot de passe
                        <input type="password" name="password" required autocomplete="current-password"
                               class="mt-2 w-full rounded-2xl border border-[#e3d7cc] bg-white px-4 py-3 text-sm focus:border-[#1f2d3a] focus:outline-none">
                    </label>
                    <button class="w-full rounded-full bg-[#1f2d3a] px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-[#1f2d3a]/30" type="submit">
                        Se connecter
                    </button>
                </form>
                <div class="mt-6 text-sm text-[#6d6258]">
                    <a class="font-medium text-[#1f2d3a]" href="/">Retour a l'accueil</a>
                </div>
            </div>
        </div>
        
        <div class="relative hidden lg:block">
            <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('<?= e($loginImage) ?>');"></div>
            <div class="absolute inset-0 bg-gradient-to-tr from-[#0f0f0f]/60 via-transparent to-transparent"></div>
            <div class="absolute bottom-10 left-10 max-w-sm text-white">
                <p class="text-xs uppercase tracking-[0.3em] text-white/70">SaaS interne CESI</p>
                <h2 class="mt-3 text-3xl font-semibold">Centralisez les infos des projets Cube.</h2>
            </div>
        </div>
    </div>
</div>
<?php require __DIR__ . '/../templates/footer.php'; ?>
