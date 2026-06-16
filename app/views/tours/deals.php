<?php
$featuredDeals = array_slice($dealTours, 0, 4);
$promoCards = [
    ['class' => 'blue', 'label' => 'Ưu đãi đặt sớm', 'title' => 'Giảm đến 20%', 'text' => 'Đặt tour trước 60 ngày nhận ngay ưu đãi hấp dẫn.', 'cta' => 'Xem ngay'],
    ['class' => 'orange', 'label' => 'Đi theo nhóm', 'title' => 'Giá giảm thêm đến 10%', 'text' => 'Càng đông càng tiết kiệm cho nhóm từ 4 người.', 'cta' => 'Xem ngay'],
    ['class' => 'teal', 'label' => 'Ưu đãi sinh nhật', 'title' => 'Giảm 500K', 'text' => 'Dành tặng bạn trong tháng sinh nhật.', 'cta' => 'Xem ngay'],
];
$destDeals = [
    ['name' => 'Châu Á', 'deal' => 'Giảm đến 2.500.000đ', 'img' => 'https://images.unsplash.com/photo-1528164344705-47542687000d?auto=format&fit=crop&w=700&q=85'],
    ['name' => 'Châu Âu', 'deal' => 'Giảm đến 5.000.000đ', 'img' => 'https://images.unsplash.com/photo-1502602898657-3e91760cbb34?auto=format&fit=crop&w=700&q=85'],
];
?>
<section class="deals-hero">
    <div class="deals-hero-content">
        <p class="eyebrow">Ưu đãi hấp dẫn</p>
        <h1>Du lịch thả ga<br><span>Không lo về giá</span></h1>
        <p>Hàng ngàn ưu đãi mỗi ngày dành riêng cho bạn.</p>
    </div>
</section>

<section class="deal-benefits">
    <article><span>🎁</span><strong>Ưu đãi độc quyền</strong><p>Dành riêng cho khách hàng Travely</p></article>
    <article><span>💸</span><strong>Tiết kiệm tối đa</strong><p>Giá tốt hơn khi đặt sớm hoặc theo nhóm</p></article>
    <article><span>✓</span><strong>Đa dạng lựa chọn</strong><p>Nhiều chương trình khuyến mãi hấp dẫn</p></article>
    <article><span>🏆</span><strong>Hỗ trợ tận tâm</strong><p>Đội ngũ tư vấn sẵn sàng 24/7</p></article>
</section>

<section class="deals-section">
    <div class="section-heading">
        <div>
            <p class="eyebrow">Trending</p>
            <h2>Ưu đãi nổi bật</h2>
        </div>
        <a class="text-link" href="<?= url('tours/foreign') ?>">Xem tất cả</a>
    </div>
    <div class="deal-product-grid">
        <?php foreach ($featuredDeals as $tour): ?>
            <article class="deal-product-card reveal">
                <a class="deal-product-media" href="<?= url('tour/' . $tour['slug']) ?>">
                    <span><?= e($tour['discount_label'] ?: 'HOT') ?></span>
                    <img src="<?= e(media_url($tour['thumbnail'])) ?>" alt="<?= e($tour['title']) ?>">
                </a>
                <div class="deal-product-body">
                    <p><?= e($tour['country']) ?></p>
                    <h3><a href="<?= url('tour/' . $tour['slug']) ?>"><?= e($tour['title']) ?></a></h3>
                    <div class="deal-price">
                        <small>Giảm đến</small>
                        <strong><?= money(max(0, (float) $tour['old_price'] - (float) $tour['price'])) ?></strong>
                    </div>
                    <div class="price-row">
                        <strong><?= money($tour['price']) ?></strong>
                        <del><?= money($tour['old_price']) ?></del>
                    </div>
                    <div class="rating-row"><span class="stars">★★★★★</span><span><?= number_format((float) $tour['rating'], 1) ?> (<?= (int) $tour['review_count'] ?>)</span></div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="deals-section promo-grid" id="specials">
    <?php foreach ($promoCards as $promo): ?>
        <article class="promo-card <?= e($promo['class']) ?> reveal">
            <span><?= e($promo['label']) ?></span>
            <h3><?= e($promo['title']) ?></h3>
            <p><?= e($promo['text']) ?></p>
            <a class="btn white small" href="<?= url('tours') ?>"><?= e($promo['cta']) ?></a>
        </article>
    <?php endforeach; ?>
</section>

<section class="deals-section" id="destinations">
    <div class="section-heading">
        <div>
            <p class="eyebrow">Destination deals</p>
            <h2>Ưu đãi theo điểm đến</h2>
        </div>
        <a class="text-link" href="<?= url('tours/foreign') ?>">Xem tất cả</a>
    </div>
    <div class="destination-deal-grid">
        <?php foreach ($destDeals as $deal): ?>
            <a class="destination-deal-card reveal" href="<?= url('tours/foreign') ?>">
                <img src="<?= e($deal['img']) ?>" alt="<?= e($deal['name']) ?>">
                <div>
                    <h3><?= e($deal['name']) ?></h3>
                    <p><?= e($deal['deal']) ?></p>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<section class="deals-section special-programs" id="programs">
    <div class="section-heading">
        <div>
            <p class="eyebrow">Programs</p>
            <h2>Chương trình ưu đãi đặc biệt</h2>
        </div>
    </div>
    <div class="program-grid">
        <article><span>⚡</span><strong>Flash Sale</strong><p>Giá sốc trong 24h</p></article>
        <article><span>📅</span><strong>Ưu đãi cuối tuần</strong><p>Giảm thêm đến 1 triệu</p></article>
        <article><span>🧳</span><strong>Combo tiết kiệm</strong><p>Vé máy bay + khách sạn</p></article>
        <article><span>💳</span><strong>Ưu đãi thẻ ngân hàng</strong><p>Giảm thêm đến 10%</p></article>
        <article><span>🎟</span><strong>Quà tặng voucher</strong><p>Nhiều quà tặng hấp dẫn</p></article>
    </div>
</section>
