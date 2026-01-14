<?php
$user = require_auth($pdo);
$title = 'Cube Portal - Mon compte';
$bodyClass = '';
$useTailwind = true;
$active = 'account';
$integrations = require __DIR__ . '/../../lib/integrations.php';
$pageEyebrow = 'Compte';
$pageTitle = 'Mon compte';
$pageLead = 'Mettez a jour vos informations et securisez votre acces.';

$errors = [];
$successes = [];

if (is_post()) {
    if (!csrf_verify($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Jeton de securite invalide.';
    } else {
        $name = trim((string)($_POST['name'] ?? ''));
        $email = trim((string)($_POST['email'] ?? ''));
        $password = (string)($_POST['password'] ?? '');
        $passwordConfirm = (string)($_POST['password_confirm'] ?? '');

        if ($email === '') {
            $errors[] = 'Email requis.';
        }

        if ($password !== '' && $password !== $passwordConfirm) {
            $errors[] = 'Les mots de passe ne correspondent pas.';
        }

        if ($email !== '') {
            $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email AND id <> :id LIMIT 1');
            $stmt->execute([':email' => $email, ':id' => $user['id']]);
            if ($stmt->fetch()) {
                $errors[] = 'Un autre compte utilise deja cet email.';
            }
        }

        if (empty($errors)) {
            $fields = [
                ':name' => $name,
                ':email' => $email,
                ':id' => $user['id'],
            ];
            $setParts = ['name = :name', 'email = :email'];
            if ($password !== '') {
                $fields[':password_hash'] = password_hash($password, PASSWORD_DEFAULT);
                $setParts[] = 'password_hash = :password_hash';
            }
            $setSql = implode(', ', $setParts);

            try {
                $stmt = $pdo->prepare("UPDATE users SET {$setSql} WHERE id = :id");
                $stmt->execute($fields);
                $successes[] = 'Compte mis a jour.';
                $user = current_user($pdo) ?? $user;
            } catch (Throwable $e) {
                $errors[] = 'Impossible de mettre a jour le compte : ' . $e->getMessage();
            }
        }
    }
}

$profileName = trim((string)($user['name'] ?? ''));
$profileEmail = trim((string)($user['email'] ?? ''));
$initialsSource = $profileName !== '' ? $profileName : $profileEmail;
$initials = '';
if ($initialsSource !== '') {
    $parts = preg_split('/\s+/', $initialsSource) ?: [];
    foreach ($parts as $part) {
        if ($part === '') {
            continue;
        }
        $letter = function_exists('mb_substr') ? mb_substr($part, 0, 1) : substr($part, 0, 1);
        $initials .= $letter;
        if (strlen($initials) >= 2) {
            break;
        }
    }
    $initials = function_exists('mb_strtoupper') ? mb_strtoupper($initials) : strtoupper($initials);
}
$roleLabel = ($user['role'] ?? '') === 'admin' ? 'Administrateur' : 'Utilisateur';

ob_start();
?>
<?php if (!empty($errors)): ?>
    <div class="mt-6 rounded-2xl border border-[#f2b1b1] bg-[#ffe5e5] px-4 py-3 text-sm text-[#7d2b2b]">
        <?= e(implode(' ', $errors)) ?>
    </div>
<?php elseif (!empty($successes)): ?>
    <div class="mt-6 rounded-2xl border border-[#b7e1c0] bg-[#e6f7e9] px-4 py-3 text-sm text-[#2f6b3a]">
        <?= e(implode(' ', $successes)) ?>
    </div>
<?php endif; ?>

<section class="mt-8 grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
    <article class="rounded-3xl border border-[#e3d7cc] bg-white p-6">
        <h2 class="text-xl font-semibold text-[#0f0f0f]">Informations personnelles</h2>
        <p class="mt-2 text-sm text-[#6d6258]">Mettez a jour vos coordonnees et changez votre mot de passe si besoin.</p>
        <form method="post" class="mt-6 grid gap-4">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <label class="block text-sm font-medium text-[#2b2723]">
                Nom complet
                <input type="text" name="name" value="<?= e($profileName) ?>"
                       class="mt-2 w-full rounded-2xl border border-[#e3d7cc] bg-white px-4 py-3 text-sm focus:border-[#1f2d3a] focus:outline-none">
            </label>
            <label class="block text-sm font-medium text-[#2b2723]">
                Email
                <input type="email" name="email" required value="<?= e($profileEmail) ?>"
                       class="mt-2 w-full rounded-2xl border border-[#e3d7cc] bg-white px-4 py-3 text-sm focus:border-[#1f2d3a] focus:outline-none">
            </label>
            <label class="block text-sm font-medium text-[#2b2723]">
                Nouveau mot de passe
                <input type="password" name="password"
                       class="mt-2 w-full rounded-2xl border border-[#e3d7cc] bg-white px-4 py-3 text-sm focus:border-[#1f2d3a] focus:outline-none">
                <span class="mt-2 block text-xs text-[#6d6258]">Laissez vide si vous ne souhaitez pas le modifier.</span>
            </label>
            <label class="block text-sm font-medium text-[#2b2723]">
                Confirmer le mot de passe
                <input type="password" name="password_confirm"
                       class="mt-2 w-full rounded-2xl border border-[#e3d7cc] bg-white px-4 py-3 text-sm focus:border-[#1f2d3a] focus:outline-none">
            </label>
            <div class="flex flex-wrap gap-3 pt-2">
                <button class="rounded-full bg-[#1f2d3a] px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-[#1f2d3a]/30" type="submit">
                    Mettre a jour
                </button>
            </div>
        </form>
    </article>
    <aside class="rounded-3xl border border-[#e3d7cc] bg-[#f9f3ed] p-6">
        <div class="flex items-center gap-4">
            <div class="flex h-14 w-14 items-center justify-center rounded-3xl bg-[#1f2d3a] text-lg font-semibold text-white">
                <?= e($initials !== '' ? $initials : 'CP') ?>
            </div>
            <div>
                <p class="text-sm font-semibold text-[#0f0f0f]"><?= e($profileName !== '' ? $profileName : $profileEmail) ?></p>
                <p class="text-xs text-[#6d6258]"><?= e($roleLabel) ?></p>
            </div>
        </div>
        <div class="mt-6 space-y-3 text-sm text-[#6d6258]">
            <p><span class="font-semibold text-[#2b2723]">Email :</span> <?= e($profileEmail) ?></p>
            <p><span class="font-semibold text-[#2b2723]">Statut :</span> Actif</p>
        </div>
    </aside>
</section>
<?php
$content = ob_get_clean();
require __DIR__ . '/../../templates/app-shell.php';
