<?php
$gallery = json_list($tour['gallery']);
$highlights = json_list($tour['highlights']);
$itinerary = json_list($tour['itinerary']);
$included = json_list($tour['included']);
$today = date('Y-m-d');
$startDates = array_values(array_filter(json_list($tour['start_dates']), function ($date) use ($today) {
    return is_string($date) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) && $date >= $today;
}));
sort($startDates, SORT_STRING);
if (!$gallery) {
    $gallery = [$tour['thumbnail'], $tour['hero_image']];
}
?>
<section class="detail-hero">
    <div class="detail-gallery reveal">
        <img class="gallery-main" src="<?= e(media_url($tour['hero_image'] ?: $tour['thumbnail'])) ?>" alt="<?= e($tour['title']) ?>">
        <div class="gallery-thumbs">
            <?php foreach (array_slice($gallery, 0, 5) as $image): ?>
                <img src="<?= e(media_url($image)) ?>" alt="<?= e($tour['title']) ?>">
            <?php endforeach; ?>
        </div>
    </div>
    <div class="detail-intro reveal">
        <div class="crumbs"><a href="<?= url('') ?>">Trang chủ</a> / <a href="<?= url($tour['type'] === 'domestic' ? 'tours/domestic' : 'tours/foreign') ?>"><?= $tour['type'] === 'domestic' ? 'Tour trong nước' : 'Tour nước ngoài' ?></a></div>
        <div class="label-row">
            <span class="badge">Bán chạy</span>
            <span class="badge soft"><?= e($tour['country']) ?></span>
        </div>
        <h1><?= e($tour['title']) ?></h1>
        <div class="rating-row big"><span class="stars">★★★★★</span><span><?= number_format((float) $tour['rating'], 1) ?> · <?= (int) $tour['review_count'] ?> đánh giá</span></div>
        <p><?= e($tour['description']) ?></p>
        <div class="detail-facts">
            <span><?= (int) $tour['duration_days'] ?> ngày <?= (int) $tour['duration_nights'] ?> đêm</span>
            <span><?= e($tour['transport']) ?></span>
            <span><?= e($tour['hotel']) ?></span>
            <span><?= e($tour['category']) ?></span>
        </div>
        <form method="post" action="<?= url('favorite/toggle') ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="tour_id" value="<?= (int) $tour['id'] ?>">
            <button class="btn ghost" type="submit"><?= $isFavorite ? 'Đã yêu thích' : 'Thêm vào yêu thích' ?></button>
        </form>
    </div>
    <aside class="booking-card reveal">
        <p>Giá chỉ từ</p>
        <strong><?= money($tour['price']) ?></strong>
        <?php if ((float) $tour['old_price'] > (float) $tour['price']): ?>
            <del><?= money($tour['old_price']) ?></del>
        <?php endif; ?>

        <?php if (Auth::check()): ?>
            <?php $user = Auth::user(); ?>
            <form method="post" action="<?= url('booking/store') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="tour_id" value="<?= (int) $tour['id'] ?>">
                <label>Ngày khởi hành
                    <select name="start_date" required>
                        <?php foreach ($startDates ?: [date('Y-m-d', strtotime('+14 days'))] as $date): ?>
                            <option value="<?= e($date) ?>"><?= e(date('d/m/Y', strtotime($date))) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label>Số khách
                    <input type="number" min="1" max="20" name="guests" value="2" required>
                </label>
                <label>Họ tên
                    <input name="full_name" value="<?= e($user['name']) ?>" required>
                </label>
                <label>Số điện thoại
                    <input name="phone" value="<?= e($user['phone']) ?>" required>
                </label>
                <label>Email
                    <input type="email" name="email" value="<?= e($user['email']) ?>" required>
                </label>
                <label>Ghi chú
                    <textarea name="notes" rows="3" placeholder="Yêu cầu ăn uống, phòng, lịch bay..."></textarea>
                </label>
                <button class="btn primary full" type="submit">Đặt tour ngay</button>
            </form>
        <?php else: ?>
            <div class="login-required">
                <p>Đăng nhập để đặt tour và lưu lịch sử booking.</p>
                <a class="btn primary full" href="<?= url('login') ?>">Đăng nhập</a>
            </div>
        <?php endif; ?>
        <div class="secure-note">Thanh toán an toàn, admin xác nhận trước khi chốt lịch.</div>
    </aside>
</section>

<section class="detail-layout">
    <div class="detail-content">
        <div class="tabs" data-tabs>
            <button class="active" data-tab="overview" type="button">Tổng quan</button>
            <button data-tab="itinerary" type="button">Lịch trình</button>
            <button data-tab="included" type="button">Dịch vụ</button>
        </div>
        <article class="tab-panel active" data-panel="overview">
            <h2>Điểm nổi bật</h2>
            <div class="highlight-grid">
                <?php foreach ($highlights as $item): ?>
                    <div class="highlight-item"><?= e($item) ?></div>
                <?php endforeach; ?>
            </div>
        </article>
        <article class="tab-panel" data-panel="itinerary">
            <h2>Lịch trình chi tiết</h2>
            <div class="timeline">
                <?php foreach ($itinerary as $index => $item): ?>
                    <div class="timeline-item">
                        <span>Ngày <?= $index + 1 ?></span>
                        <p><?= e($item) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </article>
        <article class="tab-panel" data-panel="included">
            <h2>Đã bao gồm</h2>
            <ul class="check-list">
                <?php foreach ($included as $item): ?>
                    <li><?= e($item) ?></li>
                <?php endforeach; ?>
            </ul>
        </article>
    </div>
    <aside class="support-card">
        <h3>Cần tư vấn nhanh?</h3>
        <p>Hotline hoạt động 24/7 cho demo đặt tour.</p>
        <a class="btn ghost full" href="tel:19001234">1900 1234</a>
    </aside>
</section>

<section class="section">
    <div class="section-heading">
        <div>
            <p class="eyebrow">Next scene</p>
            <h2>Có thể bạn cũng thích</h2>
        </div>
    </div>
    <div class="tour-grid">
        <?php foreach ($relatedTours as $tour): ?>
            <?php require APP_PATH . '/views/partials/tour_card.php'; ?>
        <?php endforeach; ?>
    </div>
</section>
