<?php
declare(strict_types=1);

/**
 * Render a reusable modal.
 *
 * @param string $id        Unique DOM id for the modal overlay.
 * @param string $title     Modal title.
 * @param string $eyebrow   Small label above the title.
 * @param string $bodyHtml  Body HTML content.
 * @param string $footerHtml Optional footer HTML (actions).
 */
function render_modal(string $id, string $title, string $eyebrow, string $bodyHtml, string $footerHtml = ''): void
{
    ?>
    <div id="<?= e($id) ?>" class="fixed inset-0 z-40 hidden items-center justify-center bg-black/30 px-4 flex">
        <div class="w-full max-w-lg rounded-3xl border border-[#e3d7cc] bg-white p-6 shadow-2xl">
            <?php if ($eyebrow !== ''): ?>
                <p class="text-xs uppercase tracking-[0.3em] text-[#a09082]"><?= e($eyebrow) ?></p>
            <?php endif; ?>
            <?php if ($title !== ''): ?>
                <h3 class="mt-3 text-lg font-semibold text-[#0f0f0f]"><?= e($title) ?></h3>
            <?php endif; ?>
            <div class="mt-3 text-sm text-[#6d6258] modal-body">
                <?= $bodyHtml ?>
            </div>
            <?php if ($footerHtml !== ''): ?>
                <div class="mt-6 modal-footer">
                    <?= $footerHtml ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php
}
