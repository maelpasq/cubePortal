<?php
require_once '../includes/functions.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Cube Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        /* Specific page styles can go here or in style.css */
    </style>
</head>
<body>

<div class="dashboard-layout">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="logo">
            Cube Portal
        </div>
        <ul class="nav-links">
            <li class="nav-item">
                <a href="/dashboard" class="nav-link active">Vue d'ensemble</a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">Ressources</a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">Projets</a>
            </li>
            <?php if (isAdmin()): ?>
            <li class="nav-item" style="margin-top: 20px;">
                <div style="font-size: 0.75rem; text-transform:uppercase; color:#999; margin-bottom:8px; padding-left:12px; font-weight:600;">Administration</div>
                <a href="/admin" class="nav-link">Gestion des utilisateurs</a>
            </li>
            <?php endif; ?>
        </ul>
        <div class="user-info">
            <div style="margin-bottom: 8px; font-weight: 500; color: var(--text-color);">
                <?= htmlspecialchars($_SESSION['user_email']) ?>
            </div>
            <a href="/logout" style="color: var(--danger); font-size: 0.9rem;">Se déconnecter</a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <header class="header">
            <h1>Bienvenue sur le dashboard</h1>
            <button class="btn btn-secondary">Paramètres</button>
        </header>

        <section class="card">
            <h3 style="margin-bottom: 16px;">Annonces récentes</h3>
            <p style="color: #666;">Bienvenue sur la première version du Cube Portal. Retrouvez ici bientôt toutes les documentations.</p>
        </section>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px;">
            <div class="card">
                <h4 style="margin-bottom: 10px;">Documentation Technique</h4>
                <p style="font-size: 0.9rem; color: #666;">Accédez aux guides techniques pour vos projets dev.</p>
                <a href="#" style="display:inline-block; margin-top:16px; color:var(--accent-color); font-weight:500;">Voir plus →</a>
            </div>
            <div class="card">
                <h4 style="margin-bottom: 10px;">Gestion de Projet</h4>
                <p style="font-size: 0.9rem; color: #666;">Templates de gestion, CDC, et plannings.</p>
                <a href="#" style="display:inline-block; margin-top:16px; color:var(--accent-color); font-weight:500;">Voir plus →</a>
            </div>
        </div>

    </main>
</div>

</body>
</html>
