<?php
$title = $title ?? 'Cube Portal';
$bodyClass = $bodyClass ?? '';
$useTailwind = $useTailwind ?? true;
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?></title>
    <?php if ($useTailwind): ?>
        <script src="https://cdn.tailwindcss.com"></script>
    <?php endif; ?>
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body class="<?= e($bodyClass) ?>">
