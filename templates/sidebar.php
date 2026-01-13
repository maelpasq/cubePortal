<?php
$active = $active ?? '';
$integrations = $integrations ?? [];
?>
<aside class="flex h-full flex-col gap-8 border-r border-[#e3d7cc] bg-white px-6 py-8 lg:sticky lg:top-0 lg:h-screen lg:overflow-hidden">
    <div>
        <p class="text-xs uppercase tracking-[0.4em] text-[#6d6258]">Cube Portal</p>
        <p class="mt-2 text-lg font-semibold text-[#0f0f0f]">CESI Projects</p>
    </div>
    <nav class="flex flex-col gap-2 text-sm font-medium text-[#2b2723]">
        <a class="flex items-center gap-3 rounded-2xl px-4 py-3 <?= $active === 'dashboard' ? 'bg-[#efe7df] text-[#0f0f0f]' : 'hover:bg-[#f6f1eb]' ?>" href="/dashboard">
            <svg class="h-4 w-4 text-[#0f0f0f]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M3 10.5 12 3l9 7.5" />
                <path d="M5.5 12.5v7a1 1 0 0 0 1 1H10v-5h4v5h3.5a1 1 0 0 0 1-1v-7" />
            </svg>
            <span>Tableau de bord</span>
        </a>
        <div class="mt-4 text-xs uppercase tracking-[0.3em] text-[#a09082]">Documents</div>
        <a class="flex items-center gap-3 rounded-2xl px-4 py-3 <?= $active === 'documents' ? 'bg-[#efe7df] text-[#0f0f0f]' : 'hover:bg-[#f6f1eb]' ?>" href="/documents">
            <svg class="h-4 w-4 text-[#0f0f0f]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M4 6.5a2 2 0 0 1 2-2h4l2 2h6a2 2 0 0 1 2 2V17a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2Z" />
            </svg>
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
                    <svg class="h-4 w-4 text-[#0f0f0f]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M14 4h-4a2 2 0 0 0-2 2v2h2.5a1.5 1.5 0 0 1 0 3H8v2h2.5a1.5 1.5 0 0 1 0 3H8v2a2 2 0 0 0 2 2h4a2 2 0 0 0 2-2v-2h-2.5a1.5 1.5 0 0 1 0-3H16v-2h-2.5a1.5 1.5 0 0 1 0-3H16V6a2 2 0 0 0-2-2Z" />
                    </svg>
                    <span><?= e($label) ?></span>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
        <?php if (!empty($user) && $user['role'] === 'admin'): ?>
            <div class="mt-4 text-xs uppercase tracking-[0.3em] text-[#a09082]">Administration</div>
            <a class="flex items-center gap-3 rounded-2xl px-4 py-3 <?= $active === 'admin' ? 'bg-[#efe7df] text-[#0f0f0f]' : 'hover:bg-[#f6f1eb]' ?>" href="/admin">
                <svg class="h-4 w-4 text-[#0f0f0f]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="12" cy="12" r="3" />
                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06A1.65 1.65 0 0 0 15 19.4a1.65 1.65 0 0 0-1 .6 1.65 1.65 0 0 0-.33 1.82 2 2 0 1 1-3.34 0 1.65 1.65 0 0 0-.33-1.82 1.65 1.65 0 0 0-1-.6 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.6 15a1.65 1.65 0 0 0-.6-1 1.65 1.65 0 0 0-1.82-.33 2 2 0 1 1 0-3.34 1.65 1.65 0 0 0 1.82-.33 1.65 1.65 0 0 0 .6-1 1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.6a1.65 1.65 0 0 0 1-.6 1.65 1.65 0 0 0 .33-1.82 2 2 0 1 1 3.34 0 1.65 1.65 0 0 0 .33 1.82 1.65 1.65 0 0 0 1 .6 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82 1.65 1.65 0 0 0 .6 1 1.65 1.65 0 0 0 1.82.33 2 2 0 1 1 0 3.34 1.65 1.65 0 0 0-1.82.33 1.65 1.65 0 0 0-.6 1Z" />
                </svg>
                <span>Espace admin</span>
            </a>
        <?php endif; ?>
    </nav>
    <div class="mt-auto flex flex-col gap-2">
        <a class="flex items-center gap-3 rounded-2xl px-4 py-3 text-[#b3261e] hover:bg-[#b3261e]/10" href="/logout">
            <svg class="h-4 w-4 text-[#b3261e]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
                <path d="M10 17l5-5-5-5" />
                <path d="M15 12H3" />
            </svg>
            <span>Se deconnecter</span>
        </a>
        <div class="rounded-3xl border border-[#e3d7cc] bg-[#f9f3ed] px-4 py-4 text-xs text-[#6d6258]">
            Acces securise. Pensez a changer le mot de passe apres la premiere connexion.
        </div>
    </div>
</aside>
