<?php

class UserController extends Controller
{
    public function dashboard()
    {
        $this->requireAuth();
        $user = Auth::user();

        $this->view('user/dashboard', [
            'title' => 'Tài khoản của tôi',
            'user' => $user,
            'bookings' => Booking::forUser($user['id']),
            'favorites' => Favorite::forUser($user['id']),
        ]);
    }

    public function toggleFavorite()
    {
        $this->requireAuth();
        $this->verifyCsrf();

        $tourId = (int) ($_POST['tour_id'] ?? 0);
        if ($tourId > 0) {
            Favorite::toggle(Auth::user()['id'], $tourId);
        }

        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? url('tours')));
        exit;
    }
}
