<?php
require_once __DIR__ . '/inc/auth.php';
require_once __DIR__ . '/inc/csrf.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf'] ?? '')) {
        $error = 'Jeton CSRF invalide.';
    } else {
        $u = trim($_POST['username'] ?? '');
        $p = $_POST['password'] ?? '';
        if ($u === '' || $p === '') {
            $error = 'Tous les champs sont requis.';
        } else {
            if (login_user($u, $p)) {
                header('Location: /dashboard');
                exit;
            } else {
                $error = 'Identifiants invalides.';
            }
        }
    }
}
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Connexion — Cube Portal</title>
  <link rel="stylesheet" href="/assets/css/style.css">
  <meta name="robots" content="noindex">
</head>
<body class="auth">
  <main class="auth-box">
    <h2>Connexion</h2>
    <?php if ($error): ?>
      <div class="error"><?=htmlspecialchars($error)?></div>
    <?php endif; ?>
    <form method="post" action="/login">
      <?php echo csrf_input(); ?>
      <label>Nom d'utilisateur
        <input name="username" required autofocus>
      </label>
      <label>Mot de passe
        <input name="password" type="password" required>
      </label>
      <div class="form-row">
        <button class="btn" type="submit">Se connecter</button>
      </div>
    </form>
  </main>
</body>
</html>
