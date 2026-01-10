<?php $title = $title ?? 'Erreur'; ?>
<section class="panel">
    <h1><?= e($title) ?></h1>
    <p class="helper"><?= e($message ?? 'Une erreur est survenue.') ?></p>
    <div class="cta-group">
        <a class="primary-btn" href="<?= base_url('') ?>">Retour a l accueil</a>
        <?php if (current_user()): ?>
            <a class="ghost-btn" href="<?= base_url('dashboard') ?>">Dashboard</a>
        <?php endif; ?>
    </div>
</section>
