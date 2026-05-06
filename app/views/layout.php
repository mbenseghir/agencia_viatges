<?php $messages = flash(); ?>
<!doctype html>
<html lang="ca">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? config('app', 'name')) ?> · <?= e(config('app', 'name')) ?></title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header class="site-header">
    <div class="container header-inner">
        <a class="brand" href="<?= url('home') ?>">
            <span class="brand-mark">PT</span>
            <span><?= e(config('app', 'name')) ?></span>
        </a>
        <nav class="nav">
            <a href="<?= url('home') ?>">Promocions</a>
            <?php if (is_admin()): ?>
                <a href="<?= url('admin/dashboard') ?>">Administració</a>
                <a href="<?= url('admin/reservas') ?>">Reserves</a>
                <a href="<?= url('auth/logout') ?>">Sortir</a>
            <?php else: ?>
                <a href="<?= url('auth/login') ?>">Accés intern</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<main class="container main">
    <?php if (!empty($messages)): ?>
        <div class="alerts">
            <?php foreach ($messages as $type => $items): ?>
                <?php foreach ($items as $message): ?>
                    <div class="alert alert-<?= e($type) ?>"><?= e($message) ?></div>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?= $content ?>
</main>

<footer class="site-footer">
    <div class="container footer-inner">
        <span>UF1845 · Accés a dades en aplicacions web de l’entorn servidor</span>
        <span>Projecte pràctic: agència de viatges</span>
    </div>
</footer>
<script src="assets/app.js"></script>
</body>
</html>
