<?php
$user = require_auth($pdo);
$integrations = require __DIR__ . '/../../lib/integrations.php';
$slug = 'exemple';
$integration = null;

foreach ($integrations as $item) {
    if (($item['slug'] ?? '') === $slug) {
        $integration = $item;
        break;
    }
}

if ($integration === null) {
    http_response_code(404);
    echo 'Integration not found.';
    exit;
}

$title = 'Cube Portal - ' . ($integration['label'] ?? 'Exemple');
$bodyClass = '';
$useTailwind = true;
$active = 'integration:' . $slug;
$pageEyebrow = 'Exemple';
$pageTitle = $integration['label'] ?? 'Exemple';
$pageLead = $integration['summary'] ?? '';

ob_start();
?>
<section class="mt-8 grid gap-6 md:grid-cols-2">
    <article class="rounded-3xl border border-[#e3d7cc] bg-white p-6">
        <h2 class="text-xl font-semibold text-[#0f0f0f]">Configuration</h2>
        <p class="mt-3 text-sm text-[#6d6258]">
            Ajoutez ici les champs de connexion ou d'API lorsque vous serez pret a brancher un service externe.
        </p>
    </article>
    <article class="rounded-3xl border border-[#e3d7cc] bg-white p-6">
        <h2 class="text-xl font-semibold text-[#0f0f0f]">Etat</h2>
        <p class="mt-3 text-sm text-[#6d6258]">
            Cette page sert d'exemple pour vos futures integrations.
        </p>
    </article>
</section>
<?php
$content = ob_get_clean();
require __DIR__ . '/../../templates/app-shell.php';
