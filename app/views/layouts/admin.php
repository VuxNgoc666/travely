<?php
$currentUser = Auth::user();
$requestPath = trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/');
$basePath = trim(parse_url(BASE_URL, PHP_URL_PATH) ?: BASE_URL, '/');
$adminPath = trim(preg_replace('#^' . preg_quote($basePath, '#') . '#', '', $requestPath), '/');
$adminActive = function ($path) use ($adminPath) {
    $path = trim($path, '/');
    return $adminPath === $path || ($path !== 'admin' && str_starts_with($adminPath, $path));
};
$navItems = [
    ['path' => 'admin', 'label' => 'Tổng quan', 'code' => '01', 'hint' => 'Radar'],
    ['path' => 'admin/tours', 'label' => 'Tour', 'code' => '02', 'hint' => 'Catalog'],
    ['path' => 'admin/bookings', 'label' => 'Booking', 'code' => '03', 'hint' => 'Orders'],
    ['path' => 'admin/contacts', 'label' => 'Liên hệ', 'code' => '04', 'hint' => 'Inbox'],
    ['path' => 'admin/users', 'label' => 'Người dùng', 'code' => '05', 'hint' => 'Access'],
];
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'Admin') ?> - Travely</title>
    <link rel="stylesheet" href="<?= versioned_asset('css/style.css') ?>">
</head>
<body class="admin-body admin-neo-body">
<div class="admin-neo-bg" aria-hidden="true">
    <span class="admin-neo-orb orb-a"></span>
    <span class="admin-neo-orb orb-b"></span>
    <span class="admin-neo-grid"></span>
</div>

<div class="admin-neo-shell">
    <aside class="admin-neo-rail">
        <a class="admin-neo-brand" href="<?= url('admin') ?>">
            <span>TRAVELY</span>
            <small>Control tower</small>
        </a>

        <nav class="admin-neo-nav" aria-label="Admin">
            <?php foreach ($navItems as $item): ?>
                <a class="<?= $adminActive($item['path']) ? 'active' : '' ?>" href="<?= url($item['path']) ?>">
                    <span class="admin-neo-code"><?= e($item['code']) ?></span>
                    <strong><?= e($item['label']) ?></strong>
                    <em><?= e($item['hint']) ?></em>
                </a>
            <?php endforeach; ?>
        </nav>

        <a class="admin-neo-site" href="<?= url('') ?>">
            <span>WEB</span>
            Xem website
        </a>
    </aside>

    <main class="admin-neo-main">
        <header class="admin-neo-topbar">
            <div>
                <p class="admin-neo-kicker">Travely command system</p>
                <h1><?= e($title ?? 'Admin') ?></h1>
            </div>
            <div class="admin-neo-user">
                <span><?= date('d/m/Y') ?></span>
                <strong><?= e($currentUser['name'] ?? 'Admin') ?></strong>
                <a class="btn ghost small" href="<?= url('logout') ?>">Đăng xuất</a>
            </div>
        </header>

        <nav class="admin-neo-mobile-nav" aria-label="Admin mobile">
            <?php foreach ($navItems as $item): ?>
                <a class="<?= $adminActive($item['path']) ? 'active' : '' ?>" href="<?= url($item['path']) ?>">
                    <?= e($item['label']) ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <?php if ($message = flash('success')): ?>
            <div class="alert success"><?= e($message) ?></div>
        <?php endif; ?>
        <?php if ($message = flash('error')): ?>
            <div class="alert error"><?= e($message) ?></div>
        <?php endif; ?>

        <?= $content ?>
    </main>
</div>
<script src="<?= versioned_asset('js/app.js') ?>"></script>
</body>
</html>
