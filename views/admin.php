<?php
require_once '../includes/functions.php';
requireAdmin();

$message = '';
$error = '';

// Handle Create User
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_user'])) {
    $email = $_POST['email'] ?? '';
    $pass = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'user';

    if ($email && $pass) {
        // Hash password
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        try {
            $stmt = $pdo->prepare("INSERT INTO users (email, password_hash, role) VALUES (:e, :p, :r)");
            $stmt->execute(['e' => $email, 'p' => $hash, 'r' => $role]);
            $message = "Utilisateur créé avec succès.";
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Duplicate entry
                $error = "Cet email existe déjà.";
            } else {
                $error = "Erreur lors de la création.";
            }
        }
    } else {
        $error = "Veuillez remplir tous les champs.";
    }
}

// Fetch Users
$stmt = $pdo->query("SELECT id, email, role, created_at FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Cube Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

<div class="dashboard-layout">
    <!-- Sidebar (Same as dashboard - ideally this duplication should be a partial, but keeping it simple/single-file for now as requested "simple saas") -->
    <aside class="sidebar">
        <div class="logo">Cube Portal</div>
        <ul class="nav-links">
            <li class="nav-item"><a href="/dashboard" class="nav-link">Vue d'ensemble</a></li>
            <li class="nav-item"><a href="#" class="nav-link">Ressources</a></li>
            <li class="nav-item"><a href="#" class="nav-link">Projets</a></li>
            
            <li class="nav-item" style="margin-top: 20px;">
                <div style="font-size: 0.75rem; text-transform:uppercase; color:#999; margin-bottom:8px; padding-left:12px; font-weight:600;">Administration</div>
                <a href="/admin" class="nav-link active">Gestion des utilisateurs</a>
            </li>
        </ul>
        <div class="user-info">
            <div style="margin-bottom: 8px; font-weight: 500; color: var(--text-color);">
                <?= htmlspecialchars($_SESSION['user_email']) ?>
            </div>
            <a href="/logout" style="color: var(--danger); font-size: 0.9rem;">Se déconnecter</a>
        </div>
    </aside>

    <main class="main-content">
        <header class="header">
            <h1>Administration</h1>
        </header>

        <!-- Use 2 columns: List and Create -->
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
            
            <!-- User List -->
            <div class="card">
                <h3 style="margin-bottom: 20px;">Utilisateurs</h3>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Email</th>
                            <th>Rôle</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($users as $u): ?>
                        <tr>
                            <td>#<?= $u['id'] ?></td>
                            <td><?= htmlspecialchars($u['email']) ?></td>
                            <td>
                                <span style="padding: 4px 8px; border-radius: 4px; background: <?= $u['role'] === 'admin' ? '#eef2ff' : '#f3f4f6' ?>; color: <?= $u['role'] === 'admin' ? '#4f46e5' : '#374151' ?>; font-size: 0.85rem; font-weight: 500;">
                                    <?= ucfirst($u['role']) ?>
                                </span>
                            </td>
                            <td><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
                            <td><button class="btn-secondary" style="padding: 4px 8px; font-size: 0.8rem;">Éditer</button></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Create User Form -->
            <div class="card" style="height: fit-content;">
                <h3 style="margin-bottom: 20px;">Créer un compte</h3>
                
                <?php if ($message): ?>
                    <div style="background:var(--success); color:white; padding:10px; border-radius:6px; margin-bottom:16px; font-size:0.9rem;"><?= $message ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div style="background:var(--danger); color:white; padding:10px; border-radius:6px; margin-bottom:16px; font-size:0.9rem;"><?= $error ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <input type="hidden" name="create_user" value="1">
                    
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Mot de passe</label>
                        <input type="password" name="password" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Rôle</label>
                        <select name="role" style="width: 100%; padding: 12px; background: var(--input-bg); border: 1px solid transparent; border-radius: 8px;">
                            <option value="user">Utilisateur</option>
                            <option value="admin">Administrateur</option>
                        </select>
                    </div>

                    <button type="submit" class="btn" style="width: 100%;">Créer l'utilisateur</button>
                </form>
            </div>

        </div>
    </main>
</div>

</body>
</html>
