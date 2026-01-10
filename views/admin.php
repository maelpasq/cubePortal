<?php $title = 'Espace admin'; ?>
<section class="panel">
    <header class="panel-head">
        <div>
            <p class="eyebrow">Comptes</p>
            <h3>Utilisateurs du portail</h3>
        </div>
    </header>
    <?php if (!empty($users)): ?>
        <div class="table">
            <div class="table-head">
                <span>Nom</span>
                <span>Email</span>
                <span>Role</span>
                <span>Cree le</span>
            </div>
            <?php foreach ($users as $account): ?>
                <div class="table-row">
                    <span><?= e($account['name']) ?></span>
                    <span><?= e($account['email']) ?></span>
                    <span class="pill <?= $account['role'] === 'admin' ? 'pill-admin' : '' ?>"><?= e($account['role']) ?></span>
                    <span><?= date('d/m/Y', strtotime($account['created_at'])) ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="helper">Aucun utilisateur pour le moment.</p>
    <?php endif; ?>
</section>

<section class="panel">
    <header class="panel-head">
        <div>
            <p class="eyebrow">Gestion</p>
            <h3>Creer un compte</h3>
        </div>
    </header>
    <?php if (!empty($formError)): ?>
        <div class="flash danger"><?= e($formError) ?></div>
    <?php endif; ?>
    <form class="form two-col" method="post" action="<?= base_url('admin') ?>">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <label class="field">
            <span>Nom complet</span>
            <input type="text" name="name" required placeholder="Nom Prenom" value="<?= e($formValues['name'] ?? '') ?>">
        </label>
        <label class="field">
            <span>Email</span>
            <input type="email" name="email" required placeholder="prenom.nom@cesi.fr" value="<?= e($formValues['email'] ?? '') ?>">
        </label>
        <label class="field">
            <span>Mot de passe</span>
            <input type="password" name="password" required minlength="8" placeholder="Minimum 8 caracteres">
        </label>
        <label class="field">
            <span>Role</span>
            <select name="role" required>
                <option value="member" <?= (isset($formValues['role']) && $formValues['role'] === 'member') ? 'selected' : '' ?>>Membre</option>
                <option value="admin" <?= (isset($formValues['role']) && $formValues['role'] === 'admin') ? 'selected' : '' ?>>Admin</option>
            </select>
        </label>
        <button type="submit" class="primary-btn full">Creer le compte</button>
    </form>
</section>
