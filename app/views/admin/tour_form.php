<?php
function lines_value($value) {
    return e(implode("\n", json_list($value)));
}

function sorted_date_lines_value($value) {
    $dates = array_values(array_filter(json_list($value), function ($date) {
        return is_string($date) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $date);
    }));
    sort($dates, SORT_STRING);
    return e(implode("\n", $dates));
}

function image_preview_class($value) {
    return trim((string) $value) === '' ? ' image-url-preview-empty' : '';
}
?>
<form class="admin-panel admin-form" method="post" action="<?= url('admin/tours/save') ?>">
    <?= csrf_field() ?>
    <input type="hidden" name="id" value="<?= e($tour['id']) ?>">
    <div class="panel-heading">
        <h2><?= $tour['id'] ? 'Sửa tour' : 'Thêm tour mới' ?></h2>
        <button class="btn primary" type="submit">Lưu tour</button>
    </div>
    <div class="form-grid">
        <label>Tên tour
            <input name="title" value="<?= e($tour['title']) ?>" required>
        </label>
        <label>Slug
            <input name="slug" value="<?= e($tour['slug']) ?>" placeholder="Tự tạo nếu bỏ trống">
        </label>
        <label>Loại tour
            <select name="type">
                <option value="domestic" <?= selected('domestic', $tour['type']) ?>>Trong nước</option>
                <option value="foreign" <?= selected('foreign', $tour['type']) ?>>Nước ngoài</option>
            </select>
        </label>
        <label>Khu vực
            <input name="region" value="<?= e($tour['region']) ?>">
        </label>
        <label>Điểm đến
            <input name="destination" value="<?= e($tour['destination']) ?>">
        </label>
        <label>Quốc gia
            <input name="country" value="<?= e($tour['country']) ?>">
        </label>
        <label>Chủ đề
            <input name="category" value="<?= e($tour['category']) ?>">
        </label>
        <label>Nhãn giảm giá
            <input name="discount_label" value="<?= e($tour['discount_label']) ?>">
        </label>
        <label>Giá bán
            <input type="number" name="price" value="<?= e($tour['price']) ?>" min="0">
        </label>
        <label>Giá cũ
            <input type="number" name="old_price" value="<?= e($tour['old_price']) ?>" min="0">
        </label>
        <label>Số ngày
            <input type="number" name="duration_days" value="<?= e($tour['duration_days']) ?>" min="1">
        </label>
        <label>Số đêm
            <input type="number" name="duration_nights" value="<?= e($tour['duration_nights']) ?>" min="0">
        </label>
        <label>Phương tiện
            <input name="transport" value="<?= e($tour['transport']) ?>">
        </label>
        <label>Khách sạn
            <input name="hotel" value="<?= e($tour['hotel']) ?>">
        </label>
        <label>Đánh giá
            <input type="number" step="0.1" name="rating" value="<?= e($tour['rating']) ?>" min="0" max="5">
        </label>
        <label>Lượt đánh giá
            <input type="number" name="review_count" value="<?= e($tour['review_count']) ?>" min="0">
        </label>
        <label>Trạng thái
            <select name="status">
                <option value="active" <?= selected('active', $tour['status']) ?>>active</option>
                <option value="draft" <?= selected('draft', $tour['status']) ?>>draft</option>
            </select>
        </label>
        <label class="check-admin">
            <input type="checkbox" name="featured" value="1" <?= !empty($tour['featured']) ? 'checked' : '' ?>>
            Nổi bật trên trang chủ
        </label>
        <div class="wide image-url-field">
            <label>URL ảnh thumbnail
                <input name="thumbnail" value="<?= e($tour['thumbnail']) ?>" placeholder="Dán link ảnh online, ví dụ: https://images.unsplash.com/..." inputmode="url" data-image-preview-input="thumbnail">
                <span class="field-hint">Dùng URL ảnh online, không cần tải file về máy.</span>
            </label>
            <div class="image-url-preview<?= image_preview_class($tour['thumbnail']) ?>" data-image-preview="thumbnail">
                <img <?= trim((string) $tour['thumbnail']) !== '' ? 'src="' . e($tour['thumbnail']) . '"' : '' ?> alt="Preview thumbnail">
                <span>Preview thumbnail</span>
            </div>
        </div>
        <div class="wide image-url-field">
            <label>URL ảnh hero
                <input name="hero_image" value="<?= e($tour['hero_image']) ?>" placeholder="Dán link ảnh online dùng cho ảnh lớn của trang chi tiết" inputmode="url" data-image-preview-input="hero">
                <span class="field-hint">Nên dùng ảnh ngang, chất lượng cao để phần đầu trang đẹp hơn.</span>
            </label>
            <div class="image-url-preview<?= image_preview_class($tour['hero_image']) ?>" data-image-preview="hero">
                <img <?= trim((string) $tour['hero_image']) !== '' ? 'src="' . e($tour['hero_image']) . '"' : '' ?> alt="Preview hero">
                <span>Preview hero</span>
            </div>
        </div>
        <label class="wide">Mô tả
            <textarea name="description" rows="4"><?= e($tour['description']) ?></textarea>
        </label>
        <label class="wide">Gallery, mỗi dòng một URL ảnh online
            <textarea name="gallery" rows="5" placeholder="https://images.unsplash.com/...&#10;https://images.unsplash.com/..."><?= lines_value($tour['gallery']) ?></textarea>
            <span class="field-hint">Mỗi dòng là một link ảnh online, không cần upload file.</span>
        </label>
        <label class="wide">Điểm nổi bật, mỗi dòng một ý
            <textarea name="highlights" rows="5"><?= lines_value($tour['highlights']) ?></textarea>
        </label>
        <label class="wide">Lịch trình, mỗi dòng một ngày
            <textarea name="itinerary" rows="5"><?= lines_value($tour['itinerary']) ?></textarea>
        </label>
        <label class="wide">Đã bao gồm, mỗi dòng một dịch vụ
            <textarea name="included" rows="5"><?= lines_value($tour['included']) ?></textarea>
        </label>
        <label class="wide">Ngày khởi hành, mỗi dòng định dạng YYYY-MM-DD
            <textarea name="start_dates" rows="4"><?= sorted_date_lines_value($tour['start_dates']) ?></textarea>
        </label>
    </div>
</form>
