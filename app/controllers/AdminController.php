<?php

class AdminController extends Controller
{
    public function __construct()
    {
        $this->requireAdmin();
    }

    public function dashboard()
    {
        $recentMessages = array_slice(ContactMessage::all(), 0, 5);

        $this->view('admin/dashboard', [
            'title' => 'Tổng quan',
            'tourCount' => Tour::count(),
            'bookingCount' => Booking::count(),
            'pendingBookingCount' => Booking::countByStatus('pending'),
            'userCount' => User::count(),
            'revenue' => Booking::revenue(),
            'recentBookings' => Booking::recent(6),
            'recentTours' => Tour::all(['admin' => true, 'sort' => 'newest', 'limit' => 5]),
            'contactCount' => ContactMessage::count(),
            'unreadContactCount' => ContactMessage::unreadCount(),
            'recentMessages' => $recentMessages,
        ], 'admin');
    }

    public function tours()
    {
        $this->view('admin/tours', [
            'title' => 'Quản lý tour',
            'tours' => Tour::all(['admin' => true]),
        ], 'admin');
    }

    public function tourForm($id = null)
    {
        $tour = $id ? Tour::find($id) : Tour::blank();
        if (!$tour) {
            flash('error', 'Không tìm thấy tour.');
            redirect('admin/tours');
        }

        $this->view('admin/tour_form', [
            'title' => $id ? 'Sửa tour' : 'Thêm tour',
            'tour' => $tour,
        ], 'admin');
    }

    public function saveTour()
    {
        $this->verifyCsrf();

        $id = !empty($_POST['id']) ? (int) $_POST['id'] : null;
        $title = trim($_POST['title'] ?? '');
        if ($title === '') {
            flash('error', 'Tên tour không được để trống.');
            redirect($id ? 'admin/tours/edit/' . $id : 'admin/tours/create');
        }

        $slug = slugify(trim($_POST['slug'] ?? '') ?: $title);
        if (Tour::slugExists($slug, $id)) {
            flash('error', 'Slug này đã tồn tại. Vui lòng đổi slug hoặc tên tour.');
            redirect($id ? 'admin/tours/edit/' . $id : 'admin/tours/create');
        }

        try {
            if ($id) {
                Tour::update($id, $_POST);
                flash('success', 'Đã cập nhật tour.');
            } else {
                Tour::create($_POST);
                flash('success', 'Đã thêm tour mới.');
            }
        } catch (PDOException $exception) {
            flash('error', 'Không lưu được tour. Vui lòng kiểm tra dữ liệu và thử lại.');
            redirect($id ? 'admin/tours/edit/' . $id : 'admin/tours/create');
        }

        redirect('admin/tours');
    }

    public function deleteTour($id)
    {
        $this->verifyCsrf();
        Tour::delete((int) $id);
        flash('success', 'Đã xóa tour.');
        redirect('admin/tours');
    }

    public function bookings()
    {
        $this->view('admin/bookings', [
            'title' => 'Quản lý đặt tour',
            'bookings' => Booking::all(),
        ], 'admin');
    }

    public function updateBookingStatus($id)
    {
        $this->verifyCsrf();
        $booking = Booking::find((int) $id);
        if (!$booking) {
            flash('error', 'Không tìm thấy booking.');
            redirect('admin/bookings');
        }

        if (in_array($booking['status'], ['completed', 'cancelled'], true)) {
            flash('error', 'Booking đã hoàn tất hoặc đã hủy nên không thể đổi trạng thái.');
            redirect('admin/bookings');
        }

        Booking::updateStatus((int) $id, $_POST['status'] ?? 'pending');
        flash('success', 'Đã cập nhật trạng thái booking.');
        redirect('admin/bookings');
    }

    public function contacts()
    {
        $this->view('admin/contacts', [
            'title' => 'Liên hệ',
            'messages' => ContactMessage::all(),
            'newCount' => ContactMessage::unreadCount(),
        ], 'admin');
    }

    public function updateContactStatus($id)
    {
        $this->verifyCsrf();
        ContactMessage::updateStatus((int) $id, $_POST['status'] ?? 'new');
        flash('success', 'Đã cập nhật trạng thái liên hệ.');
        redirect('admin/contacts');
    }

    public function users()
    {
        $this->view('admin/users', [
            'title' => 'Quản lý người dùng',
            'users' => User::all(),
        ], 'admin');
    }

    public function updateUserRole($id)
    {
        $this->verifyCsrf();
        User::updateRole((int) $id, $_POST['role'] ?? 'user');
        flash('success', 'Đã cập nhật quyền người dùng.');
        redirect('admin/users');
    }

    public function deleteUser($id)
    {
        $this->verifyCsrf();
        User::delete((int) $id);
        flash('success', 'Đã xóa người dùng thường.');
        redirect('admin/users');
    }
}
