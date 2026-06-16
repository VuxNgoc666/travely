<?php

class PaymentController extends Controller
{
    public function show($id)
    {
        $this->requireAuth();

        $booking = Booking::find((int) $id);
        if (!$booking) {
            http_response_code(404);
            $this->view('errors/404', ['title' => 'Không tìm thấy booking']);
            return;
        }

        $user = Auth::user();
        if (!$this->canAccessBooking($booking, $user)) {
            http_response_code(403);
            $this->view('errors/403', ['title' => 'Không có quyền truy cập']);
            return;
        }

        $tour = Tour::find($booking['tour_id']);
        if (!$tour) {
            http_response_code(404);
            $this->view('errors/404', ['title' => 'Không tìm thấy tour']);
            return;
        }

        $bankId = '970436';
        $accountNo = '1067264096';
        $accountName = 'VU VAN NGOC';
        $amount = (int) round((float) $booking['total_price']);
        $addInfo = (string) ($booking['payment_reference'] ?? '');
        $template = 'compact2';
        $qrImage = sprintf(
            'https://img.vietqr.io/image/%s-%s-%s.png?amount=%s&addInfo=%s&accountName=%s',
            rawurlencode($bankId),
            rawurlencode($accountNo),
            rawurlencode($template),
            rawurlencode((string) $amount),
            rawurlencode($addInfo),
            rawurlencode($accountName)
        );

        $this->view('payment/show', [
            'title' => 'Thanh toán booking',
            'booking' => $booking,
            'tour' => $tour,
            'qrImage' => $qrImage,
            'qrFallback' => asset('images/bank-qr.png'),
            'bankName' => 'Vietcombank',
            'bankAccountName' => $accountName,
            'bankAccountNumber' => $accountNo,
            'bankId' => $bankId,
            'amount' => $amount,
            'bankTransferContent' => $booking['payment_reference'],
        ]);
    }

    public function confirm($id)
    {
        $this->requireAuth();
        $this->verifyCsrf();

        $booking = Booking::find((int) $id);
        if (!$booking) {
            flash('error', 'Không tìm thấy booking.');
            redirect('account');
        }

        $user = Auth::user();
        if (!$this->canAccessBooking($booking, $user)) {
            http_response_code(403);
            $this->view('errors/403', ['title' => 'Không có quyền truy cập']);
            return;
        }

        if (in_array($booking['status'], ['completed', 'cancelled'], true) || ($booking['payment_status'] ?? 'unpaid') === 'paid') {
            flash('error', 'Booking này đã được thanh toán hoặc không còn khả dụng để thanh toán.');
            redirect('payment/' . $booking['id']);
        }

        $transactionCode = trim((string) ($_POST['transaction_code'] ?? ''));
        if ($transactionCode === '' || strlen($transactionCode) < 6) {
            flash('error', 'Vui lòng nhập mã giao dịch hợp lệ sau khi chuyển khoản.');
            redirect('payment/' . $booking['id']);
        }

        Booking::markPaid((int) $booking['id'], [
            'payment_method' => 'bank_transfer',
            'transaction_code' => $transactionCode,
        ]);

        flash('success', 'Đã lưu mã giao dịch và xác nhận thanh toán cho booking của bạn.');
        redirect('payment/success/' . $booking['id']);
    }

    public function success($id)
    {
        $this->requireAuth();

        $booking = Booking::find((int) $id);
        if (!$booking) {
            http_response_code(404);
            $this->view('errors/404', ['title' => 'Không tìm thấy booking']);
            return;
        }

        $user = Auth::user();
        if (!$this->canAccessBooking($booking, $user)) {
            http_response_code(403);
            $this->view('errors/403', ['title' => 'Không có quyền truy cập']);
            return;
        }

        if (($booking['payment_status'] ?? 'unpaid') !== 'paid') {
            redirect('payment/' . $booking['id']);
        }

        $tour = Tour::find($booking['tour_id']);
        if (!$tour) {
            http_response_code(404);
            $this->view('errors/404', ['title' => 'Không tìm thấy tour']);
            return;
        }

        $this->view('payment/success', [
            'title' => 'Thanh toán thành công',
            'booking' => $booking,
            'tour' => $tour,
        ]);
    }

    private function canAccessBooking(array $booking, array $user)
    {
        return Auth::isAdmin() || (int) ($booking['user_id'] ?? 0) === (int) ($user['id'] ?? 0);
    }
}
