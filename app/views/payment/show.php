<?php
$totalGuests = (int) $booking['guests'];
$unitPrice = (float) $tour['price'];
$totalPrice = (float) $booking['total_price'];
$paymentReference = $booking['payment_reference'] ?? '';
$isPaid = ($booking['payment_status'] ?? 'unpaid') === 'paid';
?>

<section class="account-hero">
    <div>
        <p class="eyebrow">Checkout counter</p>
        <h1>Thanh toán booking #<?= (int) $booking['id'] ?></h1>
        <p><?= e($tour['title']) ?> · <?= e(date('d/m/Y', strtotime($booking['start_date']))) ?> · <?= $totalGuests ?> khách</p>
    </div>
    <a class="btn ghost" href="<?= url('account') ?>">Về tài khoản</a>
</section>

<section class="payment-layout">
    <div class="admin-panel payment-main">
        <div class="panel-heading">
            <h2>Thanh toán chuyển khoản / QR</h2>
            <span class="status <?= e($isPaid ? 'confirmed' : 'pending') ?>"><?= e($isPaid ? payment_status_label('paid') : payment_status_label('unpaid')) ?></span>
        </div>

        <div class="payment-steps">
            <div class="payment-step">
                <strong>1. Quét QR</strong>
                <span>Quét mã VietQR Vietcombank bên dưới để tự điền số tài khoản, số tiền và nội dung.</span>
            </div>
            <div class="payment-step">
                <strong>2. Chuyển khoản</strong>
                <span>Thanh toán đúng số tiền và nhớ giữ nguyên nội dung chuyển khoản.</span>
            </div>
            <div class="payment-step">
                <strong>3. Nhập mã giao dịch</strong>
                <span>Dán mã giao dịch từ app ngân hàng vào form để lưu vào booking.</span>
            </div>
        </div>

        <?php if ($isPaid): ?>
            <div class="payment-note">
                Đơn này đã được ghi nhận thanh toán. Mã giao dịch: <strong><?= e($booking['transaction_code'] ?? '') ?></strong>
            </div>
        <?php else: ?>
            <div class="payment-qr-card">
                <img src="<?= e($qrImage) ?>" alt="QR chuyển khoản Travely" onerror="this.onerror=null;this.src='<?= e($qrFallback) ?>';">
            </div>

            <div class="payment-note">
                Ngân hàng: <strong><?= e($bankName) ?></strong><br>
                Đơn giá tour: <strong><?= money($unitPrice) ?></strong> x <?= $totalGuests ?> khách<br>
                Số tiền cần chuyển: <strong><?= money($totalPrice) ?></strong><br>
                Tên tài khoản: <strong><?= e($bankAccountName) ?></strong><br>
                Số tài khoản: <strong><?= e($bankAccountNumber) ?></strong><br>
                Nội dung chuyển khoản: <strong><?= e($paymentReference) ?></strong>
            </div>

            <form method="post" action="<?= url('payment/' . $booking['id']) ?>">
                <?= csrf_field() ?>
                <label class="wide">Mã giao dịch sau khi chuyển khoản
                    <input name="transaction_code" value="<?= e($booking['transaction_code'] ?? '') ?>" placeholder="Ví dụ: 1234567890" required>
                </label>
                <button class="btn primary full" type="submit">Tôi đã chuyển khoản</button>
            </form>
        <?php endif; ?>
    </div>

    <aside class="booking-card">
        <p>Tổng tiền</p>
        <strong><?= money($booking['total_price']) ?></strong>
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
                <span>Tổng thanh toán</span>
                <strong><?= money($totalPrice) ?></strong>
            </div>
            <div class="payment-summary-row">
                <span>Mã nội dung</span>
                <strong><?= e($paymentReference) ?></strong>
            </div>
            <div class="payment-summary-row">
                <span>Ngân hàng</span>
                <strong><?= e($bankName) ?></strong>
            </div>
            <div class="payment-summary-row">
                <span>Trạng thái</span>
                <strong><?= e(payment_status_label($booking['payment_status'] ?? 'unpaid')) ?></strong>
            </div>
        </div>

        <a class="btn ghost full" href="<?= url('tour/' . $tour['slug']) ?>">Xem lại tour</a>
    </aside>
</section>
