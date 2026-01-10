<?php $title = 'Dashboard'; ?>
<section class="grid dashboard-cards">
    <article class="card">
        <p class="eyebrow">Utilisateurs</p>
        <h2><?= (int) ($stats['user_count'] ?? 0) ?></h2>
        <p class="helper">Comptes actifs sur Cube Portal.</p>
    </article>
    <article class="card">
        <p class="eyebrow">Admins</p>
        <h2><?= (int) ($stats['admin_count'] ?? 0) ?></h2>
        <p class="helper">Espace admin réservé au premier compte.</p>
    </article>
    <article class="card">
        <p class="eyebrow">Sécurité</p>
        <h2>En place</h2>
        <p class="helper">Sessions protégées, CSRF, mots de passe hashés.</p>
    </article>
</section>

<section class="panel">
    <header class="panel-head">
        <div>
            <p class="eyebrow">Activité récente</p>
            <h3>Nouveaux comptes</h3>
        </div>
        <?php if ($user && $user['role'] === 'admin'): ?>
            <a class="ghost-btn small" href="<?= base_url('admin') ?>">Gérer les comptes</a>
        <?php endif; ?>
    </header>
    <?php if (!empty($stats['recent_users'])): ?>
        <div class="table">
            <div class="table-head">
                <span>Nom</span>
                <span>Email</span>
                <span>Rôle</span>
                <span>Créé le</span>
            </div>
            <?php foreach ($stats['recent_users'] as $recent): ?>
                <div class="table-row">
                    <span><?= e($recent['name']) ?></span>
                    <span><?= e($recent['email']) ?></span>
                    <span class="pill"><?= e($recent['role']) ?></span>
                    <span><?= date('d/m/Y', strtotime($recent['created_at'])) ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="helper">Aucun compte pour le moment. Créez le premier via l’espace admin.</p>
    <?php endif; ?>
</section>
