<?php
$user = require_admin($pdo);
$title = 'Cube Portal - Espace admin';
$bodyClass = '';
$useTailwind = true;
$active = 'admin';
$integrations = require __DIR__ . '/../lib/integrations.php';
require __DIR__ . '/../templates/modal.php';

$errors = [];
$successes = [];

if (is_post()) {
    $action = $_POST['action'] ?? 'create';
    if (!csrf_verify($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Jeton de securite invalide.';
    } elseif ($action === 'create') {
        $name = trim((string)($_POST['name'] ?? ''));
        $email = trim((string)($_POST['email'] ?? ''));
        $role = ($_POST['role'] ?? 'user') === 'admin' ? 'admin' : 'user';
        $password = (string)($_POST['password'] ?? '');
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if ($email === '' || $password === '') {
            $errors[] = 'Email et mot de passe requis.';
        } else {
            $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
            $stmt->execute([':email' => $email]);
            if ($stmt->fetch()) {
                $errors[] = 'Cet email existe deja.';
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
                $successes[] = 'Compte cree avec succes.';
            }
        }
    } elseif ($action === 'update') {
        $userId = (int)($_POST['user_id'] ?? 0);
        $name = trim((string)($_POST['edit_name'] ?? ''));
        $email = trim((string)($_POST['edit_email'] ?? ''));
        $role = ($_POST['edit_role'] ?? 'user') === 'admin' ? 'admin' : 'user';
        $isActive = isset($_POST['edit_is_active']) ? 1 : 0;
        $password = (string)($_POST['edit_password'] ?? '');

        if ($userId <= 0 || $email === '') {
            $errors[] = 'Selectionnez un utilisateur et renseignez un email.';
        } else {
            $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email AND id <> :id LIMIT 1');
            $stmt->execute([':email' => $email, ':id' => $userId]);
            if ($stmt->fetch()) {
                $errors[] = 'Un autre compte utilise deja cet email.';
            } else {
                $fields = [
                    'name' => $name,
                    'email' => $email,
                    'role' => $role,
                    'is_active' => $isActive,
                ];
                $setParts = ['name = :name', 'email = :email', 'role = :role', 'is_active = :active'];
                if ($password !== '') {
                    $fields['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
                    $setParts[] = 'password_hash = :password_hash';
                }
                $setSql = implode(', ', $setParts);
                $fields['id'] = $userId;

                try {
                    $stmt = $pdo->prepare("UPDATE users SET {$setSql} WHERE id = :id");
                    $stmt->bindValue(':name', $fields['name']);
                    $stmt->bindValue(':email', $fields['email']);
                    $stmt->bindValue(':role', $fields['role']);
                    $stmt->bindValue(':active', $fields['is_active'], PDO::PARAM_INT);
                    if (isset($fields['password_hash'])) {
                        $stmt->bindValue(':password_hash', $fields['password_hash']);
                    }
                    $stmt->bindValue(':id', $fields['id'], PDO::PARAM_INT);
                    $stmt->execute();
                    $successes[] = 'Compte mis a jour.';
                } catch (Throwable $e) {
                    $errors[] = 'Impossible de mettre a jour le compte : ' . $e->getMessage();
                }
            }
        }
    } elseif ($action === 'toggle') {
        $userId = (int)($_POST['user_id'] ?? 0);
        if ($userId <= 0) {
            $errors[] = 'Utilisateur invalide.';
        } else {
            $stmt = $pdo->prepare('SELECT is_active FROM users WHERE id = :id');
            $stmt->execute([':id' => $userId]);
            $row = $stmt->fetch();
            if (!$row) {
                $errors[] = 'Utilisateur introuvable.';
            } else {
                $newStatus = (int)$row['is_active'] === 1 ? 0 : 1;
                $update = $pdo->prepare('UPDATE users SET is_active = :status WHERE id = :id');
                $update->execute([':status' => $newStatus, ':id' => $userId]);
                $successes[] = $newStatus === 1 ? 'Compte reactive.' : 'Compte desactive.';
            }
        }
    } elseif ($action === 'delete') {
        $userId = (int)($_POST['user_id'] ?? 0);
        if ($userId <= 0) {
            $errors[] = 'Utilisateur invalide.';
        } elseif ($userId === (int)$user['id']) {
            $errors[] = 'Vous ne pouvez pas supprimer votre propre compte.';
        } else {
            $delete = $pdo->prepare('DELETE FROM users WHERE id = :id');
            $delete->execute([':id' => $userId]);
            $successes[] = 'Compte supprime.';
        }
    }
}

$stmt = $pdo->query('SELECT id, email, name, role, is_active, created_at FROM users ORDER BY created_at DESC');
$users = $stmt->fetchAll();

$pageEyebrow = 'Administration';
$pageTitle = 'Espace admin';
$pageLead = 'Gerez les comptes et acces a Cube Portal.';

ob_start();
?>
<?php if (!empty($errors)): ?>
    <div class="mt-6 rounded-2xl border border-[#f2b1b1] bg-[#ffe5e5] px-4 py-3 text-sm">
        <?= e(implode(' ', $errors)) ?>
    </div>
<?php elseif (!empty($successes)): ?>
    <div class="mt-6 rounded-2xl border border-[#b7e1c0] bg-[#e6f7e9] px-4 py-3 text-sm">
        <?= e(implode(' ', $successes)) ?>
    </div>
<?php endif; ?>

<section class="mt-8 rounded-3xl border border-[#e3d7cc] bg-white p-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h2 class="text-xl font-semibold text-[#0f0f0f]">Comptes existants</h2>
        <button
            type="button"
            id="open-create"
            class="rounded-full bg-[#1f2d3a] px-5 py-2 text-sm font-semibold text-white shadow-lg shadow-[#1f2d3a]/30"
        >
            Creer un compte
        </button>
    </div>
    <div class="mt-6 overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="text-xs uppercase text-[#6d6258]">
                <tr>
                    <th class="pb-3">Nom</th>
                    <th class="pb-3">Email</th>
                    <th class="pb-3">Role</th>
                    <th class="pb-3">Statut</th>
                    <th class="pb-3">Creation</th>
                    <th class="pb-3">Actions</th>
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
                        <td class="py-3">
                            <div class="flex flex-wrap gap-2">
                                <button
                                    class="rounded-full border border-[#e3d7cc] px-3 py-2 text-xs font-semibold text-[#1f2d3a]"
                                    type="button"
                                    data-edit="true"
                                    data-id="<?= e((string)$row['id']) ?>"
                                    data-name="<?= e($row['name'] ?? '') ?>"
                                    data-email="<?= e($row['email'] ?? '') ?>"
                                    data-role="<?= e($row['role'] ?? 'user') ?>"
                                    data-active="<?= (int)$row['is_active'] === 1 ? '1' : '0' ?>"
                                >
                                    Modifier
                                </button>
                                <form method="post">
                                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                    <input type="hidden" name="action" value="toggle">
                                    <input type="hidden" name="user_id" value="<?= e((string)$row['id']) ?>">
                                    <button class="rounded-full border border-[#e3d7cc] p-2 text-xs font-semibold text-[#1f2d3a]" type="submit" aria-label="<?= (int)$row['is_active'] === 1 ? 'Desactiver' : 'Reactiver' ?>">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M5 12h14" />
                                            <path d="M12 5v14" />
                                        </svg>
                                    </button>
                                </form>
                                <form method="post" class="delete-user-form">
                                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="user_id" value="<?= e((string)$row['id']) ?>">
                                    <button
                                        class="rounded-full bg-[#b3261e] p-2 text-xs font-semibold text-white hover:bg-[#921c17]"
                                        type="button"
                                        data-delete-user="true"
                                        data-user-name="<?= e($row['name'] ?: $row['email']) ?>"
                                        aria-label="Supprimer"
                                    >
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6v12a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V6M10 6V4a2 2 0 0 1 2-2h0a2 2 0 0 1 2 2v2"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?php
render_modal(
    'edit-modal',
    "Modifier l'utilisateur",
    'Edition',
    '
    <form method="post" class="mt-3 grid gap-4" id="edit-form">
        <input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="user_id" id="edit-user-id-modal">
        <label class="block text-sm font-medium text-[#2b2723]">
            Nom
            <input type="text" name="edit_name" id="edit-name"
                   class="mt-2 w-full rounded-2xl border border-[#e3d7cc] bg-white px-4 py-3 text-sm focus:border-[#1f2d3a] focus:outline-none">
        </label>
        <label class="block text-sm font-medium text-[#2b2723]">
            Email
            <input type="email" name="edit_email" id="edit-email" required
                   class="mt-2 w-full rounded-2xl border border-[#e3d7cc] bg-white px-4 py-3 text-sm focus:border-[#1f2d3a] focus:outline-none">
        </label>
        <label class="block text-sm font-medium text-[#2b2723]">
            Role
            <select name="edit_role" id="edit-role" class="mt-2 w-full rounded-2xl border border-[#e3d7cc] bg-white px-4 py-3 text-sm focus:border-[#1f2d3a] focus:outline-none">
                <option value="user">Utilisateur</option>
                <option value="admin">Admin</option>
            </select>
        </label>
        <label class="block text-sm font-medium text-[#2b2723]">
            Mot de passe (optionnel)
            <input type="password" name="edit_password" id="edit-password"
                   class="mt-2 w-full rounded-2xl border border-[#e3d7cc] bg-white px-4 py-3 text-sm focus:border-[#1f2d3a] focus:outline-none">
        </label>
        <label class="flex items-center gap-2 text-sm font-medium text-[#2b2723]">
            <input type="checkbox" name="edit_is_active" id="edit-active" class="h-4 w-4 rounded border-[#e3d7cc]">
            Compte actif
        </label>
        <div class="flex flex-wrap gap-3">
            <button class="rounded-full bg-[#1f2d3a] px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-[#1f2d3a]/30" type="submit">Enregistrer</button>
            <button type="button" id="edit-cancel" class="rounded-full border border-[#e3d7cc] px-6 py-3 text-sm font-semibold text-[#1f2d3a]">Annuler</button>
        </div>
    </form>
    '
);

render_modal(
    'create-modal',
    'Creer un compte',
    'Creation',
    '
    <form method="post" class="mt-3 grid gap-4" id="create-form">
        <input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">
        <input type="hidden" name="action" value="create">
        <label class="block text-sm font-medium text-[#2b2723]">
            Nom
            <input type="text" name="name"
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
        <div class="flex flex-wrap gap-3">
            <button class="rounded-full bg-[#1f2d3a] px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-[#1f2d3a]/30" type="submit">Creer</button>
            <button type="button" id="create-cancel" class="rounded-full border border-[#e3d7cc] px-6 py-3 text-sm font-semibold text-[#1f2d3a]">Annuler</button>
        </div>
    </form>
    '
);

render_modal(
    'delete-user-modal',
    'Supprimer ce compte ?',
    'Confirmation',
    '<p id="delete-user-name" class="mt-1 text-sm text-[#6d6258]"></p>',
    '<div class="flex flex-wrap items-center gap-3">
        <button id="delete-user-confirm" class="rounded-full bg-[#b3261e] px-4 py-2 text-sm font-semibold text-white hover:bg-[#921c17]" type="button">Supprimer</button>
        <button id="delete-user-cancel" class="rounded-full border border-[#e3d7cc] px-4 py-2 text-sm font-semibold text-[#1f2d3a]" type="button">Annuler</button>
    </div>'
);
?>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('edit-modal');
    const cancelBtn = document.getElementById('edit-cancel');
    const userIdField = document.getElementById('edit-user-id-modal');
    const nameInput = document.getElementById('edit-name');
    const emailInput = document.getElementById('edit-email');
    const roleSelect = document.getElementById('edit-role');
    const activeCheckbox = document.getElementById('edit-active');
    const passwordInput = document.getElementById('edit-password');
    const createModal = document.getElementById('create-modal');
    const createCancel = document.getElementById('create-cancel');
    const openCreate = document.getElementById('open-create');
    const createForm = document.getElementById('create-form');
    const deleteModal = document.getElementById('delete-user-modal');
    const deleteName = document.getElementById('delete-user-name');
    const deleteConfirm = document.getElementById('delete-user-confirm');
    const deleteCancel = document.getElementById('delete-user-cancel');
    if (!modal || !cancelBtn || !userIdField || !nameInput || !emailInput || !roleSelect || !activeCheckbox || !passwordInput || !deleteModal || !deleteName || !deleteConfirm || !deleteCancel || !createModal || !createCancel || !openCreate || !createForm) return;

    const closeModal = () => {
        modal.classList.add('hidden');
        passwordInput.value = '';
    };

    document.querySelectorAll('button[data-edit]').forEach((btn) => {
        btn.addEventListener('click', () => {
            userIdField.value = btn.dataset.id || '';
            nameInput.value = btn.dataset.name || '';
            emailInput.value = btn.dataset.email || '';
            roleSelect.value = btn.dataset.role || 'user';
            activeCheckbox.checked = btn.dataset.active === '1';
            passwordInput.value = '';
            modal.classList.remove('hidden');
        });
    });

    cancelBtn.addEventListener('click', (e) => {
        e.preventDefault();
        closeModal();
    });

    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            closeModal();
        }
    });

    const closeCreateModal = () => {
        createModal.classList.add('hidden');
        createForm.reset();
    };

    openCreate.addEventListener('click', () => {
        createModal.classList.remove('hidden');
    });

    createCancel.addEventListener('click', (e) => {
        e.preventDefault();
        closeCreateModal();
    });

    createModal.addEventListener('click', (e) => {
        if (e.target === createModal) {
            closeCreateModal();
        }
    });

    let deleteForm = null;
    document.querySelectorAll('button[data-delete-user]').forEach((btn) => {
        btn.addEventListener('click', () => {
            deleteForm = btn.closest('form');
            deleteName.textContent = btn.dataset.userName || 'Utilisateur';
            deleteModal.classList.remove('hidden');
        });
    });

    const closeDeleteModal = () => {
        deleteModal.classList.add('hidden');
        deleteForm = null;
    };

    deleteCancel.addEventListener('click', (e) => {
        e.preventDefault();
        closeDeleteModal();
    });

    deleteConfirm.addEventListener('click', (e) => {
        e.preventDefault();
        if (deleteForm) {
            deleteForm.submit();
        }
        closeDeleteModal();
    });

    deleteModal.addEventListener('click', (e) => {
        if (e.target === deleteModal) {
            closeDeleteModal();
        }
    });
});
</script>
<?php
$content = ob_get_clean();
require __DIR__ . '/../templates/app-shell.php';
