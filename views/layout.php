<?php
$pageTitle = $title ?? 'Cube Portal';
$isAuthenticated = (bool) $user;
$isDashboardShell = $isAuthenticated && in_array($viewName, ['dashboard', 'admin'], true);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body class="<?= $isAuthenticated ? 'app-mode' : 'marketing' ?>">
<?php if ($isDashboardShell): ?>
    <div class="app-shell">
        <aside class="sidebar">
            <div class="brand">
                <div class="brand-mark">CP</div>
                <div>
                    <div class="brand-title">Cube Portal</div>
                    <div class="brand-subtitle">CESI Projects</div>
                </div>
            </div>
            <nav class="side-nav">
                <a class="side-link <?= $viewName === 'dashboard' ? 'active' : '' ?>" href="<?= base_url('dashboard') ?>">Tableau de bord</a>
                <?php if ($user && $user['role'] === 'admin'): ?>
                    <a class="side-link <?= $viewName === 'admin' ? 'active' : '' ?>" href="<?= base_url('admin') ?>">Espace admin</a>
                <?php endif; ?>
                <a class="side-link" href="<?= base_url('') ?>">Accueil public</a>
            </nav>
            <div class="side-footer">
                <div class="user-card">
                    <div class="avatar"><?= strtoupper(substr($user['name'], 0, 1)) ?></div>
                    <div>
                        <div class="user-name"><?= e($user['name']) ?></div>
                        <div class="user-role"><?= e($user['role']) ?></div>
                    </div>
                </div>
                <a class="side-link danger" href="<?= base_url('logout') ?>">Se deconnecter</a>
            </div>
        </aside>
        <div class="app-content">
            <header class="app-topbar">
                <div>
                    <p class="eyebrow">Connecte</p>
                    <h1 class="page-title"><?= e($pageTitle) ?></h1>
                </div>
                <div class="top-actions">
                    <a class="ghost-btn" href="<?= base_url('dashboard') ?>">Vue globale</a>
                    <?php if ($user && $user['role'] === 'admin'): ?>
                        <a class="primary-btn" href="<?= base_url('admin') ?>">Admin</a>
                    <?php endif; ?>
                </div>
            </header>
            <main class="main-area">
                <?php foreach ($flashes as $flash): ?>
                    <div class="flash <?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
                <?php endforeach; ?>
                <?= $content ?>
            </main>
        </div>
    </div>
<?php else: ?>
    <header class="site-nav">
        <div class="brand">
            <div class="brand-mark">CP</div>
            <div>
                <div class="brand-title">Cube Portal</div>
                <div class="brand-subtitle">CESI Projects</div>
            </div>
        </div>
        <nav>
            <a href="#features">Fonctionnalites</a>
            <a href="#security">Securite</a>
            <a href="<?= base_url('login') ?>" class="primary-btn small">Connexion</a>
        </nav>
    </header>
    <main class="main-area">
        <?php foreach ($flashes as $flash): ?>
            <div class="flash <?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
        <?php endforeach; ?>
        <?= $content ?>
    </main>
<?php endif; ?>

<script src="<?= base_url('assets/js/main.js') ?>"></script>
</body>
</html>
