<?php
$user = require_admin($pdo);
$title = 'Cube Portal - Espace admin';
$bodyClass = '';
$useTailwind = true;
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
<div class="min-h-screen bg-[#f6f1eb]">
    <div class="mx-auto grid min-h-screen w-full max-w-7xl grid-cols-1 lg:grid-cols-[260px_1fr]">
        <?php require __DIR__ . '/../templates/sidebar.php'; ?>
        <main class="px-6 py-10 lg:px-10">
            <header class="flex flex-col gap-3">
                <p class="text-xs uppercase tracking-[0.4em] text-[#6d6258]">Administration</p>
                <h1 class="text-3xl font-semibold text-[#0f0f0f]">Espace admin</h1>
                <p class="text-sm text-[#6d6258]">Gerez les comptes et acces a Cube Portal.</p>
            </header>

            <?php if (!empty($error)): ?>
                <div class="mt-6 rounded-2xl border border-[#f2b1b1] bg-[#ffe5e5] px-4 py-3 text-sm">
                    <?= e($error) ?>
                </div>
            <?php elseif (!empty($success)): ?>
                <div class="mt-6 rounded-2xl border border-[#b7e1c0] bg-[#e6f7e9] px-4 py-3 text-sm">
                    <?= e($success) ?>
                </div>
            <?php endif; ?>

            <section class="mt-8 rounded-3xl border border-[#e3d7cc] bg-white p-6">
                <h2 class="text-xl font-semibold text-[#0f0f0f]">Creer un compte</h2>
                <form method="post" class="mt-6 grid gap-4 md:grid-cols-2">
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <label class="block text-sm font-medium text-[#2b2723]">
                        Nom
                        <input type="text" name="name" placeholder="Nom complet"
                               class="mt-2 w-full rounded-2xl border border-[#e3d7cc] bg-white px-4 py-3 text-sm focus:border-[#1f2d3a] focus:outline-none">
                    </label>
                    <label class="block text-sm font-medium text-[#2b2723]">
                        Email
                        <input type="email" name="email" required
                               class="mt-2 w-full rounded-2xl border border-[#e3d7cc] bg-white px-4 py-3 text-sm focus:border-[#1f2d3a] focus:outline-none">
                    </label>
                    <label class="block text-sm font-medium text-[#2b2723]">
                        Mot de passe
                        <input type="password" name="password" required
                               class="mt-2 w-full rounded-2xl border border-[#e3d7cc] bg-white px-4 py-3 text-sm focus:border-[#1f2d3a] focus:outline-none">
                    </label>
                    <label class="block text-sm font-medium text-[#2b2723]">
                        Role
                        <select name="role" class="mt-2 w-full rounded-2xl border border-[#e3d7cc] bg-white px-4 py-3 text-sm focus:border-[#1f2d3a] focus:outline-none">
                            <option value="user">Utilisateur</option>
                            <option value="admin">Admin</option>
                        </select>
                    </label>
                    <label class="flex items-center gap-2 text-sm font-medium text-[#2b2723]">
                        <input type="checkbox" name="is_active" checked class="h-4 w-4 rounded border-[#e3d7cc]">
                        Compte actif
                    </label>
                    <div class="md:col-span-2">
                        <button class="rounded-full bg-[#1f2d3a] px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-[#1f2d3a]/30" type="submit">Creer</button>
                    </div>
                </form>
            </section>

            <section class="mt-8 rounded-3xl border border-[#e3d7cc] bg-white p-6">
                <h2 class="text-xl font-semibold text-[#0f0f0f]">Comptes existants</h2>
                <div class="mt-6 overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="text-xs uppercase text-[#6d6258]">
                            <tr>
                                <th class="pb-3">Nom</th>
                                <th class="pb-3">Email</th>
                                <th class="pb-3">Role</th>
                                <th class="pb-3">Statut</th>
                                <th class="pb-3">Creation</th>
                            </tr>
                        </thead>
                        <tbody class="text-[#2b2723]">
                            <?php foreach ($users as $row): ?>
                                <tr class="border-t border-[#efe7df]">
                                    <td class="py-3"><?= e($row['name'] ?: '-') ?></td>
                                    <td class="py-3"><?= e($row['email']) ?></td>
                                    <td class="py-3"><?= e($row['role']) ?></td>
                                    <td class="py-3"><?= (int)$row['is_active'] === 1 ? 'Actif' : 'Inactif' ?></td>
                                    <td class="py-3"><?= e($row['created_at']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
</div>
<?php require __DIR__ . '/../templates/footer.php'; ?>
