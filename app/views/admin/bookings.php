<section class="admin-panel">
    <div class="panel-heading">
        <h2>Booking</h2>
        <span><?= count($bookings) ?> yêu cầu</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
            <tr>
                <th>Khách</th>
                <th>Tour</th>
                <th>Ngày đi</th>
                <th>Số khách</th>
                <th>Tổng tiền</th>
                <th>Thanh toán</th>
                <th>Mã GD</th>
                <th>Trạng thái</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($bookings as $booking): ?>
                <tr>
                    <td><strong><?= e($booking['full_name']) ?></strong><span class="table-sub"><?= e($booking['phone']) ?></span></td>
                    <td><?= e($booking['tour_title']) ?></td>
                    <td><?= e(date('d/m/Y', strtotime($booking['start_date']))) ?></td>
                    <td><?= (int) $booking['guests'] ?></td>
                    <td><?= money($booking['total_price']) ?></td>
                    <td><span class="status <?= e(($booking['payment_status'] ?? 'unpaid') === 'paid' ? 'confirmed' : 'pending') ?>"><?= e(payment_status_label($booking['payment_status'] ?? 'unpaid')) ?></span></td>
                    <td><?= e($booking['transaction_code'] ?? '-') ?></td>
                    <td>
                        <?php if (in_array($booking['status'], ['completed', 'cancelled'], true)): ?>
                            <span class="status-locked"><?= e(status_label($booking['status'])) ?></span>
                        <?php else: ?>
                            <form class="inline-form" method="post" action="<?= url('admin/bookings/status/' . $booking['id']) ?>">
                                <?= csrf_field() ?>
                                <select name="status">
                                    <?php foreach (['pending', 'confirmed', 'completed', 'cancelled'] as $status): ?>
                                        <option value="<?= e($status) ?>" <?= selected($status, $booking['status']) ?>><?= e(status_label($status)) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button class="btn ghost small" type="submit">Lưu</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
