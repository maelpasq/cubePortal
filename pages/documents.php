<?php
$user = require_auth($pdo);
$title = 'Cube Portal - Documents';
$bodyClass = '';
$useTailwind = true;
$active = 'documents';
$integrations = require __DIR__ . '/../lib/integrations.php';
require __DIR__ . '/../templates/modal.php';
$pageEyebrow = 'Documents';
$pageTitle = 'Gestion des documents';
$pageLead = 'Deposez vos fichiers puis consultez ou telechargez-les.';

$uploadSuccess = [];
$uploadErrors = [];
$deleteSuccess = [];
$deleteErrors = [];
$tableReady = true;
$tableError = '';

// S'assure que la table existe pour stocker les fichiers en base.
try {
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS documents (
            id INT AUTO_INCREMENT PRIMARY KEY,
            filename VARCHAR(255) NOT NULL,
            mime_type VARCHAR(190) NOT NULL DEFAULT 'application/octet-stream',
            size_bytes BIGINT UNSIGNED NOT NULL DEFAULT 0,
            content LONGBLOB NOT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB"
    );
} catch (Throwable $e) {
    $tableReady = false;
    $tableError = "Impossible de preparer le stockage des documents : " . $e->getMessage();
}

function format_bytes(int $bytes): string
{
    $units = ['o', 'Ko', 'Mo', 'Go'];
    $i = 0;
    while ($bytes >= 1024 && $i < count($units) - 1) {
        $bytes /= 1024;
        $i++;
    }
    return round($bytes, 1) . ' ' . $units[$i];
}

function clean_filename(string $name): string
{
    $name = trim(str_replace(["\r", "\n"], ' ', $name));
    if ($name === '') {
        $name = 'document';
    }
    $short = function_exists('mb_substr') ? mb_substr($name, 0, 190) : substr($name, 0, 190);
    return $short === '' ? 'document' : $short;
}

if ($tableReady && is_post()) {
    $action = $_POST['action'] ?? 'upload';

    if (!csrf_verify($_POST['csrf_token'] ?? null)) {
        $uploadErrors[] = 'Jeton de securite invalide.';
    } elseif ($action === 'delete') {
        $docId = isset($_POST['document_id']) ? (int)$_POST['document_id'] : 0;
        if ($docId <= 0) {
            $deleteErrors[] = 'Document invalide.';
        } else {
            try {
                $stmt = $pdo->prepare('DELETE FROM documents WHERE id = :id');
                $stmt->execute([':id' => $docId]);
                if ($stmt->rowCount() > 0) {
                    $deleteSuccess[] = 'Document supprime.';
                } else {
                    $deleteErrors[] = 'Document introuvable.';
                }
            } catch (Throwable $e) {
                $deleteErrors[] = "Erreur lors de la suppression : " . $e->getMessage();
            }
        }
    } else {
        if (!isset($_FILES['documents'])) {
            $uploadErrors[] = 'Aucun fichier selectionne.';
        } else {
            $files = $_FILES['documents'];
            $total = is_array($files['name']) ? count($files['name']) : 0;

            for ($i = 0; $i < $total; $i++) {
                $error = $files['error'][$i] ?? UPLOAD_ERR_NO_FILE;
                $tmpName = $files['tmp_name'][$i] ?? '';
                $originalName = $files['name'][$i] ?? 'document';

                if ($error !== UPLOAD_ERR_OK || !is_uploaded_file($tmpName)) {
                    $uploadErrors[] = "Echec du televersement pour {$originalName}.";
                    continue;
                }

                $filename = clean_filename($originalName);
                $size = (int)($files['size'][$i] ?? 0);
                $mime = $files['type'][$i] ?? 'application/octet-stream';
                if (function_exists('mime_content_type')) {
                    $detected = mime_content_type($tmpName);
                    if ($detected) {
                        $mime = $detected;
                    }
                }

                $content = file_get_contents($tmpName);
                if ($content === false) {
                    $uploadErrors[] = "Impossible de lire le fichier {$originalName}.";
                    continue;
                }

                try {
                    $stmt = $pdo->prepare('INSERT INTO documents (filename, mime_type, size_bytes, content) VALUES (:filename, :mime, :size, :content)');
                    $stmt->bindValue(':filename', $filename, PDO::PARAM_STR);
                    $stmt->bindValue(':mime', $mime, PDO::PARAM_STR);
                    $stmt->bindValue(':size', $size, PDO::PARAM_INT);
                    $stmt->bindValue(':content', $content, PDO::PARAM_LOB);
                    $stmt->execute();
                    $uploadSuccess[] = $filename;
                } catch (Throwable $e) {
                    $uploadErrors[] = "Impossible d'enregistrer {$originalName} : " . $e->getMessage();
                }
            }
        }
    }
}

