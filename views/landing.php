<?php $title = 'Cube Portal - Hub projets Cube'; ?>
<section class="hero">
    <div class="hero-content">
        <p class="eyebrow">SaaS orienté CESI</p>
        <h1>Cube Portal centralise les infos clés de vos projets Cube.</h1>
        <p class="lead">Roadmaps, documentation, comptes, décisions : une seule interface claire et sécurisée pour toute l’équipe.</p>
        <div class="cta-group">
            <a class="primary-btn" href="<?= base_url('login') ?>">Accéder à l’espace sécurisé</a>
            <a class="ghost-btn" href="#features">Découvrir</a>
        </div>
        <div class="badges">
            <span class="badge">Accès administrateur dédié</span>
            <span class="badge">Sessions protégées</span>
            <span class="badge">Réécriture d’URL sans .php</span>
        </div>
    </div>
    <div class="hero-card">
        <div class="stat-block">
            <p class="label">Disponibilité</p>
            <p class="stat">99.9%</p>
            <p class="helper">Infra pensée pour les projets critiques CESI.</p>
        </div>
        <div class="stat-block">
            <p class="label">Comptes gérés</p>
            <p class="stat">Sécurisés</p>
            <p class="helper">Hashage, CSRF, contrôle de session.</p>
        </div>
        <div class="stat-block highlight">
            <p class="label">Admin</p>
            <p class="stat">Espace dédié</p>
            <p class="helper">Création et pilotage des comptes.</p>
        </div>
    </div>
</section>

<section id="features" class="grid">
    <article class="card">
        <p class="eyebrow">Centralisation</p>
        <h3>Landing claire</h3>
        <p>Présentez la valeur du projet Cube, orientez les nouveaux arrivants et gardez un point d’entrée unique.</p>
    </article>
    <article class="card">
        <p class="eyebrow">Authentification</p>
        <h3>Connexion sécurisée</h3>
        <p>Sessions verrouillées, déconnexion automatique si inactivité, protection CSRF et mots de passe hashés.</p>
    </article>
    <article class="card">
        <p class="eyebrow">Pilotage</p>
        <h3>Dashboard moderne</h3>
        <p>Vue d’ensemble des comptes, raccourcis clés, sidebar claire en blanc chaud et noir pour la lisibilité.</p>
    </article>
</section>

<section id="security" class="split">
    <div>
        <p class="eyebrow">Sécurité</p>
        <h2>Concu comme un vrai SaaS</h2>
        <ul class="bullet-list">
            <li>Réécriture d’URL pour masquer les .php</li>
            <li>Hashage des mots de passe avec <code>password_hash</code></li>
            <li>Protection CSRF sur les formulaires sensibles</li>
            <li>Sessions verrouillées (HTTPOnly, SameSite, rotation, fingerprint)</li>
            <li>Espace admin dédié au premier compte</li>
        </ul>
    </div>
    <div class="card stacked">
        <h3>Prêt à démarrer</h3>
        <p>Importez database.sql, renseignez config/.env.php, le premier compte admin est généré automatiquement.</p>
        <a class="primary-btn" href="<?= base_url('login') ?>">Connexion</a>
    </div>
</section>
