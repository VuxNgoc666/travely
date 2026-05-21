<?php
$heroMap = [
    'domestic' => 'https://images.unsplash.com/photo-1528127269322-539801943592?auto=format&fit=crop&w=1800&q=85',
    'foreign' => 'https://images.unsplash.com/photo-1500534314209-a25ddb2bd429?auto=format&fit=crop&w=1800&q=85',
];
$heroImage = $heroMap[$type ?? ''] ?? 'https://images.unsplash.com/photo-1506929562872-bb421503ef21?auto=format&fit=crop&w=1800&q=85';
?>
<section class="sub-hero" style="--hero-image:url('<?= e($heroImage) ?>')">
    <div>
        <p class="eyebrow"><?= $isDeals ? 'Limited offers' : 'Curated trips' ?></p>
        <h1><?= e($heading) ?></h1>
        <p><?= e($subtitle) ?></p>
    </div>
</section>

<form class="search-dock listing-search" action="<?= url($type === 'domestic' ? 'tours/domestic' : ($type === 'foreign' ? 'tours/foreign' : ($isDeals ? 'deals' : 'tours'))) ?>" method="get">
    <label>
        <span>Điểm đến</span>
        <input name="keyword" value="<?= old('keyword') ?>" placeholder="Tên tour, quốc gia...">
    </label>
    <label>
        <span>Chủ đề</span>
        <select name="category">
            <option value="">Tất cả</option>
            <?php foreach ($categories as $row): ?>
                <option <?= selected($row['category'], $filters['category'] ?? '') ?>><?= e($row['category']) ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <label>
        <span>Ngày đi</span>
        <input type="date" name="start_date" value="<?= old('start_date') ?>" min="<?= date('Y-m-d') ?>" data-min-today>
    </label>
    <label>
        <span>Sắp xếp</span>
        <select name="sort">
            <option value="">Phổ biến</option>
            <option value="newest" <?= selected('newest', $filters['sort'] ?? '') ?>>Mới nhất</option>
            <option value="price_asc" <?= selected('price_asc', $filters['sort'] ?? '') ?>>Giá tăng dần</option>
            <option value="price_desc" <?= selected('price_desc', $filters['sort'] ?? '') ?>>Giá giảm dần</option>
        </select>
    </label>
    <button class="btn primary" type="submit">Tìm kiếm</button>
</form>

<section class="catalog">
    <aside class="filter-panel reveal">
        <div class="filter-head">
            <strong>Bộ lọc tìm kiếm</strong>
            <a href="<?= url($type === 'domestic' ? 'tours/domestic' : ($type === 'foreign' ? 'tours/foreign' : 'tours')) ?>">Xóa tất cả</a>
        </div>
        <form action="<?= url($type === 'domestic' ? 'tours/domestic' : ($type === 'foreign' ? 'tours/foreign' : ($isDeals ? 'deals' : 'tours'))) ?>" method="get">
            <div class="filter-group">
                <span>Khu vực</span>
                <?php foreach ($regions as $row): ?>
                    <label><input type="radio" name="region" value="<?= e($row['region']) ?>" <?= checked($row['region'], $filters['region'] ?? '') ?>> <?= e($row['region']) ?></label>
                <?php endforeach; ?>
            </div>
            <div class="filter-group">
                <span>Khoảng giá</span>
                <label><input type="radio" name="price_range" value="under_3" <?= checked('under_3', $filters['price_range'] ?? '') ?>> Dưới 3.000.000đ</label>
                <label><input type="radio" name="price_range" value="3_8" <?= checked('3_8', $filters['price_range'] ?? '') ?>> 3.000.000đ - 8.000.000đ</label>
                <label><input type="radio" name="price_range" value="over_8" <?= checked('over_8', $filters['price_range'] ?? '') ?>> Trên 8.000.000đ</label>
            </div>
            <div class="filter-group">
                <span>Thời gian</span>
                <label><input type="radio" name="duration" value="short" <?= checked('short', $filters['duration'] ?? '') ?>> 1 - 3 ngày</label>
                <label><input type="radio" name="duration" value="medium" <?= checked('medium', $filters['duration'] ?? '') ?>> 4 - 5 ngày</label>
            </div>
            <button class="btn primary full" type="submit">Áp dụng</button>
        </form>
        <div class="mini-deal">
            <span>Private deal</span>
            <strong>500.000đ</strong>
            <p>Giảm khi đặt tour theo nhóm từ 4 khách.</p>
        </div>
    </aside>

    <div class="catalog-main">
        <div class="catalog-toolbar">
            <p><strong><?= count($tours) ?></strong> hành trình phù hợp</p>
        </div>

        <?php if (!$tours): ?>
            <div class="empty-state compact">
                <h2>Chưa có tour trùng bộ lọc.</h2>
                <p>Hãy nới ngân sách hoặc chọn khu vực khác.</p>
            </div>
        <?php else: ?>
            <div class="tour-grid catalog-grid">
                <?php foreach ($tours as $tour): ?>
                    <?php require APP_PATH . '/views/partials/tour_card.php'; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