$documents = [];
if ($tableReady) {
    try {
        $stmt = $pdo->query('SELECT id, filename, mime_type, size_bytes, created_at FROM documents ORDER BY created_at DESC');
        $documents = $stmt->fetchAll() ?: [];
    } catch (Throwable $e) {
        $tableError = "Impossible de charger les documents : " . $e->getMessage();
        $tableReady = false;
    }
}

ob_start();
?>
<section class="mt-8 space-y-6">
    <article class="rounded-3xl border border-[#e3d7cc] bg-white p-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="text-sm font-semibold text-[#0f0f0f]">Deposer des documents</p>
                <p class="mt-2 text-sm text-[#6d6258]">Glissez vos fichiers ici ou cliquez pour les ajouter. Tous formats acceptes.</p>
            </div>
        </div>

        <?php if (!$tableReady && $tableError !== ''): ?>
            <div class="mt-6 rounded-2xl border border-[#f2b1b1] bg-[#ffe5e5] px-4 py-3 text-sm text-[#7d2b2b]">
                <?= e($tableError) ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($uploadSuccess)): ?>
            <div class="mt-6 rounded-2xl border border-[#d3e6d5] bg-[#f1fbf3] px-4 py-3 text-sm text-[#2f6b3a]">
                Fichiers ajoutes: <?= e(implode(', ', $uploadSuccess)) ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($uploadErrors)): ?>
            <div class="mt-6 rounded-2xl border border-[#f2b1b1] bg-[#ffe5e5] px-4 py-3 text-sm text-[#7d2b2b]">
                <?= e(implode(' ', $uploadErrors)) ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($deleteSuccess)): ?>
            <div class="mt-6 rounded-2xl border border-[#d3e6d5] bg-[#f1fbf3] px-4 py-3 text-sm text-[#2f6b3a]">
                <?= e(implode(' ', $deleteSuccess)) ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($deleteErrors)): ?>
            <div class="mt-6 rounded-2xl border border-[#f2b1b1] bg-[#ffe5e5] px-4 py-3 text-sm text-[#7d2b2b]">
                <?= e(implode(' ', $deleteErrors)) ?>
            </div>
        <?php endif; ?>

        <form id="documents-form" class="mt-6 space-y-4" method="post" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="action" value="upload">
            <label id="drop-area" class="relative flex h-44 cursor-pointer flex-col items-center justify-center gap-3 rounded-3xl border border-dashed border-[#d3c6ba] bg-[#f9f3ed] px-4 text-center text-sm text-[#6d6258] hover:border-[#1f2d3a] hover:text-[#1f2d3a]">
                <input id="documents-input" class="absolute inset-0 h-full w-full cursor-pointer opacity-0" type="file" name="documents[]" multiple accept="*/*">
                <span class="text-sm font-semibold text-[#0f0f0f]">Glisser-deposer vos fichiers</span>
                <span class="text-xs text-[#6d6258]">Ou cliquez pour parcourir • Tous formats</span>
            </label>
            <div id="pending-list" class="rounded-2xl border border-[#e3d7cc] bg-[#f6f1eb] px-4 py-4 text-sm text-[#6d6258] hidden"></div>
            <button
                id="import-button"
                class="hidden rounded-full px-6 py-3 text-sm font-semibold text-white <?= $tableReady ? 'bg-[#1f2d3a] shadow-lg shadow-[#1f2d3a]/30' : 'bg-[#b8b0a7] cursor-not-allowed' ?>"
                type="submit"
                <?= $tableReady ? '' : 'disabled aria-disabled="true"' ?>
            >
                Importer les fichiers
            </button>
        </form>

        <div class="mt-8 border-t border-[#efe7df] pt-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-[#0f0f0f]">Documents disponibles</p>
                    <p class="mt-2 text-sm text-[#6d6258]"><?= count($documents) ?> fichier(s) disponible(s)</p>
                </div>
            </div>

            <?php if (!$tableReady && $tableError !== ''): ?>
                <div class="mt-6 rounded-2xl border border-[#f2b1b1] bg-[#ffe5e5] px-4 py-3 text-sm text-[#7d2b2b]">
                    <?= e($tableError) ?>
                </div>
            <?php elseif (empty($documents)): ?>
                <div class="mt-6 rounded-2xl border border-[#e3d7cc] bg-[#f9f3ed] px-4 py-4 text-sm text-[#6d6258]">
                    Aucun fichier pour le moment. Deposez vos documents pour les retrouver ici.
                </div>
            <?php else: ?>
                <ul class="mt-6 divide-y divide-[#efe7df]">
                    <?php foreach ($documents as $document): ?>
                        <li class="flex flex-col gap-3 py-3 sm:flex-row sm:items-center sm:justify-between sm:gap-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-[#f6f1eb] text-xs font-semibold text-[#1f2d3a]">
                                    <?= e(strtoupper(pathinfo($document['filename'] ?? 'FILE', PATHINFO_EXTENSION) ?: 'FILE')) ?>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-[#0f0f0f]"><?= e($document['filename'] ?? 'Document') ?></p>
                                    <p class="text-xs text-[#6d6258]"><?= e(format_bytes((int)($document['size_bytes'] ?? 0))) ?> • <?= e(date('d/m/Y H:i', strtotime($document['created_at'] ?? 'now'))) ?></p>
                                </div>
                            </div>
                            <div class="flex flex-wrap items-center gap-2 text-sm">
                                <a class="rounded-full border border-[#e3d7cc] px-3 py-2 text-[#1f2d3a]" href="/documents/download?id=<?= e((string)$document['id']) ?>" target="_blank" rel="noreferrer">Ouvrir</a>
                                <a class="rounded-full bg-[#1f2d3a] px-3 py-2 font-semibold text-white" href="/documents/download?id=<?= e((string)$document['id']) ?>&download=1">Telecharger</a>
                                <form method="post" class="inline">
                                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="document_id" value="<?= e((string)$document['id']) ?>">
                                    <button class="rounded-full bg-[#b3261e] px-3 py-2 font-semibold text-white hover:bg-[#921c17]" type="submit" data-delete="true" data-doc-name="<?= e($document['filename'] ?? 'Document') ?>">
                                        Supprimer
                                    </button>
                                </form>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </article>
</section>
<?php
render_modal(
    'delete-modal',
    'Supprimer ce document ?',
    'Confirmation',
    '<p id="delete-modal-name" class="mt-1 text-sm text-[#6d6258]"></p>',
    '<div class="flex flex-wrap items-center gap-3">
        <button id="confirm-delete" class="rounded-full bg-[#b3261e] px-4 py-2 text-sm font-semibold text-white hover:bg-[#921c17]" type="button">Supprimer</button>
        <button id="cancel-delete" class="rounded-full border border-[#e3d7cc] px-4 py-2 text-sm font-semibold text-[#1f2d3a]" type="button">Annuler</button>
    </div>'
);
?>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('documents-input');
    const dropArea = document.getElementById('drop-area');
    const pendingList = document.getElementById('pending-list');
    const importButton = document.getElementById('import-button');
    const deleteModal = document.getElementById('delete-modal');
    const deleteModalName = document.getElementById('delete-modal-name');
    const confirmDeleteBtn = document.getElementById('confirm-delete');
    const cancelDeleteBtn = document.getElementById('cancel-delete');
    if (!input || !dropArea || !pendingList || !importButton || !deleteModal || !deleteModalName || !confirmDeleteBtn || !cancelDeleteBtn) return;

    let formToDelete = null;

    let dataTransfer = new DataTransfer();

    const refreshList = () => {
        const files = dataTransfer.files;
        if (!files || files.length === 0) {
            pendingList.classList.add('hidden');
            pendingList.innerHTML = '';
            importButton.classList.add('hidden');
            return;
        }
        const items = [];
        for (let i = 0; i < files.length; i++) {
            const f = files[i];
            const size = (f.size / 1024 / 1024) >= 1 ? (f.size / 1024 / 1024).toFixed(1) + ' Mo' : (f.size / 1024).toFixed(0) + ' Ko';
            items.push(
                `<div class="flex items-center justify-between gap-3 border-b border-[#efe7df] py-2 last:border-0">
                    <div class="text-left">
                        <p class="text-sm font-semibold text-[#0f0f0f]">${f.name}</p>
                        <p class="text-xs text-[#6d6258]">${size}</p>
                    </div>
                    <button type="button" class="rounded-full bg-[#b3261e] px-3 py-1 text-xs font-semibold text-white hover:bg-[#921c17]" data-index="${i}">Supprimer</button>
                </div>`
            );
        }
        pendingList.innerHTML = items.join('');
        pendingList.classList.remove('hidden');
        importButton.classList.remove('hidden');
    };

    const setFiles = (fileList) => {
        dataTransfer = new DataTransfer();
        for (let i = 0; i < fileList.length; i++) {
            dataTransfer.items.add(fileList[i]);
        }
        input.files = dataTransfer.files;
        refreshList();
    };

    input.addEventListener('change', (e) => {
        const files = e.target.files;
        if (!files) return;
        const merged = Array.from(dataTransfer.files);
        merged.push(...Array.from(files));
        setFiles(merged);
    });

    pendingList.addEventListener('click', (e) => {
        const target = e.target;
        if (!(target instanceof HTMLElement)) return;
        if (target.dataset.index !== undefined) {
            const idx = parseInt(target.dataset.index, 10);
            const remaining = Array.from(dataTransfer.files).filter((_, i) => i !== idx);
            setFiles(remaining);
        }
    });

    ['dragenter', 'dragover'].forEach(evt =>
        dropArea.addEventListener(evt, (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropArea.classList.add('border-[#1f2d3a]', 'text-[#1f2d3a]');
        })
    );
    ['dragleave', 'drop'].forEach(evt =>
        dropArea.addEventListener(evt, (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropArea.classList.remove('border-[#1f2d3a]', 'text-[#1f2d3a]');
        })
    );
    dropArea.addEventListener('drop', (e) => {
        const files = e.dataTransfer?.files;
        if (!files || files.length === 0) return;
        const merged = Array.from(dataTransfer.files);
        merged.push(...Array.from(files));
        setFiles(merged);
    });

    // Gestion modale suppression
    document.querySelectorAll('button[data-delete]').forEach((btn) => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const form = btn.closest('form');
            if (!form) return;
            formToDelete = form;
            deleteModalName.textContent = btn.dataset.docName || 'Document';
            deleteModal.classList.remove('hidden');
        });
    });

    const closeModal = () => {
        deleteModal.classList.add('hidden');
        formToDelete = null;
    };

    cancelDeleteBtn.addEventListener('click', (e) => {
        e.preventDefault();
        closeModal();
    });

    confirmDeleteBtn.addEventListener('click', (e) => {
        e.preventDefault();
        if (formToDelete) {
            formToDelete.submit();
        }
        closeModal();
    });

    deleteModal.addEventListener('click', (e) => {
        if (e.target === deleteModal) {
            closeModal();
        }
    });
});
</script>
<?php
$content = ob_get_clean();
require __DIR__ . '/../templates/app-shell.php';
