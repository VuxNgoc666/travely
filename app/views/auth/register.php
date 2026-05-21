<?php $registerHero = versioned_asset('images/ha-long-hero-new.png'); ?>
<section class="auth-screen auth-login auth-register" style="--auth-bg:url('<?= e($registerHero) ?>')">
    <div class="auth-login-copy auth-register-copy reveal">
        <p class="eyebrow">New passenger</p>
        <h1>Tạo tài khoản cho hành trình mới.</h1>
        <p>Lưu tour yêu thích, đặt lịch khởi hành và theo dõi trạng thái booking trong một không gian Travely đồng bộ.</p>
        <div class="auth-login-stats" aria-label="Lợi ích tài khoản Travely">
            <span><strong>Miễn phí</strong>Tạo tài khoản</span>
            <span><strong>1 chạm</strong>Lưu tour</span>
            <span><strong>Realtime</strong>Theo dõi booking</span>
        </div>
    </div>
    <form class="auth-card auth-login-card auth-register-card reveal" method="post" action="<?= url('register') ?>">
        <?= csrf_field() ?>
        <p class="eyebrow">Join Travely</p>
        <h2>Đăng ký</h2>
        <p class="auth-lead">Tạo tài khoản người dùng để đặt tour và lưu lại các hành trình bạn quan tâm.</p>
        <label>Họ tên
            <input name="name" value="<?= old('name') ?>" placeholder="Nguyễn Văn A" autocomplete="name" required>
        </label>
        <label>Email
            <input type="email" name="email" value="<?= old('email') ?>" placeholder="ban@example.com" autocomplete="email" required>
        </label>
        <label>Số điện thoại
            <input name="phone" value="<?= old('phone') ?>" placeholder="0909 123 456" autocomplete="tel" required>
        </label>
        <label>Mật khẩu
            <input type="password" name="password" minlength="6" placeholder="Tối thiểu 6 ký tự" autocomplete="new-password" required>
        </label>
        <button class="btn primary full auth-submit" type="submit">Tạo tài khoản</button>
        <div class="auth-demo-box auth-benefit-box">
            <span>Không cần thanh toán khi tạo tài khoản.</span>
            <span>Booking chỉ được ghi nhận sau khi bạn bấm đặt tour.</span>
        </div>
        <p class="auth-switch">Đã có tài khoản? <a href="<?= url('login') ?>">Đăng nhập</a></p>
    </form>
</section>
