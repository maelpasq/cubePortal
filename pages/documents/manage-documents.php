<?php
$user = require_auth($pdo);
$title = 'Cube Portal - Documents';
$bodyClass = '';
$useTailwind = true;
$active = 'documents';
$integrations = require __DIR__ . '/../../lib/integrations.php';
require __DIR__ . '/../../templates/modal.php';
$pageEyebrow = 'Documents';
$pageTitle = 'Gestion des documents';
$pageLead = 'Déposez vos fichiers puis consultez ou téléchargez-les.';

$uploadSuccess = [];
$uploadErrors = [];
$deleteSuccess = [];
$deleteErrors = [];
$maxFiles = 5;
$maxFileSize = 20 * 1024 * 1024; // 20 Mo
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
        $uploadErrors[] = 'Jeton de sécurité invalide.';
    } elseif ($action === 'bulk_delete') {
        $ids = array_filter(array_map('intval', $_POST['selected_ids'] ?? []));
        if (empty($ids)) {
            $deleteErrors[] = 'Aucun document sélectionné.';
        } else {
            $in = implode(',', array_fill(0, count($ids), '?'));
            $stmt = $pdo->prepare("DELETE FROM documents WHERE id IN ($in)");
            if ($stmt->execute($ids)) {
                $deleteSuccess[] = 'Documents supprimés.';
            } else {
                $deleteErrors[] = 'Erreur lors de la suppression multiple.';
            }
        }
    } elseif ($action === 'delete') {
        $docId = isset($_POST['document_id']) ? (int)$_POST['document_id'] : 0;
        if ($docId <= 0) {
            $deleteErrors[] = 'Document invalide.';
        } else {
            try {
                $stmt = $pdo->prepare('DELETE FROM documents WHERE id = :id');
                $stmt->execute([':id' => $docId]);
                if ($stmt->rowCount() > 0) {
                    $deleteSuccess[] = 'Document supprimé.';
                } else {
                    $deleteErrors[] = 'Document introuvable.';
                }
            } catch (Throwable $e) {
                $deleteErrors[] = "Erreur lors de la suppression : " . $e->getMessage();
            }
        }
    } else {
        if (!isset($_FILES['documents'])) {
            $uploadErrors[] = 'Aucun fichier sélectionné.';
        } else {
            $files = $_FILES['documents'];
            $total = is_array($files['name']) ? count($files['name']) : 0;

            if ($total > $maxFiles) {
                $uploadErrors[] = "Maximum {$maxFiles} fichiers par envoi.";
            }

            for ($i = 0; $i < $total; $i++) {
                if ($i >= $maxFiles) {
                    break;
                }
                $error = $files['error'][$i] ?? UPLOAD_ERR_NO_FILE;
                $tmpName = $files['tmp_name'][$i] ?? '';
                $originalName = $files['name'][$i] ?? 'document';
                $size = (int)($files['size'][$i] ?? 0);

                if ($error !== UPLOAD_ERR_OK || !is_uploaded_file($tmpName)) {
                    $uploadErrors[] = "Échec du téléversement pour {$originalName}.";
                    continue;
                }

                if ($size > $maxFileSize) {
                    $uploadErrors[] = "{$originalName} depasse la taille maximale de 20 Mo.";
                    continue;
                }

                $filename = clean_filename($originalName);
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
                    <p class="text-sm font-semibold text-[#0f0f0f]">Déposer des documents</p>
                    <p class="mt-2 text-sm text-[#6d6258]">Glissez vos fichiers ici ou cliquez pour les ajouter. Tous formats acceptés.</p>
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
                <span class="text-xs text-[#6d6258]">Ou cliquez pour parcourir - Tous formats - Max 5 fichiers - 20 Mo/fichier</span>
            </label>
            <div id="pending-list" class="rounded-2xl border border-[#e3d7cc] bg-[#f6f1eb] px-4 py-4 text-sm text-[#6d6258] hidden"></div>
            <button
                id="import-button"
                class="hidden inline-flex items-center gap-2 rounded-full px-6 py-3 text-sm font-semibold text-white <?= $tableReady ? 'bg-[#1f2d3a] shadow-lg shadow-[#1f2d3a]/30' : 'bg-[#b8b0a7] cursor-not-allowed' ?>"
                type="submit"
                <?= $tableReady ? '' : 'disabled aria-disabled="true"' ?>
            >
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
                Importer les fichiers
            </button>
        </form>

        <div class="mt-8 border-t border-[#efe7df] pt-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm font-semibold text-[#0f0f0f]">Documents disponibles</p>
                    <p class="mt-2 text-sm text-[#6d6258]"><?= count($documents) ?> fichier(s) disponible(s)</p>
                </div>
                <?php if (!empty($documents)): ?>
                    <div class="flex items-center gap-2">
                        <button id="toggle-select" type="button" class="inline-flex items-center gap-2 rounded-full border border-[#e3d7cc] px-4 py-2 text-sm font-semibold text-[#1f2d3a]">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 5h18M3 12h18M3 19h18"/></svg>
                            Sélectionner
                        </button>
                        <button id="bulk-delete-btn" class="hidden inline-flex items-center gap-2 rounded-full bg-[#b3261e] px-4 py-2 text-sm font-semibold text-white hover:bg-[#921c17]" type="submit" form="documents-list-form" disabled>
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6v12a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V6M10 6V4a2 2 0 0 1 2-2h0a2 2 0 0 1 2 2v2"/></svg>
                            Supprimer
                        </button>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (!$tableReady && $tableError !== ''): ?>
                <div class="mt-6 rounded-2xl border border-[#f2b1b1] bg-[#ffe5e5] px-4 py-3 text-sm text-[#7d2b2b]">
                    <?= e($tableError) ?>
                </div>
            <?php elseif (empty($documents)): ?>
                <div class="mt-6 rounded-2xl border border-[#e3d7cc] bg-[#f9f3ed] px-4 py-4 text-sm text-[#6d6258]">
                    Aucun fichier pour le moment. Déposez vos documents pour les retrouver ici.
                </div>
            <?php else: ?>
                <ul class="mt-6 divide-y divide-[#efe7df]">
                    <?php foreach ($documents as $document): ?>
                        <li class="document-row flex flex-col gap-3 rounded-2xl py-3 transition sm:flex-row sm:items-center sm:justify-between sm:gap-4">
                            <div class="flex items-center gap-3">
                                <input type="checkbox" form="documents-list-form" name="selected_ids[]" value="<?= e((string)$document['id']) ?>" class="selection-checkbox hidden h-4 w-4 rounded border-[#e3d7cc] text-[#1f2d3a] focus:ring-0">
                                <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-[#f6f1eb] text-xs font-semibold text-[#1f2d3a]">
                                    <?= e(strtoupper(pathinfo($document['filename'] ?? 'FILE', PATHINFO_EXTENSION) ?: 'FILE')) ?>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-[#0f0f0f]"><?= e($document['filename'] ?? 'Document') ?></p>
                                    <p class="text-xs text-[#6d6258]"><?= e(format_bytes((int)($document['size_bytes'] ?? 0))) ?> - <?= e(date('d/m/Y H:i', strtotime($document['created_at'] ?? 'now'))) ?></p>
                                </div>
                            </div>
                        <div class="flex flex-wrap items-center gap-2 text-sm">
                                <a class="rounded-full border border-[#e3d7cc] p-2 text-[#1f2d3a]" href="/documents/download?id=<?= e((string)$document['id']) ?>" target="_blank" rel="noreferrer" aria-label="Ouvrir">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
                                </a>
                                    <a class="rounded-full bg-[#1f2d3a] p-2 font-semibold text-white" href="/documents/download?id=<?= e((string)$document['id']) ?>&download=1" aria-label="Télécharger">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M19 13l-7 7-7-7"/></svg>
                                </a>
                                <form method="post" class="inline">
                                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="document_id" value="<?= e((string)$document['id']) ?>">
                                    <button class="rounded-full bg-[#b3261e] p-2 font-semibold text-white hover:bg-[#921c17]" type="submit" data-delete="true" data-doc-name="<?= e($document['filename'] ?? 'Document') ?>" aria-label="Supprimer">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6v12a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V6M10 6V4a2 2 0 0 1 2-2h0a2 2 0 0 1 2 2v2"/></svg>
                                    </button>
                                </form>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <form id="documents-list-form" method="post" class="hidden">
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="action" value="bulk_delete">
                </form>
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
        <button id="confirm-delete" class="inline-flex items-center gap-2 rounded-full bg-[#b3261e] px-4 py-2 text-sm font-semibold text-white hover:bg-[#921c17]" type="button">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6v12a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V6M10 6V4a2 2 0 0 1 2-2h0a2 2 0 0 1 2 2v2"/></svg>
            Supprimer
        </button>
        <button id="cancel-delete" class="inline-flex items-center gap-2 rounded-full border border-[#e3d7cc] px-4 py-2 text-sm font-semibold text-[#1f2d3a]" type="button">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 6l12 12M18 6L6 18"/></svg>
            Annuler
        </button>
    </div>'
);

render_modal(
    'limit-modal',
    'Trop de fichiers',
    'Sélection',
    '<p class="text-sm text-[#6d6258]">Choisissez jusqu\'a 5 fichiers a conserver.</p>
            <div id="limit-error" class="mt-2 text-sm text-[#b3261e] hidden"></div>
            <div id="limit-files-list" class="mt-3 max-h-64 space-y-2 overflow-auto"></div>',
    '<div class="flex flex-wrap items-center gap-3">
        <button id="limit-confirm" class="inline-flex items-center gap-2 rounded-full bg-[#1f2d3a] px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-[#1f2d3a]/30" type="button">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12l5 5L20 7"/></svg>
            Valider
        </button>
        <button id="limit-cancel" class="inline-flex items-center gap-2 rounded-full border border-[#e3d7cc] px-4 py-2 text-sm font-semibold text-[#1f2d3a]" type="button">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 6l12 12M18 6L6 18"/></svg>
            Annuler
        </button>
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
    const limitModal = document.getElementById('limit-modal');
    const limitList = document.getElementById('limit-files-list');
    const limitError = document.getElementById('limit-error');
    const limitConfirm = document.getElementById('limit-confirm');
    const limitCancel = document.getElementById('limit-cancel');
    const toggleSelectBtn = document.getElementById('toggle-select');
    const documentsListForm = document.getElementById('documents-list-form');
    const bulkDeleteBtn = document.getElementById('bulk-delete-btn');
    if (!input || !dropArea || !pendingList || !importButton || !deleteModal || !deleteModalName || !confirmDeleteBtn || !cancelDeleteBtn || !limitModal || !limitList || !limitError || !limitConfirm || !limitCancel) return;

    const MAX_FILES = 5;
    let formToDelete = null;
    let dataTransfer = new DataTransfer();
    let overLimitFiles = [];
    let selectionMode = false;

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

    const setFiles = (filesArr) => {
        dataTransfer = new DataTransfer();
        filesArr.slice(0, MAX_FILES).forEach((f) => dataTransfer.items.add(f));
        input.files = dataTransfer.files;
        refreshList();
    };

    const buildLimitList = (filesArr) => {
        const items = filesArr.map((f, idx) => {
            const size = (f.size / 1024 / 1024) >= 1 ? (f.size / 1024 / 1024).toFixed(1) + ' Mo' : (f.size / 1024).toFixed(0) + ' Ko';
            const checked = idx < MAX_FILES ? 'checked' : '';
            return `<label class="flex items-center gap-3 rounded-2xl border border-[#e3d7cc] px-3 py-2">
                <input type="checkbox" class="limit-checkbox h-4 w-4 rounded border-[#e3d7cc] text-[#1f2d3a] focus:ring-0" data-index="${idx}" ${checked}>
                <div class="flex flex-col">
                    <span class="text-sm font-semibold text-[#0f0f0f]">${f.name}</span>
                    <span class="text-xs text-[#6d6258]">${size}</span>
                </div>
            </label>`;
        });
        limitList.innerHTML = items.join('');
    };

    const openLimitModal = (filesArr) => {
        overLimitFiles = filesArr;
        buildLimitList(filesArr);
        limitError.classList.add('hidden');
        limitModal.classList.remove('hidden');
    };

    const closeLimitModal = () => {
        overLimitFiles = [];
        limitModal.classList.add('hidden');
    };

    const handleNewFiles = (newFiles) => {
        const merged = Array.from(dataTransfer.files);
        merged.push(...Array.from(newFiles));
        if (merged.length > MAX_FILES) {
            openLimitModal(merged);
        } else {
            setFiles(merged);
        }
    };

    input.addEventListener('change', (e) => {
        const files = e.target.files;
        if (!files) return;
        handleNewFiles(files);
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
        handleNewFiles(files);
    });

    // Limit modal logic
    limitCancel.addEventListener('click', (e) => {
        e.preventDefault();
        closeLimitModal();
    });

    limitModal.addEventListener('click', (e) => {
        if (e.target === limitModal) {
            closeLimitModal();
        }
    });

    limitConfirm.addEventListener('click', (e) => {
        e.preventDefault();
        const checkboxes = Array.from(limitList.querySelectorAll('.limit-checkbox'));
        const selectedIndexes = checkboxes
            .filter((cb) => cb.checked)
            .map((cb) => parseInt(cb.dataset.index || '0', 10));
        if (selectedIndexes.length === 0 || selectedIndexes.length > MAX_FILES) {
            limitError.textContent = `Sélectionnez jusqu'à ${MAX_FILES} fichiers.`;
            limitError.classList.remove('hidden');
            return;
        }
        const chosen = selectedIndexes.map((idx) => overLimitFiles[idx]).filter(Boolean);
        setFiles(chosen);
        closeLimitModal();
    });

    // Gestion modale suppression (unitaire)
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

    const closeDeleteModal = () => {
        deleteModal.classList.add('hidden');
        formToDelete = null;
    };

    cancelDeleteBtn.addEventListener('click', (e) => {
        e.preventDefault();
        closeDeleteModal();
    });

    confirmDeleteBtn.addEventListener('click', (e) => {
        e.preventDefault();
        if (formToDelete) {
            formToDelete.submit();
        }
        closeDeleteModal();
    });

    deleteModal.addEventListener('click', (e) => {
        if (e.target === deleteModal) {
            closeDeleteModal();
        }
    });

    // Mode selection multiple pour suppression
    if (toggleSelectBtn && documentsListForm && bulkDeleteBtn) {
        const checkboxes = Array.from(document.querySelectorAll('.selection-checkbox'));
        const updateBulkState = () => {
            const anyChecked = checkboxes.some((cb) => cb.checked);
            bulkDeleteBtn.disabled = !anyChecked;
            checkboxes.forEach((cb) => {
                const row = cb.closest('.document-row');
                if (!row) return;
                if (cb.checked) {
                    row.classList.add('bg-[#efe7df]', 'border', 'border-[#1f2d3a]/50', 'shadow-sm', 'ring-2', 'ring-[#1f2d3a]/15');
                } else {
                    row.classList.remove('bg-[#efe7df]', 'border', 'border-[#1f2d3a]/50', 'shadow-sm', 'ring-2', 'ring-[#1f2d3a]/15');
                }
            });
        };
        toggleSelectBtn.addEventListener('click', () => {
            selectionMode = !selectionMode;
            if (selectionMode) {
                toggleSelectBtn.classList.add('bg-[#1f2d3a]', 'text-white');
                bulkDeleteBtn.classList.remove('hidden');
                checkboxes.forEach((cb) => {
                    cb.classList.remove('hidden');
                    const row = cb.closest('.document-row');
                    if (row) row.classList.add('cursor-pointer');
                });
            } else {
                toggleSelectBtn.classList.remove('bg-[#1f2d3a]', 'text-white');
                bulkDeleteBtn.classList.add('hidden');
                checkboxes.forEach((cb) => {
                    cb.checked = false;
                    cb.classList.add('hidden');
                    const row = cb.closest('.document-row');
                    if (row) row.classList.remove('bg-[#efe7df]', 'border', 'border-[#1f2d3a]/50', 'shadow-sm', 'ring-2', 'ring-[#1f2d3a]/15', 'cursor-pointer');
                });
                bulkDeleteBtn.disabled = true;
            }
            updateBulkState();
        });
        checkboxes.forEach((cb) => {
            cb.addEventListener('change', updateBulkState);
            const row = cb.closest('.document-row');
            if (row) {
                row.addEventListener('click', (e) => {
                    if (!selectionMode) return;
                    const target = e.target;
                    if (target instanceof HTMLElement && target.closest('a, button')) {
                        return;
                    }
                    cb.checked = !cb.checked;
                    updateBulkState();
                });
            }
        });
        bulkDeleteBtn.addEventListener('click', (e) => {
            if (bulkDeleteBtn.disabled) {
                e.preventDefault();
                return;
            }
            documentsListForm.submit();
        });
    }
});
</script>
<?php
$content = ob_get_clean();
require __DIR__ . '/../../templates/app-shell.php';
