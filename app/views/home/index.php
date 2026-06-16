<?php
$heroImage = versioned_asset('images/ha-long-hero-new.png');
?>
<section class="hero home-hero cinematic" style="--hero-image:url('<?= e($heroImage) ?>')">
    <div class="hero-overlay"></div>
    <img class="script-title" src="<?= versioned_asset('images/travel-the-world-neon-cropped.png') ?>" alt="Travel The World">
    <div class="hero-content reveal">
        <h1>Khám phá thế giới theo cách của bạn</h1>
        <p>Đặt tour dễ dàng - giá tốt mỗi ngày - trải nghiệm đáng nhớ</p>
        <div class="hero-actions">
            <a class="btn primary" href="<?= url('tours') ?>">Khám phá ngay</a>
        </div>
    </div>
    <div class="hero-wave" aria-hidden="true">
        <svg class="wave-svg wave-back" viewBox="0 0 2880 220" preserveAspectRatio="none">
            <path d="M0 104C120 66 240 76 360 108C480 140 600 128 720 96C840 64 960 66 1080 100C1200 134 1320 140 1440 104C1560 66 1680 76 1800 108C1920 140 2040 128 2160 96C2280 64 2400 66 2520 100C2640 134 2760 140 2880 104V220H0V104Z"></path>
        </svg>
        <svg class="wave-svg wave-mid" viewBox="0 0 2880 220" preserveAspectRatio="none">
            <path d="M0 116C150 78 270 92 420 128C570 164 700 132 840 104C990 74 1120 82 1260 118C1350 142 1404 136 1440 116C1590 78 1710 92 1860 128C2010 164 2140 132 2280 104C2430 74 2560 82 2700 118C2790 142 2844 136 2880 116V220H0V116Z"></path>
        </svg>
        <svg class="wave-svg wave-front" viewBox="0 0 2880 220" preserveAspectRatio="none">
            <path d="M0 136C150 94 282 110 432 142C588 176 708 154 864 124C1014 94 1146 102 1296 136C1368 152 1416 150 1440 136C1590 94 1722 110 1872 142C2028 176 2148 154 2304 124C2454 94 2586 102 2736 136C2808 152 2856 150 2880 136V220H0V136Z"></path>
        </svg>
    </div>
    <form class="search-dock home-search reveal" action="<?= url('tours') ?>" method="get">
        <div class="search-tabs">
            <button class="active" type="button">Tìm tour</button>
        </div>
        <div class="search-fields">
            <label>
                <span>Điểm đến</span>
                <input name="keyword" placeholder="Bạn muốn đi đâu?">
            </label>
            <label>
                <span>Ngày đi</span>
                <input type="date" name="start_date" value="" min="<?= date('Y-m-d') ?>" data-min-today>
            </label>
            <label>
                <span>Phong cách</span>
                <select name="category">
                    <option value="">Chọn phong cách</option>
                    <option>Biển đảo</option>
                    <option>Văn hóa - Lịch sử</option>
                    <option>Phiêu lưu</option>
                    <option>Nghỉ dưỡng</option>
                </select>
            </label>
            <label>
                <span>Ngân sách</span>
                <select name="price_range">
                    <option value="">Chọn ngân sách</option>
                    <option value="under_3">Dưới 3 triệu</option>
                    <option value="3_8">3 - 8 triệu</option>
                    <option value="over_8">Trên 8 triệu</option>
                </select>
            </label>
            <button class="btn primary" type="submit">Tìm kiếm</button>
        </div>
    </form>
</section>

<section class="feature-strip home-features">
    <div class="feature-item reveal"><span class="feature-icon">🎁</span><strong>Giá tốt mỗi ngày</strong><p>Cam kết giá tốt nhất thị trường</p></div>
    <div class="feature-item reveal"><span class="feature-icon">💸</span><strong>Đặt tour dễ dàng</strong><p>Quy trình đơn giản, xác nhận tức thì</p></div>
    <div class="feature-item reveal"><span class="feature-icon">✓</span><strong>Hỗ trợ 24/7</strong><p>Đội ngũ tư vấn chuyên nghiệp</p></div>
    <div class="feature-item reveal"><span class="feature-icon">🏆</span><strong>Trải nghiệm đáng nhớ</strong><p>Lịch trình hấp dẫn, hướng dẫn tận tâm</p></div>
</section>

<section class="section">
    <div class="section-heading">
        <div>
            <p class="eyebrow">Editor picks</p>
            <h2>Tour nổi bật</h2>
        </div>
        <a class="text-link" href="<?= url('tours') ?>">Xem tất cả</a>
    </div>
    <div class="tour-grid">
        <?php foreach ($featuredTours as $tour): ?>
            <?php require APP_PATH . '/views/partials/tour_card.php'; ?>
        <?php endforeach; ?>
    </div>
</section>

<section class="cinema-banner reveal">
    <img src="https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1800&q=85" alt="Landscape">
    <div>
        <p class="eyebrow">Ưu đãi độc quyền</p>
        <h2>Giảm 30%</h2>
        <p>Nhóm từ 4 khách được áp dụng ưu đãi tự động trong danh sách deal.</p>
        <a class="btn white" href="<?= url('deals') ?>">Xem deal</a>
    </div>
</section>

<section class="section">
    <div class="section-heading">
        <div>
            <p class="eyebrow">Vietnam frames</p>
            <h2>Trong nước đáng đi ngay</h2>
        </div>
        <a class="text-link" href="<?= url('tours/domestic') ?>">Xem tất cả</a>
    </div>
    <div class="story-grid">
        <?php foreach ($domesticTours as $tour): ?>
            <a class="story-card reveal" href="<?= url('tour/' . $tour['slug']) ?>">
                <img src="<?= e(media_url($tour['thumbnail'])) ?>" alt="<?= e($tour['title']) ?>">
                <span><?= e($tour['destination']) ?></span>
                <strong>Từ <?= money($tour['price']) ?></strong>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<section class="section split-showcase">
    <div>
        <p class="eyebrow">Global moodboard</p>
        <h2>Tour nước ngoài</h2>
        <p>Từ Seoul mùa hoa, Zurich hồ xanh, đến Bangkok nắng vàng, mỗi tour được đặt trong một không gian hình ảnh riêng để người dùng lướt là muốn đi.</p>
        <a class="btn primary" href="<?= url('tours/foreign') ?>">Mở bản đồ quốc tế</a>
    </div>
    <div class="mosaic">
        <?php foreach (array_slice($foreignTours, 0, 5) as $index => $tour): ?>
            <img class="tile-<?= $index + 1 ?>" src="<?= e(media_url($tour['thumbnail'])) ?>" alt="<?= e($tour['destination']) ?>">
        <?php endforeach; ?>
    </div>
</section>

<section class="section">
    <div class="section-heading">
        <div>
            <p class="eyebrow">Flash deals</p>
            <h2>Ưu đãi đang nóng</h2>
        </div>
        <a class="text-link" href="<?= url('deals') ?>">Xem tất cả</a>
    </div>
    <div class="deal-row">
        <?php foreach ($dealTours as $tour): ?>
            <a class="deal-card reveal" href="<?= url('tour/' . $tour['slug']) ?>">
                <img src="<?= e(media_url($tour['thumbnail'])) ?>" alt="<?= e($tour['title']) ?>">
                <div>
                    <span><?= e($tour['discount_label'] ?: 'Ưu đãi') ?></span>
                    <strong><?= e($tour['title']) ?></strong>
                    <p><?= money($tour['price']) ?> <del><?= money($tour['old_price']) ?></del></p>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</section>
