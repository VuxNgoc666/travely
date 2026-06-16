<?php
$totalGuests = (int) $booking['guests'];
$unitPrice = (float) $tour['price'];
$totalPrice = (float) $booking['total_price'];
$paymentReference = $booking['payment_reference'] ?? '';
$transactionCode = $booking['transaction_code'] ?? '';
?>

<section class="account-hero">
    <div>
        <p class="eyebrow">Payment receipt</p>
        <h1>Thanh toán thành công</h1>
        <p>Booking #<?= (int) $booking['id'] ?> đã được ghi nhận với mã giao dịch <?= e($transactionCode) ?>.</p>
    </div>
    <a class="btn ghost" href="<?= url('account') ?>">Về tài khoản</a>
</section>

<section class="payment-layout">
    <div class="admin-panel payment-main">
        <div class="panel-heading">
            <h2>Biên nhận thanh toán</h2>
            <span class="status confirmed"><?= e(payment_status_label('paid')) ?></span>
        </div>

        <div class="payment-note">
            Cảm ơn bạn đã thanh toán. Travely đã ghi nhận mã giao dịch và chuyển booking sang trạng thái xác nhận.
        </div>

        <div class="payment-summary">
            <div class="payment-summary-row">
                <span>Tour</span>
                <strong><?= e($tour['title']) ?></strong>
            </div>
            <div class="payment-summary-row">
                <span>Ngày đi</span>
                <strong><?= e(date('d/m/Y', strtotime($booking['start_date']))) ?></strong>
            </div>
            <div class="payment-summary-row">
                <span>Số khách</span>
                <strong><?= $totalGuests ?></strong>
            </div>
            <div class="payment-summary-row">
                <span>Đơn giá</span>
                <strong><?= money($unitPrice) ?></strong>
            </div>
            <div class="payment-summary-row">
                <span>Tổng tiền</span>
                <strong><?= money($totalPrice) ?></strong>
            </div>
            <div class="payment-summary-row">
                <span>Mã giao dịch</span>
                <strong><?= e($transactionCode) ?></strong>
            </div>
            <div class="payment-summary-row">
                <span>Nội dung CK</span>
                <strong><?= e($paymentReference) ?></strong>
            </div>
        </div>

        <a class="btn primary full" href="<?= url('account') ?>">Xem booking của tôi</a>
    </div>

    <aside class="booking-card">
        <p>Trạng thái</p>
        <strong><?= e(status_label($booking['status'])) ?></strong>
        <div class="payment-summary">
            <div class="payment-summary-row">
                <span>Thanh toán</span>
                <strong><?= e(payment_status_label($booking['payment_status'] ?? 'unpaid')) ?></strong>
            </div>
            <div class="payment-summary-row">
                <span>Ngày tạo</span>
                <strong><?= e(date('d/m/Y H:i', strtotime($booking['created_at']))) ?></strong>
            </div>
        </div>

        <a class="btn ghost full" href="<?= url('tour/' . $tour['slug']) ?>">Quay lại tour</a>
    </aside>
</section>
