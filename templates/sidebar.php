<?php
$active = $active ?? '';
?>
<aside class="flex h-full flex-col gap-8 border-r border-[#e3d7cc] bg-white px-6 py-8">
    <div>
        <p class="text-xs uppercase tracking-[0.4em] text-[#6d6258]">Cube Portal</p>
        <p class="mt-2 text-lg font-semibold text-[#0f0f0f]">CESI Projects</p>
    </div>
    <nav class="flex flex-col gap-2 text-sm font-medium text-[#2b2723]">
        <a class="rounded-2xl px-4 py-3 <?= $active === 'dashboard' ? 'bg-[#efe7df] text-[#0f0f0f]' : 'hover:bg-[#f6f1eb]' ?>" href="/dashboard">Tableau de bord</a>
        <?php if (!empty($user) && $user['role'] === 'admin'): ?>
            <a class="rounded-2xl px-4 py-3 <?= $active === 'admin' ? 'bg-[#efe7df] text-[#0f0f0f]' : 'hover:bg-[#f6f1eb]' ?>" href="/admin">Espace admin</a>
        <?php endif; ?>
        <a class="rounded-2xl px-4 py-3 hover:bg-[#f6f1eb]" href="/logout">Se deconnecter</a>
    </nav>
    <div class="mt-auto rounded-3xl border border-[#e3d7cc] bg-[#f9f3ed] px-4 py-4 text-xs text-[#6d6258]">
        Acces securise. Pensez a changer le mot de passe apres la premiere connexion.
    </div>
</aside>
