<?php
$user = require_auth($pdo);
$title = 'Cube Portal - Dashboard';
$bodyClass = 'app';
$active = 'dashboard';
require __DIR__ . '/../templates/head.php';
?>
<div class="layout">
    <?php require __DIR__ . '/../templates/sidebar.php'; ?>
    <main class="main">
        <header class="main__header">
            <div>
                <p class="eyebrow">Bienvenue</p>
                <h1>Tableau de bord</h1>
                <p class="muted">Bonjour <?= e($user['name'] ?: $user['email']) ?>. Voici l'etat des projets Cube.</p>
            </div>
        </header>
        <section class="cards">
            <article class="card">
                <h3>Projets actifs</h3>
                <p class="big">4</p>
                <span class="muted">Cubes en suivi cette semaine</span>
            </article>
            <article class="card">
                <h3>Livrables</h3>
                <p class="big">12</p>
                <span class="muted">Documents references a completer</span>
            </article>
            <article class="card">
                <h3>Contacts cles</h3>
                <p class="big">18</p>
                <span class="muted">Partenaires et tuteurs CESI</span>
            </article>
        </section>
        <section class="panel">
            <h2>Focus du jour</h2>
            <p>Centralisez les informations critiques et partagez-les avec l'equipe projet pour conserver une vision claire.</p>
        </section>
    </main>
</div>
<?php require __DIR__ . '/../templates/footer.php'; ?>
