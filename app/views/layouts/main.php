<?php $currentUser = Auth::user(); ?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? APP_NAME) ?></title>
    <link rel="stylesheet" href="<?= versioned_asset('css/style.css') ?>">
</head>
<body>
<div class="ambient"></div>
<header class="site-header <?= trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/') === trim(BASE_URL, '/') ? 'transparent-header' : '' ?>" data-header>
    <a class="brand" href="<?= url('') ?>">
        <span>TRAVELY</span>
        <small><?= APP_TAGLINE ?></small>
    </a>
    <button class="nav-toggle" type="button" aria-label="Mở menu" data-nav-toggle><span></span><span></span></button>
    <nav class="main-nav" data-nav>
        <a class="<?= active_nav('/') ?>" href="<?= url('') ?>">Trang chủ</a>
        <a class="<?= active_nav('tours/domestic') ?>" href="<?= url('tours/domestic') ?>">Tour trong nước</a>
        <a class="<?= active_nav('tours/foreign') ?>" href="<?= url('tours/foreign') ?>">Tour nước ngoài</a>
        <a class="<?= active_nav('deals') ?>" href="<?= url('deals') ?>">Ưu đãi</a>
        <a class="<?= active_nav('contact') ?>" href="<?= url('contact') ?>">Liên hệ</a>
    </nav>
    <div class="header-actions">
        <a class="hotline" href="tel:19001234">1900 1234</a>
        <?php if ($currentUser): ?>
            <?php if ($currentUser['role'] === 'admin'): ?>
                <a class="btn ghost small" href="<?= url('admin') ?>">Admin</a>
            <?php endif; ?>
            <a class="avatar-link" href="<?= url('account') ?>"><?= e(strtoupper(substr($currentUser['name'], 0, 1))) ?></a>
            <a class="btn primary small" href="<?= url('logout') ?>">Đăng xuất</a>
        <?php else: ?>
            <a class="btn primary small" href="<?= url('login') ?>">Đăng nhập</a>
        <?php endif; ?>
    </div>
</header>

<?php if ($message = flash('success')): ?>
    <div class="toast success"><?= e($message) ?></div>
<?php endif; ?>
<?php if ($message = flash('error')): ?>
    <div class="toast error"><?= e($message) ?></div>
<?php endif; ?>

<main>
    <?= $content ?>
</main>

<footer class="site-footer">
    <section class="newsletter">
        <div>
            <p class="eyebrow">Boarding pass</p>
            <h2>Nhận deal du lịch mới trước khi nó hạ cánh.</h2>
        </div>
        <div class="newsletter-form newsletter-action">
            <p>Để lại thông tin ở trang liên hệ, Travely sẽ tư vấn deal phù hợp.</p>
            <a class="btn primary" href="<?= url('contact') ?>">Liên hệ nhận deal</a>
        </div>
        <div class="luggage-scene" aria-hidden="true">
            <span class="case"></span><span class="hat"></span><span class="sun"></span>
        </div>
    </section>
    <div class="footer-grid">
        <div>
            <a class="brand footer-brand" href="<?= url('') ?>">
                <span>TRAVELY</span>
                <small><?= APP_TAGLINE ?></small>
            </a>
            <p>Đồng hành cùng bạn trên mọi hành trình khám phá thế giới.</p>
        </div>
        <div>
            <h3>Khám phá</h3>
            <a href="<?= url('tours/domestic') ?>">Tour trong nước</a>
            <a href="<?= url('tours/foreign') ?>">Tour nước ngoài</a>
            <a href="<?= url('deals') ?>">Ưu đãi</a>
        </div>
        <div>
            <h3>Hỗ trợ</h3>
            <a href="<?= url('account') ?>">Tài khoản</a>
            <a href="<?= url('login') ?>">Đăng nhập</a>
            <a href="tel:19001234">1900 1234</a>
        </div>
        <div>
            <h3>Văn phòng</h3>
            <p>123 Nguyễn Huệ, Quận 1, TP. Hồ Chí Minh</p>
            <p>support@travely.vn</p>
        </div>
    </div>
    <div class="footer-bottom">© 2026 Travely. Crafted for XAMPP MVC demo.</div>
</footer>

<?php require APP_PATH . '/views/partials/ai_assistant.php'; ?>

<script src="<?= versioned_asset('js/app.js') ?>"></script>
</body>
</html>
