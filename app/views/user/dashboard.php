<section class="account-hero">
    <div>
        <p class="eyebrow">Passenger lounge</p>
        <h1>Xin chào, <?= e($user['name']) ?>.</h1>
        <p>Theo dõi booking, giữ lại tour yêu thích và tiếp tục đặt hành trình mới.</p>
    </div>
    <a class="btn primary" href="<?= url('tours') ?>">Đặt tour mới</a>
</section>

<section class="account-grid">
    <div class="account-main">
        <div class="panel-heading">
            <h2>Booking của bạn</h2>
            <span><?= count($bookings) ?> booking</span>
        </div>
        <?php if (!$bookings): ?>
            <div class="empty-state compact">
                <h3>Bạn chưa có booking.</h3>
                <p>Chọn một tour và đặt thử để booking xuất hiện ở đây.</p>
            </div>
        <?php else: ?>
            <div class="booking-list">
                <?php foreach ($bookings as $booking): ?>
                    <article class="booking-item reveal">
                        <img src="<?= e(media_url($booking['thumbnail'])) ?>" alt="<?= e($booking['tour_title']) ?>">
                        <div>
                            <span class="status <?= e($booking['status']) ?>"><?= e(status_label($booking['status'])) ?></span>
                            <h3><a href="<?= url('tour/' . $booking['slug']) ?>"><?= e($booking['tour_title']) ?></a></h3>
                            <p><?= e($booking['destination']) ?> · <?= (int) $booking['guests'] ?> khách · <?= e(date('d/m/Y', strtotime($booking['start_date']))) ?></p>
                        </div>
                        <strong><?= money($booking['total_price']) ?></strong>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <aside class="account-side">
        <div class="profile-card">
            <span class="avatar-xl"><?= e(strtoupper(substr($user['name'], 0, 1))) ?></span>
            <h2><?= e($user['name']) ?></h2>
            <p><?= e($user['email']) ?></p>
            <p><?= e($user['phone']) ?></p>
        </div>
        <div class="panel-heading">
            <h2>Tour yêu thích</h2>
            <span><?= count($favorites) ?></span>
        </div>
        <div class="favorite-stack">
            <?php foreach ($favorites as $tour): ?>
                <a class="favorite-mini" href="<?= url('tour/' . $tour['slug']) ?>">
                    <img src="<?= e(media_url($tour['thumbnail'])) ?>" alt="<?= e($tour['title']) ?>">
                    <span><?= e($tour['title']) ?></span>
                </a>
            <?php endforeach; ?>
            <?php if (!$favorites): ?>
                <p class="muted">Chưa có tour yêu thích.</p>
            <?php endif; ?>
        </div>
    </aside>
</section>
