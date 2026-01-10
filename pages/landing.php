<?php
$title = 'Cube Portal - Accueil';
$bodyClass = 'landing';
require __DIR__ . '/../templates/head.php';
$user = current_user($pdo);
?>
<div class="hero">
    <header class="hero__header">
        <div class="hero__logo">Cube Portal</div>
        <nav class="hero__nav">
            <a href="#features">Fonctionnalites</a>
            <a href="#about">A propos</a>
            <?php if ($user): ?>
                <a class="btn btn--ghost" href="/dashboard">Dashboard</a>
            <?php else: ?>
                <a class="btn btn--ghost" href="/connexion">Connexion</a>
            <?php endif; ?>
        </nav>
    </header>

    <section class="hero__content">
        <div>
            <p class="eyebrow">SaaS interne CESI</p>
            <h1>La base vivante des projets Cube.</h1>
            <p class="lead">Centralisez les contacts, documents, livrables et informations cles pour piloter chaque projet Cube en un seul endroit.</p>
            <div class="hero__actions">
                <a class="btn btn--primary" href="/connexion">Acceder au portail</a>
                <a class="btn btn--outline" href="#features">Voir les modules</a>
            </div>
        </div>
        <div class="hero__card">
            <div class="card">
                <h3>Vue rapide</h3>
                <ul>
                    <li>Projets en cours, jalons, livrables</li>
                    <li>Annuaire des interlocuteurs</li>
                    <li>Acces securise et roles</li>
                </ul>
                <div class="card__metric">
                    <span>Priorite du jour</span>
                    <strong>Suivi du Cube #4</strong>
                </div>
            </div>
        </div>
    </section>
</div>

<section id="features" class="section">
    <h2>Fonctionnalites essentielles</h2>
    <div class="grid">
        <div class="tile">
            <h3>Referentiel unique</h3>
            <p>Une source de verite pour les informations du projet Cube.</p>
        </div>
        <div class="tile">
            <h3>Acces securise</h3>
            <p>Sessions protegees, mots de passe chiffres, roles distincts.</p>
        </div>
        <div class="tile">
            <h3>Espace admin</h3>
            <p>Creation des comptes et gestion centralisee des acces.</p>
        </div>
    </div>
</section>

<section id="about" class="section section--muted">
    <h2>A propos</h2>
    <p>Cube Portal est un SaaS interne concu pour rendre les informations du projet Cube accessibles, structurees et actionnables par les equipes CESI.</p>
</section>

<footer class="footer">
    <span>Cube Portal (c) <?= date('Y') ?></span>
</footer>
<?php require __DIR__ . '/../templates/footer.php'; ?>
