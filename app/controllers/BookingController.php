<?php

class BookingController extends Controller
{
    public function store()
    {
        $this->requireAuth();
        $this->verifyCsrf();

        $tour = Tour::find($_POST['tour_id'] ?? 0);
        if (!$tour) {
            flash('error', 'Tour không tồn tại.');
            redirect('tours');
        }

        $guests = (int) ($_POST['guests'] ?? 1);
        if ($guests < 1 || $guests > 20) {
            flash('error', 'Số khách phải từ 1 đến 20 người.');
            redirect('tour/' . $tour['slug']);
        }

        $startDate = $_POST['start_date'] ?? '';
        $today = date('Y-m-d');
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate) || $startDate < $today) {
            flash('error', 'Vui lòng chọn ngày khởi hành từ hôm nay trở đi.');
            redirect('tour/' . $tour['slug']);
        }

        $availableDates = array_values(array_filter(json_list($tour['start_dates']), function ($date) use ($today) {
            return is_string($date) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) && $date >= $today;
        }));
        if (!$availableDates || !in_array($startDate, $availableDates, true)) {
            flash('error', 'Ngày khởi hành không nằm trong lịch mở bán của tour.');
            redirect('tour/' . $tour['slug']);
        }

        $user = Auth::user();
        $fullName = trim($_POST['full_name'] ?? $user['name']);
        $phone = trim($_POST['phone'] ?? $user['phone']);
        $email = trim($_POST['email'] ?? $user['email']);

        if ($fullName === '' || $phone === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Vui lòng nhập họ tên, số điện thoại và email hợp lệ.');
            redirect('tour/' . $tour['slug']);
        }

        $bookingId = Booking::create([
            'tour_id' => $tour['id'],
            'user_id' => $user['id'],
            'start_date' => $startDate,
            'guests' => $guests,
            'full_name' => $fullName,
            'phone' => $phone,
            'email' => $email,
            'notes' => $_POST['notes'] ?? '',
            'total_price' => (float) $tour['price'] * $guests,
        ]);

        flash('success', 'Đơn booking đã tạo. Bạn có thể thanh toán ngay ở trang tiếp theo.');
        redirect('payment/' . $bookingId);
    }
}
