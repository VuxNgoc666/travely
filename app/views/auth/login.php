<?php $loginHero = versioned_asset('images/ha-long-hero-new.png'); ?>
<section class="auth-screen auth-login" style="--auth-bg:url('<?= e($loginHero) ?>')">
    <div class="auth-login-copy reveal">
        <p class="eyebrow">Travely account</p>
        <h1>Vào khoang hành trình của bạn.</h1>
        <p>Theo dõi booking, lưu tour yêu thích và mở bảng điều khiển admin trong cùng một không gian du lịch cinematic.</p>
        <div class="auth-login-stats" aria-label="Travely highlights">
            <span><strong>24/7</strong>Hỗ trợ</span>
            <span><strong>120+</strong>Lịch trình</span>
            <span><strong>2 phút</strong>Đặt tour</span>
        </div>
    </div>
    <form class="auth-card auth-login-card reveal" method="post" action="<?= url('login') ?>">
        <?= csrf_field() ?>
        <p class="eyebrow">Welcome back</p>
        <h2>Đăng nhập</h2>
        <p class="auth-lead">Nhập tài khoản để tiếp tục đặt tour hoặc quản lý hệ thống.</p>
        <label>Tài khoản
            <input type="text" name="email" placeholder="admin" autocomplete="username" required>
        </label>
        <label>Mật khẩu
            <input type="password" name="password" placeholder="123456" autocomplete="current-password" required>
        </label>
        <button class="btn primary full auth-submit" type="submit">Vào hệ thống</button>
        <p class="auth-switch">Chưa có tài khoản? <a href="<?= url('register') ?>">Đăng ký ngay</a></p>
    </form>
</section>
