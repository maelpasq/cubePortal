<?php
$pageEyebrow = $pageEyebrow ?? '';
$pageTitle = $pageTitle ?? '';
$pageLead = $pageLead ?? '';
$content = $content ?? '';
$integrations = $integrations ?? [];

require __DIR__ . '/head.php';
?>
<div class="min-h-screen w-full bg-[#f6f1eb]">
    <div class="grid min-h-screen w-full grid-cols-1 lg:grid-cols-[300px_1fr]">
        <?php require __DIR__ . '/sidebar.php'; ?>
        <main class="px-6 py-10 lg:px-10">
            <?php if ($pageTitle !== '' || $pageLead !== ''): ?>
                <header class="flex flex-col gap-3">
                    <?php if ($pageEyebrow !== ''): ?>
                        <p class="text-xs uppercase tracking-[0.4em] text-[#6d6258]"><?= e($pageEyebrow) ?></p>
                    <?php endif; ?>
                    <?php if ($pageTitle !== ''): ?>
                        <h1 class="text-3xl font-semibold text-[#0f0f0f]"><?= e($pageTitle) ?></h1>
                    <?php endif; ?>
                    <?php if ($pageLead !== ''): ?>
                        <p class="text-sm text-[#6d6258]"><?= e($pageLead) ?></p>
                    <?php endif; ?>
                </header>
            <?php endif; ?>

            <?= $content ?>
        </main>
    </div>
</div>
<?php require __DIR__ . '/footer.php'; ?>
