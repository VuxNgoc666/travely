<article class="tour-card reveal">
    <a class="tour-media" href="<?= url('tour/' . $tour['slug']) ?>">
        <?php if (!empty($tour['discount_label'])): ?>
            <span class="badge hot"><?= e($tour['discount_label']) ?></span>
        <?php elseif (!empty($tour['featured'])): ?>
            <span class="badge">Bán chạy</span>
        <?php endif; ?>
        <img src="<?= e(media_url($tour['thumbnail'])) ?>" alt="<?= e($tour['title']) ?>" loading="lazy">
    </a>
    <div class="tour-body">
        <p class="muted"><?= e($tour['destination']) ?> · <?= e($tour['country']) ?></p>
        <h3><a href="<?= url('tour/' . $tour['slug']) ?>"><?= e($tour['title']) ?></a></h3>
        <div class="tour-meta">
            <span><?= (int) $tour['duration_days'] ?>N<?= (int) $tour['duration_nights'] ?>Đ</span>
            <span><?= e($tour['transport']) ?></span>
            <span><?= e($tour['hotel']) ?></span>
        </div>
        <div class="price-row">
            <strong><?= money($tour['price']) ?></strong>
            <?php if ((float) $tour['old_price'] > (float) $tour['price']): ?>
                <del><?= money($tour['old_price']) ?></del>
            <?php endif; ?>
        </div>
        <div class="rating-row">
            <span class="stars">★★★★★</span>
            <span><?= number_format((float) $tour['rating'], 1) ?> (<?= (int) $tour['review_count'] ?>)</span>
        </div>
    </div>
</article>
