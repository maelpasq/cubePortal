<?php
require_once '../includes/functions.php';

if (isLoggedIn()) {
    redirect('/dashboard');
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (login($email, $password)) {
        redirect('/dashboard');
    } else {
        $error = "Identifiants incorrects.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Cube Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

<div class="auth-container">
    <div class="auth-box">
        <h2 style="margin-bottom: 24px;">Connexion</h2>
        
        <?php if ($error): ?>
            <div style="background:var(--danger); color:white; padding:10px; border-radius:6px; margin-bottom:20px; font-size:0.9rem;">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required autofocus placeholder="admin@cubeportal.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required placeholder="password123">
            </div>

            <button type="submit" class="btn" style="width: 100%;">Se connecter</button>
        </form>
        <div style="margin-top: 20px; text-align: center;">
            <a href="/" style="font-size: 0.9rem; color: #666;">Retour à l'accueil</a>
        </div>
    </div>
</div>

</body>
</html>
