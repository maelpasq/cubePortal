<?php
require_once __DIR__ . '/inc/auth.php';
require_auth();
$me = current_user();
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Dashboard — Cube Portal</title>
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="app">
  <aside class="sidebar">
    <div class="brand">Cube Portal</div>
    <ul>
      <li><a href="/dashboard">Accueil</a></li>
      <?php if (is_admin()): ?>
      <li><a href="/admin">Espace Admin</a></li>
      <?php endif; ?>
      <li><a href="/logout">Se déconnecter</a></li>
    </ul>
  </aside>
  <main class="main">
    <header class="main-header">
      <h2>Bienvenue, <?=htmlspecialchars($me['username'])?></h2>
    </header>
    <section class="content">
      <p>Ici vous trouverez les informations liées au projet Cube.</p>
    </section>
  </main>
</body>
</html>
