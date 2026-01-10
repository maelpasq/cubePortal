<?php
$active = $active ?? '';
?>
<aside class="sidebar">
    <div class="sidebar__brand">
        <div class="logo">Cube Portal</div>
        <div class="tag">CESI Projects</div>
    </div>
    <nav class="sidebar__nav">
        <a class="nav-item <?= $active === 'dashboard' ? 'is-active' : '' ?>" href="/dashboard">Tableau de bord</a>
        <?php if (!empty($user) && $user['role'] === 'admin'): ?>
            <a class="nav-item <?= $active === 'admin' ? 'is-active' : '' ?>" href="/admin">Espace admin</a>
        <?php endif; ?>
        <a class="nav-item" href="/logout">Se deconnecter</a>
    </nav>
</aside>
