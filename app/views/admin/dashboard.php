<section class="neo-hero-panel">
    <div class="neo-hero-copy">
        <div class="neo-action-row">
            <a class="btn primary" href="<?= url('admin/tours/create') ?>">Thêm tour</a>
            <a class="btn ghost" href="<?= url('admin/bookings') ?>">Xử lý booking</a>
        </div>
    </div>
    <div class="neo-revenue-card">
        <div>
            <small>Doanh thu đã xác nhận</small>
            <strong><?= short_money($revenue) ?></strong>
            <p>Tính từ booking đã xác nhận hoặc hoàn tất.</p>
            <a href="<?= url('admin/bookings') ?>">Mở booking</a>
        </div>
    </div>
</section>

<section class="admin-stats neo-stats">
    <a class="admin-stat-card stat-blue" href="<?= url('admin/tours') ?>">
        <em>01</em>
        <span>Tour đang bán</span>
        <strong><?= (int) $tourCount ?></strong>
        <p>Catalog hoạt động</p>
    </a>
    <a class="admin-stat-card stat-green" href="<?= url('admin/bookings') ?>">
        <em>02</em>
        <span>Tổng booking</span>
        <strong><?= (int) $bookingCount ?></strong>
        <p>Yêu cầu từ khách</p>
    </a>
    <a class="admin-stat-card stat-amber" href="<?= url('admin/bookings') ?>">
        <em>03</em>
        <span>Chờ xử lý</span>
        <strong><?= (int) $pendingBookingCount ?></strong>
        <p>Cần cập nhật trạng thái</p>
    </a>
    <a class="admin-stat-card stat-rose" href="<?= url('admin/contacts') ?>">
        <em>04</em>
        <span>Liên hệ mới</span>
        <strong><?= (int) $unreadContactCount ?></strong>
        <p><?= (int) $contactCount ?> tin nhắn</p>
    </a>
    <a class="admin-stat-card stat-slate" href="<?= url('admin/users') ?>">
        <em>05</em>
        <span>Người dùng</span>
        <strong><?= (int) $userCount ?></strong>
        <p>Tài khoản hệ thống</p>
    </a>
</section>

<section class="neo-ops-grid">
    <article class="neo-focus-panel">
        <p class="admin-neo-kicker">Today focus</p>
        <h2>3 việc nên xử lý trước</h2>
        <a href="<?= url('admin/bookings') ?>"><span>1</span><strong><?= (int) $pendingBookingCount ?> booking chờ xác nhận</strong></a>
        <a href="<?= url('admin/contacts') ?>"><span>2</span><strong><?= (int) $unreadContactCount ?> liên hệ chưa đọc</strong></a>
        <a href="<?= url('admin/tours') ?>"><span>3</span><strong>Kiểm tra tour và ảnh đang hiển thị</strong></a>
    </article>

    <section class="admin-panel neo-panel">
        <div class="panel-heading">
            <div>
                <p class="admin-neo-kicker">Booking stream</p>
                <h2>Booking mới nhất</h2>
            </div>
            <a class="btn ghost small" href="<?= url('admin/bookings') ?>">Xem tất cả</a>
        </div>

        <?php if (empty($recentBookings)): ?>
            <div class="empty-state compact">
                <h3>Chưa có booking</h3>
                <p>Khi khách đặt tour, booking sẽ xuất hiện tại đây.</p>
            </div>
        <?php else: ?>
            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>Tour</th>
                        <th>Khách</th>
                        <th>Ngày đi</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($recentBookings as $booking): ?>
                        <tr>
                            <td><?= e($booking['tour_title']) ?></td>
                            <td><?= e($booking['full_name']) ?></td>
                            <td><?= e(date('d/m/Y', strtotime($booking['start_date']))) ?></td>
                            <td><?= money($booking['total_price']) ?></td>
                            <td><span class="status <?= e($booking['status']) ?>"><?= e(status_label($booking['status'])) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
</section>

<section class="admin-grid-two neo-grid-two">
    <section class="admin-panel neo-panel">
        <div class="panel-heading">
            <div>
                <p class="admin-neo-kicker">Inbox</p>
                <h2>Tin nhắn mới</h2>
            </div>
            <a class="btn ghost small" href="<?= url('admin/contacts') ?>">Xem liên hệ</a>
        </div>

        <?php if (empty($recentMessages)): ?>
            <div class="empty-state compact">
                <h3>Chưa có tin nhắn</h3>
                <p>Tin nhắn từ form liên hệ sẽ được đưa vào mục này.</p>
            </div>
        <?php else: ?>
            <div class="admin-message-list">
                <?php foreach ($recentMessages as $message): ?>
                    <a href="<?= url('admin/contacts') ?>">
                        <strong><?= e($message['name']) ?></strong>
                        <span><?= e($message['subject'] ?: 'Chưa chọn chủ đề') ?></span>
                        <small><?= e(date('d/m/Y H:i', strtotime($message['created_at']))) ?></small>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <section class="admin-panel neo-panel">
        <div class="panel-heading">
            <div>
                <p class="admin-neo-kicker">Tour lab</p>
                <h2>Tour cập nhật gần đây</h2>
            </div>
            <a class="btn ghost small" href="<?= url('admin/tours') ?>">Quản lý tour</a>
        </div>

        <?php if (empty($recentTours)): ?>
            <div class="empty-state compact">
                <h3>Chưa có tour</h3>
                <p>Dùng nút thêm tour để tạo dữ liệu đầu tiên.</p>
            </div>
        <?php else: ?>
            <div class="neo-tour-stack">
                <?php foreach ($recentTours as $tour): ?>
                    <a href="<?= url('admin/tours/edit/' . $tour['id']) ?>">
                        <img src="<?= e(media_url($tour['thumbnail'])) ?>" alt="<?= e($tour['title']) ?>">
                        <span>
                            <strong><?= e($tour['title']) ?></strong>
                            <small><?= e($tour['destination']) ?> · <?= money($tour['price']) ?></small>
                        </span>
                        <em><?= e($tour['status']) ?></em>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</section>
