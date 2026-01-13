<?php
$active = $active ?? '';
$integrations = $integrations ?? [];
?>
<aside class="flex h-full flex-col gap-8 border-r border-[#e3d7cc] bg-white px-6 py-8">
    <div>
        <p class="text-xs uppercase tracking-[0.4em] text-[#6d6258]">Cube Portal</p>
        <p class="mt-2 text-lg font-semibold text-[#0f0f0f]">CESI Projects</p>
    </div>
    <nav class="flex flex-col gap-2 text-sm font-medium text-[#2b2723]">
        <a class="flex items-center gap-3 rounded-2xl px-4 py-3 <?= $active === 'dashboard' ? 'bg-[#efe7df] text-[#0f0f0f]' : 'hover:bg-[#f6f1eb]' ?>" href="/dashboard">
            <span class="text-base">🏠</span>
            <span>Tableau de bord</span>
        </a>
        <div class="mt-4 text-xs uppercase tracking-[0.3em] text-[#a09082]">Documents</div>
        <a class="flex items-center gap-3 rounded-2xl px-4 py-3 <?= $active === 'documents' ? 'bg-[#efe7df] text-[#0f0f0f]' : 'hover:bg-[#f6f1eb]' ?>" href="/documents">
            <span class="text-base">📁</span>
            <span>Gestion des documents</span>
        </a>
        <?php if (!empty($integrations)): ?>
            <div class="mt-4 text-xs uppercase tracking-[0.3em] text-[#a09082]">Exemple</div>
            <?php foreach ($integrations as $integration): ?>
                <?php
                $slug = $integration['slug'] ?? '';
                $label = $integration['label'] ?? '';
                $isActive = $slug !== '' && $active === 'integration:' . $slug;
                ?>
                <a class="flex items-center gap-3 rounded-2xl px-4 py-3 <?= $isActive ? 'bg-[#efe7df] text-[#0f0f0f]' : 'hover:bg-[#f6f1eb]' ?>" href="/integrations/<?= e($slug) ?>">
                    <span class="text-base">🧩</span>
                    <span><?= e($label) ?></span>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
        <?php if (!empty($user) && $user['role'] === 'admin'): ?>
            <div class="mt-4 text-xs uppercase tracking-[0.3em] text-[#a09082]">Administration</div>
            <a class="flex items-center gap-3 rounded-2xl px-4 py-3 <?= $active === 'admin' ? 'bg-[#efe7df] text-[#0f0f0f]' : 'hover:bg-[#f6f1eb]' ?>" href="/admin">
                <span class="text-base">⚙️</span>
                <span>Espace admin</span>
            </a>
        <?php endif; ?>
        <div class="mt-auto"></div>
        <a class="flex items-center gap-3 rounded-2xl px-4 py-3 text-[#b3261e] hover:bg-[#b3261e]/10" href="/logout">
            <span class="text-base">🚪</span>
            <span>Se deconnecter</span>
        </a>
    </nav>
    <div class="rounded-3xl border border-[#e3d7cc] bg-[#f9f3ed] px-4 py-4 text-xs text-[#6d6258]">
        Acces securise. Pensez a changer le mot de passe apres la premiere connexion.
    </div>
</aside>
