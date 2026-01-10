<?php
require_once 'includes/functions.php';
// If already logged in, go to dashboard
if (isLoggedIn()) {
    redirect('/dashboard');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cube Portal - CESI</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        .landing-hero {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            min-height: 80vh;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .hero-title {
            font-size: 3.5rem;
            margin-bottom: 24px;
            font-weight: 800;
            line-height: 1.1;
        }
        .hero-subtitle {
            font-size: 1.25rem;
            color: #666;
            margin-bottom: 40px;
            max-width: 600px;
        }
    </style>
</head>
<body>

<main class="landing-hero">
    <h1 class="hero-title">Cube Portal</h1>
    <p class="hero-subtitle">
        La plateforme centralisée pour toutes les ressources et informations essentielles des projets Cube du CESI.
    </p>
    <div>
        <a href="/login" class="btn">Accéder au portail</a>
    </div>
</main>

</body>
</html>
