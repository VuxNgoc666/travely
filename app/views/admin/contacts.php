<section class="admin-panel">
    <div class="panel-heading">
        <div>
            <p class="eyebrow">Inbox</p>
            <h2>Yêu cầu liên hệ</h2>
        </div>
        <span><?= count($messages) ?> tin nhắn · <?= (int) $newCount ?> mới</span>
    </div>

    <?php if (empty($messages)): ?>
        <div class="empty-state">
            <h3>Chưa có yêu cầu liên hệ</h3>
            <p>Khi khách gửi form ở trang Liên hệ, nội dung sẽ xuất hiện tại đây.</p>
        </div>
    <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>Khách</th>
                    <th>Chủ đề</th>
                    <th>Nội dung</th>
                    <th>Thời gian</th>
                    <th>Trạng thái</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($messages as $message): ?>
                    <tr>
                        <td>
                            <strong><?= e($message['name']) ?></strong>
                            <span class="table-sub"><?= e($message['phone']) ?></span>
                            <span class="table-sub"><?= e($message['email']) ?></span>
                        </td>
                        <td><?= e($message['subject'] ?: 'Chưa chọn chủ đề') ?></td>
                        <td class="contact-message-cell"><?= nl2br(e($message['message'])) ?></td>
                        <td><?= e(date('d/m/Y H:i', strtotime($message['created_at']))) ?></td>
                        <td>
                            <form class="inline-form" method="post" action="<?= url('admin/contacts/status/' . $message['id']) ?>">
                                <?= csrf_field() ?>
                                <select name="status">
                                    <option value="new" <?= selected('new', $message['status']) ?>>Mới</option>
                                    <option value="read" <?= selected('read', $message['status']) ?>>Đã đọc</option>
                                    <option value="resolved" <?= selected('resolved', $message['status']) ?>>Đã xử lý</option>
                                </select>
                                <button class="btn ghost small" type="submit">Lưu</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
