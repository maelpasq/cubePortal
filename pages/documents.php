<?php
$user = require_auth($pdo);
$title = 'Cube Portal - Documents';
$bodyClass = '';
$useTailwind = true;
$active = 'documents';
$integrations = require __DIR__ . '/../lib/integrations.php';
$pageEyebrow = 'Documents';
$pageTitle = 'Gestion des documents';
$pageLead = 'Deposez vos fichiers puis consultez ou telechargez-les.';

$documentsDir = __DIR__ . '/../assets/documents';
if (!is_dir($documentsDir)) {
    mkdir($documentsDir, 0775, true);
}

$uploadSuccess = [];
$uploadErrors = [];

function format_bytes(int $bytes): string {
    $units = ['o', 'Ko', 'Mo', 'Go'];
    $i = 0;
    while ($bytes >= 1024 && $i < count($units) - 1) {
        $bytes /= 1024;
        $i++;
    }
    return round($bytes, 1) . ' ' . $units[$i];
}

function sanitize_filename(string $name): string {
    $base = pathinfo($name, PATHINFO_FILENAME);
    $ext = pathinfo($name, PATHINFO_EXTENSION);
    $base = preg_replace('/[^A-Za-z0-9_-]/', '_', $base);
    $ext = $ext !== '' ? '.' . preg_replace('/[^A-Za-z0-9]/', '', $ext) : '';
    if ($base === '') {
        $base = 'document';
    }
    return $base . $ext;
}

if (is_post()) {
    if (!csrf_verify($_POST['csrf_token'] ?? null)) {
        $uploadErrors[] = 'Jeton de securite invalide.';
    } elseif (!isset($_FILES['documents'])) {
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

            $safeName = sanitize_filename($originalName);
            $target = $documentsDir . '/' . $safeName;
            $suffix = 1;
            while (file_exists($target)) {
                $target = $documentsDir . '/' . pathinfo($safeName, PATHINFO_FILENAME) . '-' . $suffix;
                $extension = pathinfo($safeName, PATHINFO_EXTENSION);
                if ($extension !== '') {
                    $target .= '.' . $extension;
                }
                $suffix++;
            }

            if (!move_uploaded_file($tmpName, $target)) {
                $uploadErrors[] = "Impossible d'enregistrer {$originalName}.";
                continue;
            }

            $uploadSuccess[] = basename($target);
        }
    }
}

$documents = [];
$paths = glob($documentsDir . '/*') ?: [];
foreach ($paths as $path) {
    if (!is_file($path)) {
        continue;
    }
    $name = basename($path);
    $timestamp = (int)filemtime($path);
    $documents[] = [
        'name' => $name,
        'url' => '/assets/documents/' . rawurlencode($name),
        'size' => format_bytes((int)filesize($path)),
        'updated' => date('d/m/Y H:i', $timestamp),
        'timestamp' => $timestamp,
        'extension' => strtoupper(pathinfo($path, PATHINFO_EXTENSION) ?: 'FILE'),
    ];
}

usort($documents, function ($a, $b) {
    return ($b['timestamp'] ?? 0) <=> ($a['timestamp'] ?? 0);
});

ob_start();
?>
<section class="mt-8 grid gap-6 lg:grid-cols-[1.2fr_1fr]">
    <article class="rounded-3xl border border-[#e3d7cc] bg-white p-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="text-sm font-semibold text-[#0f0f0f]">Deposer des documents</p>
                <p class="mt-2 text-sm text-[#6d6258]">Glissez vos fichiers ici ou cliquez pour les ajouter. Tous formats acceptes.</p>
            </div>
        </div>

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

        <form class="mt-6 space-y-4" method="post" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <label class="relative flex h-44 cursor-pointer flex-col items-center justify-center gap-3 rounded-3xl border border-dashed border-[#d3c6ba] bg-[#f9f3ed] px-4 text-center text-sm text-[#6d6258] hover:border-[#1f2d3a] hover:text-[#1f2d3a]">
                <input class="absolute inset-0 h-full w-full cursor-pointer opacity-0" type="file" name="documents[]" multiple accept="*/*">
                <span class="text-sm font-semibold text-[#0f0f0f]">Glisser-deposer vos fichiers</span>
                <span class="text-xs text-[#6d6258]">Ou cliquez pour parcourir • Tous formats</span>
            </label>
            <button class="rounded-full bg-[#1f2d3a] px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-[#1f2d3a]/30" type="submit">
                Importer les fichiers
            </button>
        </form>
    </article>

    <article class="rounded-3xl border border-[#e3d7cc] bg-white p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-semibold text-[#0f0f0f]">Documents disponibles</p>
                <p class="mt-2 text-sm text-[#6d6258]"><?= count($documents) ?> fichier(s) disponible(s)</p>
            </div>
        </div>

        <?php if (empty($documents)): ?>
            <div class="mt-6 rounded-2xl border border-[#e3d7cc] bg-[#f9f3ed] px-4 py-4 text-sm text-[#6d6258]">
                Aucun fichier pour le moment. Deposez vos documents pour les retrouver ici.
            </div>
        <?php else: ?>
            <ul class="mt-6 divide-y divide-[#efe7df]">
                <?php foreach ($documents as $document): ?>
                    <li class="flex items-center justify-between gap-4 py-3">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-[#f6f1eb] text-xs font-semibold text-[#1f2d3a]">
                                <?= e($document['extension']) ?>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-[#0f0f0f]"><?= e($document['name']) ?></p>
                                <p class="text-xs text-[#6d6258]"><?= e($document['size']) ?> • <?= e($document['updated']) ?></p>
                            </div>
                        </div>
                        <div class="flex flex-shrink-0 items-center gap-2 text-sm">
                            <a class="rounded-full border border-[#e3d7cc] px-3 py-2 text-[#1f2d3a]" href="<?= e($document['url']) ?>" target="_blank" rel="noreferrer">Ouvrir</a>
                            <a class="rounded-full bg-[#1f2d3a] px-3 py-2 font-semibold text-white" href="<?= e($document['url']) ?>" download>Telecharger</a>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </article>
</section>
<?php
$content = ob_get_clean();
require __DIR__ . '/../templates/app-shell.php';
