<?php
// Simple setup script to create the first admin user.
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/csrf.php';

$done = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf'] ?? '')) {
        $done = 'Jeton CSRF invalide.';
    } else {
        $user = trim($_POST['username'] ?? '');
        $pass = $_POST['password'] ?? '';
        $email = trim($_POST['email'] ?? '');
        if ($user && $pass) {
            create_user($user, $email, $pass, 'admin');
            $done = 'Compte admin créé. Vous pouvez maintenant vous connecter.';
        } else {
            $done = 'Nom d\'utilisateur et mot de passe requis.';
        }
    }
}
?>
<!doctype html>
<html lang="fr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Créer admin — Cube Portal</title>
<link rel="stylesheet" href="/assets/css/style.css"></head><body>
<main style="max-width:520px;margin:40px auto;padding:20px;background:#fff;">
  <h2>Créer le premier compte administrateur</h2>
  <?php if ($done): ?><div class="ok"><?=htmlspecialchars($done)?></div><?php endif; ?>
  <form method="post">
    <?php echo csrf_input(); ?>
    <label>Nom d'utilisateur<input name="username" required></label>
    <label>Email<input name="email" type="email"></label>
    <label>Mot de passe<input name="password" type="password" required></label>
    <div class="form-row"><button class="btn" type="submit">Créer admin</button></div>
  </form>
</main></body></html>
