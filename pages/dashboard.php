<?php
$user = require_auth($pdo);
$title = 'Cube Portal - Dashboard';
$bodyClass = '';
$useTailwind = true;
$active = 'dashboard';
require __DIR__ . '/../templates/head.php';
?>
<div class="min-h-screen bg-[#f6f1eb]">
    <div class="mx-auto grid min-h-screen w-full max-w-7xl grid-cols-1 lg:grid-cols-[260px_1fr]">
        <?php require __DIR__ . '/../templates/sidebar.php'; ?>
        <main class="px-6 py-10 lg:px-10">
            <header class="flex flex-col gap-3">
                <p class="text-xs uppercase tracking-[0.4em] text-[#6d6258]">Bienvenue</p>
                <h1 class="text-3xl font-semibold text-[#0f0f0f]">Tableau de bord</h1>
                <p class="text-sm text-[#6d6258]">Bonjour <?= e($user['name'] ?: $user['email']) ?>. Voici l'etat des projets Cube.</p>
            </header>

            <section class="mt-8 grid gap-6 md:grid-cols-3">
                <article class="rounded-3xl border border-[#e3d7cc] bg-white p-6">
                    <p class="text-sm font-semibold">Projets actifs</p>
                    <p class="mt-4 text-3xl font-semibold text-[#0f0f0f]">4</p>
                    <p class="mt-2 text-xs text-[#6d6258]">Cubes en suivi cette semaine</p>
                </article>
                <article class="rounded-3xl border border-[#e3d7cc] bg-white p-6">
                    <p class="text-sm font-semibold">Livrables</p>
                    <p class="mt-4 text-3xl font-semibold text-[#0f0f0f]">12</p>
                    <p class="mt-2 text-xs text-[#6d6258]">Documents references a completer</p>
                </article>
                <article class="rounded-3xl border border-[#e3d7cc] bg-white p-6">
                    <p class="text-sm font-semibold">Contacts cles</p>
                    <p class="mt-4 text-3xl font-semibold text-[#0f0f0f]">18</p>
                    <p class="mt-2 text-xs text-[#6d6258]">Partenaires et tuteurs CESI</p>
                </article>
            </section>

            <section class="mt-8 rounded-3xl border border-[#e3d7cc] bg-[#0f0f0f] p-8 text-white">
                <h2 class="text-2xl font-semibold">Focus du jour</h2>
                <p class="mt-3 text-sm text-white/70">Centralisez les informations critiques et partagez-les avec l'equipe projet pour conserver une vision claire.</p>
            </section>
        </main>
    </div>
</div>
<?php require __DIR__ . '/../templates/footer.php'; ?>
