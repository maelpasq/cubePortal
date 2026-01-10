<?php
$user = require_admin($pdo);
$title = 'Cube Portal - Espace admin';
$bodyClass = 'app';
$active = 'admin';

if (is_post()) {
    if (!csrf_verify($_POST['csrf_token'] ?? null)) {
        $error = 'Jeton de securite invalide.';
    } else {
        $name = trim((string)($_POST['name'] ?? ''));
        $email = trim((string)($_POST['email'] ?? ''));
        $role = ($_POST['role'] ?? 'user') === 'admin' ? 'admin' : 'user';
        $password = (string)($_POST['password'] ?? '');
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if ($email === '' || $password === '') {
            $error = 'Email et mot de passe requis.';
        } else {
            $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
            $stmt->execute([':email' => $email]);
            if ($stmt->fetch()) {
                $error = 'Cet email existe deja.';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $insert = $pdo->prepare('INSERT INTO users (email, name, password_hash, role, is_active) VALUES (:email, :name, :hash, :role, :active)');
                $insert->execute([
                    ':email' => $email,
                    ':name' => $name,
                    ':hash' => $hash,
                    ':role' => $role,
                    ':active' => $isActive,
                ]);
                $success = 'Compte cree avec succes.';
            }
        }
    }
}

$stmt = $pdo->query('SELECT id, email, name, role, is_active, created_at FROM users ORDER BY created_at DESC');
$users = $stmt->fetchAll();

require __DIR__ . '/../templates/head.php';
?>
<div class="layout">
    <?php require __DIR__ . '/../templates/sidebar.php'; ?>
    <main class="main">
        <header class="main__header">
            <div>
                <p class="eyebrow">Administration</p>
                <h1>Espace admin</h1>
                <p class="muted">Gerez les comptes et acces a Cube Portal.</p>
            </div>
        </header>

        <?php if (!empty($error)): ?>
            <div class="alert alert--error"><?= e($error) ?></div>
        <?php elseif (!empty($success)): ?>
            <div class="alert alert--success"><?= e($success) ?></div>
        <?php endif; ?>

        <section class="panel">
            <h2>Creer un compte</h2>
            <form method="post" class="form form--grid">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <label class="field">
                    <span>Nom</span>
                    <input type="text" name="name" placeholder="Nom complet">
                </label>
                <label class="field">
                    <span>Email</span>
                    <input type="email" name="email" required>
                </label>
                <label class="field">
                    <span>Mot de passe</span>
                    <input type="password" name="password" required>
                </label>
                <label class="field">
                    <span>Role</span>
                    <select name="role">
                        <option value="user">Utilisateur</option>
                        <option value="admin">Admin</option>
                    </select>
                </label>
                <label class="field field--checkbox">
                    <input type="checkbox" name="is_active" checked>
                    <span>Compte actif</span>
                </label>
                <button class="btn btn--primary" type="submit">Creer</button>
            </form>
        </section>

        <section class="panel">
            <h2>Comptes existants</h2>
            <div class="table">
                <div class="table__row table__head">
                    <div>Nom</div>
                    <div>Email</div>
                    <div>Role</div>
                    <div>Statut</div>
                    <div>Creation</div>
                </div>
                <?php foreach ($users as $row): ?>
                    <div class="table__row">
                        <div><?= e($row['name'] ?: '-') ?></div>
                        <div><?= e($row['email']) ?></div>
                        <div><?= e($row['role']) ?></div>
                        <div><?= (int)$row['is_active'] === 1 ? 'Actif' : 'Inactif' ?></div>
                        <div><?= e($row['created_at']) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </main>
</div>
<?php require __DIR__ . '/../templates/footer.php'; ?>
