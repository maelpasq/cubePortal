<?php
$title = 'Cube Portal - Accueil';
$bodyClass = '';
$useTailwind = true;
require __DIR__ . '/../templates/head.php';
$user = current_user($pdo);
?>
<div class="min-h-screen bg-[#f6f1eb] text-[#0f0f0f]">
    <header class="mx-auto flex w-full max-w-6xl items-center justify-between px-6 py-8">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-2xl bg-[#0f0f0f]"></div>
            <div>
                <p class="text-xs uppercase tracking-[0.4em] text-[#6d6258]">Cube Portal</p>
                <p class="text-sm font-semibold">CESI Projects</p>
            </div>
        </div>
        <nav class="hidden items-center gap-6 text-sm font-medium text-[#2b2723] md:flex">
            <a href="#features">Fonctionnalites</a>
            <a href="#about">A propos</a>
        </nav>
        <?php if ($user): ?>
            <a class="rounded-full border border-[#e3d7cc] bg-white px-5 py-2 text-sm font-semibold" href="/dashboard">Dashboard</a>
        <?php else: ?>
            <a class="rounded-full border border-[#e3d7cc] bg-white px-5 py-2 text-sm font-semibold" href="/connexion">Connexion</a>
        <?php endif; ?>
    </header>

    <main class="mx-auto grid w-full max-w-6xl grid-cols-1 gap-10 px-6 pb-16 pt-6 lg:grid-cols-2 lg:items-center">
        <div>
            <p class="text-xs uppercase tracking-[0.4em] text-[#6d6258]">SaaS interne CESI</p>
            <h1 class="mt-4 text-4xl font-semibold leading-tight text-[#0f0f0f] sm:text-5xl">La base vivante des projets Cube.</h1>
            <p class="mt-5 text-base text-[#6d6258]">Centralisez les contacts, documents, livrables et informations cles pour piloter chaque projet Cube en un seul endroit.</p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a class="rounded-full bg-[#1f2d3a] px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-[#1f2d3a]/30" href="/connexion">Acceder au portail</a>
                <a class="rounded-full border border-[#1f2d3a] px-6 py-3 text-sm font-semibold text-[#1f2d3a]" href="#features">Voir les modules</a>
            </div>
            <div class="mt-10 grid gap-4 sm:grid-cols-2">
                <div class="rounded-3xl border border-[#e3d7cc] bg-white px-5 py-4">
                    <p class="text-sm font-semibold">Priorite du jour</p>
                    <p class="mt-2 text-sm text-[#6d6258]">Suivi du Cube #4 et alignement des livrables.</p>
                </div>
                <div class="rounded-3xl border border-[#e3d7cc] bg-white px-5 py-4">
                    <p class="text-sm font-semibold">Nouveaux acces</p>
                    <p class="mt-2 text-sm text-[#6d6258]">2 comptes en attente de validation admin.</p>
                </div>
            </div>
        </div>

        <div class="rounded-[32px] border border-[#e3d7cc] bg-[#0f0f0f] p-8 text-white shadow-2xl">
            <p class="text-xs uppercase tracking-[0.4em] text-white/60">Vue rapide</p>
            <h2 class="mt-3 text-2xl font-semibold">Pilotage en temps reel.</h2>
            <div class="mt-6 space-y-4 text-sm text-white/70">
                <p>Projets en cours, jalons, livrables, et contacts rassembles en un seul flux.</p>
                <p>Acces securise avec roles et activites tracees.</p>
            </div>
            <div class="mt-8 rounded-3xl bg-white/10 px-5 py-4">
                <p class="text-sm font-semibold text-white">Focus</p>
                <p class="mt-2 text-sm text-white/70">Centralisez les infos critiques avant la prochaine revue.</p>
            </div>
        </div>
    </main>
</div>

<section id="features" class="bg-white">
    <div class="mx-auto w-full max-w-6xl px-6 py-14">
        <h2 class="text-3xl font-semibold text-[#0f0f0f]">Fonctionnalites essentielles</h2>
        <p class="mt-3 text-sm text-[#6d6258]">Un socle clair pour organiser l'information projet.</p>
        <div class="mt-8 grid gap-6 md:grid-cols-3">
            <div class="rounded-3xl border border-[#e3d7cc] bg-[#f9f3ed] p-6">
                <h3 class="text-lg font-semibold">Referentiel unique</h3>
                <p class="mt-3 text-sm text-[#6d6258]">Une source de verite pour les informations du projet Cube.</p>
            </div>
            <div class="rounded-3xl border border-[#e3d7cc] bg-[#f9f3ed] p-6">
                <h3 class="text-lg font-semibold">Acces securise</h3>
                <p class="mt-3 text-sm text-[#6d6258]">Sessions protegees, mots de passe chiffres, roles distincts.</p>
            </div>
            <div class="rounded-3xl border border-[#e3d7cc] bg-[#f9f3ed] p-6">
                <h3 class="text-lg font-semibold">Espace admin</h3>
                <p class="mt-3 text-sm text-[#6d6258]">Creation des comptes et gestion centralisee des acces.</p>
            </div>
        </div>
    </div>
</section>

<footer class="bg-[#0f0f0f] text-white">
    <div class="mx-auto flex w-full max-w-6xl items-center justify-between px-6 py-8 text-sm text-white/70">
        <span>Cube Portal (c) <?= date('Y') ?></span>
        <span>Infrastructure CESI</span>
    </div>
</footer>
<?php require __DIR__ . '/../templates/footer.php'; ?>
