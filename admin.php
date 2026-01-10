<?php
require_once __DIR__ . '/inc/auth.php';
require_once __DIR__ . '/inc/csrf.php';
require_admin();
$db = get_db();
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf'] ?? '')) {
        $msg = 'Jeton CSRF invalide.';
    } else {
        $u = trim($_POST['username'] ?? '');
        $e = trim($_POST['email'] ?? '');
        $p = $_POST['password'] ?? '';
        $r = $_POST['role'] ?? 'user';
        if ($u && $p) {
            create_user($u, $e, $p, $r);
            $msg = 'Utilisateur créé.';
        } else {
            $msg = 'Nom d\'utilisateur et mot de passe requis.';
        }
    }
}
$stmt = $db->query('SELECT id,username,email,role,created_at FROM users ORDER BY id DESC');
$users = $stmt->fetchAll();
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Espace Admin — Cube Portal</title>
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="app">
  <aside class="sidebar">
    <div class="brand">Cube Portal</div>
    <ul>
      <li><a href="/dashboard">Accueil</a></li>
      <li><a href="/admin">Espace Admin</a></li>
      <li><a href="/logout">Se déconnecter</a></li>
    </ul>
  </aside>
  <main class="main">
    <header class="main-header">
      <h2>Administration</h2>
    </header>
    <section class="content">
      <?php if ($msg): ?><div class="ok"><?=htmlspecialchars($msg)?></div><?php endif; ?>
      <h3>Créer un utilisateur</h3>
      <form method="post" action="/admin">
        <?php echo csrf_input(); ?>
        <label>Nom d'utilisateur
          <input name="username" required>
        </label>
        <label>Email
          <input name="email" type="email">
        </label>
        <label>Mot de passe
          <input name="password" type="password" required>
        </label>
        <label>Rôle
          <select name="role">
            <option value="user">Utilisateur</option>
            <option value="admin">Admin</option>
          </select>
        </label>
        <div class="form-row"><button class="btn" type="submit">Créer</button></div>
      </form>

      <h3>Utilisateurs</h3>
      <table class="users">
        <thead><tr><th>ID</th><th>Nom</th><th>Email</th><th>Rôle</th><th>Créé</th></tr></thead>
        <tbody>
          <?php foreach ($users as $u): ?>
          <tr>
            <td><?=htmlspecialchars($u['id'])?></td>
            <td><?=htmlspecialchars($u['username'])?></td>
            <td><?=htmlspecialchars($u['email'])?></td>
            <td><?=htmlspecialchars($u['role'])?></td>
            <td><?=htmlspecialchars($u['created_at'])?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </section>
  </main>
</body>
</html>
