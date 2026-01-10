<?php $title = 'Cube Portal - Hub projets Cube'; ?>
<section class="hero">
    <div class="hero-content">
        <p class="eyebrow">SaaS oriente CESI</p>
        <h1>Cube Portal centralise les infos cles de vos projets Cube.</h1>
        <p class="lead">Roadmaps, documentation, comptes, decisions : une seule interface claire et securisee pour toute l equipe.</p>
        <div class="cta-group">
            <a class="primary-btn" href="<?= base_url('login') ?>">Acceder a l espace securise</a>
            <a class="ghost-btn" href="#features">Decouvrir</a>
        </div>
        <div class="badges">
            <span class="badge">Acces administrateur dedie</span>
            <span class="badge">Sessions protegees</span>
            <span class="badge">Reecriture d URL sans .php</span>
        </div>
    </div>
    <div class="hero-card">
        <div class="stat-block">
            <p class="label">Disponibilite</p>
            <p class="stat">99.9%</p>
            <p class="helper">Infra pensee pour les projets critiques CESI.</p>
        </div>
        <div class="stat-block">
            <p class="label">Comptes geres</p>
            <p class="stat">Securises</p>
            <p class="helper">Hashage, CSRF, controle de session.</p>
        </div>
        <div class="stat-block highlight">
            <p class="label">Admin</p>
            <p class="stat">Espace dedie</p>
            <p class="helper">Creation et pilotage des comptes.</p>
        </div>
    </div>
</section>

<section id="features" class="grid">
    <article class="card">
        <p class="eyebrow">Centralisation</p>
        <h3>Landing claire</h3>
        <p>Presentez la valeur du projet Cube, orientez les nouveaux arrivants et gardez un point d entree unique.</p>
    </article>
    <article class="card">
        <p class="eyebrow">Authentification</p>
        <h3>Connexion securisee</h3>
        <p>Sessions verrouillees, deconnexion automatique si inactivite, protection CSRF et mots de passe hashes.</p>
    </article>
    <article class="card">
        <p class="eyebrow">Pilotage</p>
        <h3>Dashboard moderne</h3>
        <p>Vue d ensemble des comptes, raccourcis cles, sidebar claire en blanc chaud et noir pour la lisibilite.</p>
    </article>
</section>

<section id="security" class="split">
    <div>
        <p class="eyebrow">Securite</p>
        <h2>Concu comme un vrai SaaS</h2>
        <ul class="bullet-list">
            <li>Reecriture d URL pour masquer les .php</li>
            <li>Hashage des mots de passe avec <code>password_hash</code></li>
            <li>Protection CSRF sur les formulaires sensibles</li>
            <li>Sessions verrouillees (HTTPOnly, SameSite, rotation, fingerprint)</li>
            <li>Espace admin dedie au premier compte</li>
        </ul>
    </div>
    <div class="card stacked">
        <h3>Pret a demarrer</h3>
        <p>Importez database.sql, renseignez config/.env.php, le premier compte admin est genere automatiquement.</p>
        <a class="primary-btn" href="<?= base_url('login') ?>">Connexion</a>
    </div>
</section>
