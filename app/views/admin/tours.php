<section class="admin-panel">
    <div class="panel-heading">
        <h2>Danh sách tour</h2>
        <a class="btn primary small" href="<?= url('admin/tours/create') ?>">Thêm tour</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
            <tr>
                <th>Tour</th>
                <th>Loại</th>
                <th>Giá</th>
                <th>Đánh giá</th>
                <th>Trạng thái</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($tours as $tour): ?>
                <tr>
                    <td>
                        <div class="table-tour">
                            <img src="<?= e(media_url($tour['thumbnail'])) ?>" alt="<?= e($tour['title']) ?>">
                            <div><strong><?= e($tour['title']) ?></strong><span><?= e($tour['destination']) ?></span></div>
                        </div>
                    </td>
                    <td><?= $tour['type'] === 'domestic' ? 'Trong nước' : 'Nước ngoài' ?></td>
                    <td><?= money($tour['price']) ?></td>
                    <td><?= number_format((float) $tour['rating'], 1) ?></td>
                    <td><span class="status <?= e($tour['status']) ?>"><?= e($tour['status']) ?></span></td>
                    <td class="table-actions">
                        <div class="table-action-row">
                            <a class="btn ghost small" href="<?= url('admin/tours/edit/' . $tour['id']) ?>">Sửa</a>
                            <form method="post" action="<?= url('admin/tours/delete/' . $tour['id']) ?>" onsubmit="return confirm('Xóa tour này?')">
                                <?= csrf_field() ?>
                                <button class="btn danger small" type="submit">Xóa</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
