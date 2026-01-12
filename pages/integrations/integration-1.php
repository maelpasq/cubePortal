<?php
$user = require_auth($pdo);
$integrations = require __DIR__ . '/../../lib/integrations.php';
$slug = 'integration-1';
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

$title = 'Cube Portal - ' . ($integration['label'] ?? 'Integration');
$bodyClass = '';
$useTailwind = true;
$active = 'integration:' . $slug;
$pageEyebrow = 'Integrations';
$pageTitle = $integration['label'] ?? 'Integration';
$pageLead = $integration['summary'] ?? '';

ob_start();
?>
<section class="mt-8 rounded-3xl border border-[#e3d7cc] bg-white p-6">
    <h2 class="text-xl font-semibold text-[#0f0f0f]">Configuration</h2>
    <p class="mt-3 text-sm text-[#6d6258]">
        Ajoutez ici les champs de configuration et deconnexion pour cette integration.
    </p>
</section>
<?php
$content = ob_get_clean();
require __DIR__ . '/../../templates/app-shell.php';
