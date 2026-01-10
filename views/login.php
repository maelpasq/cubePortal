<?php $title = 'Connexion'; ?>
<section class="auth-card">
    <div class="auth-header">
        <p class="eyebrow">Sécurisé</p>
        <h1>Connexion Cube Portal</h1>
        <p class="helper">Accès réservé aux membres du projet Cube.</p>
    </div>
    <?php if (!empty($error)): ?>
        <div class="flash danger"><?= e($error) ?></div>
    <?php endif; ?>
    <?php if (isset($_GET['timeout'])): ?>
        <div class="flash warning">Session expirée, merci de vous reconnecter.</div>
    <?php endif; ?>
    <form class="form" action="<?= base_url('login') ?>" method="post" novalidate>
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <label class="field">
            <span>Email CESI</span>
            <input type="email" name="email" placeholder="prenom.nom@cesi.fr" required>
        </label>
        <label class="field">
            <span>Mot de passe</span>
            <input type="password" name="password" placeholder="********" required minlength="8">
        </label>
        <button type="submit" class="primary-btn full">Se connecter</button>
    </form>
    <div class="auth-footer">
        <a href="<?= base_url('') ?>" class="ghost-btn small">Retour à la landing</a>
    </div>
</section>
