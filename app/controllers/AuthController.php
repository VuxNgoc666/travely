<?php

class AuthController extends Controller
{
    public function login()
    {
        if (Auth::check()) {
            redirect(Auth::isAdmin() ? 'admin' : 'account');
        }

        $this->view('auth/login', ['title' => 'Đăng nhập']);
    }

    public function authenticate()
    {
        $this->verifyCsrf();

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (Auth::attempt($email, $password)) {
            flash('success', 'Đăng nhập thành công.');
            redirect(Auth::isAdmin() ? 'admin' : 'account');
        }

        flash('error', 'Email hoặc mật khẩu chưa đúng.');
        redirect('login');
    }

    public function register()
    {
        if (Auth::check()) {
            redirect('account');
        }

        $this->view('auth/register', ['title' => 'Đăng ký']);
    }

    public function store()
    {
        $this->verifyCsrf();

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6) {
            flash('error', 'Vui lòng nhập đủ thông tin, mật khẩu tối thiểu 6 ký tự.');
            redirect('register');
        }

        if (User::findByEmail($email)) {
            flash('error', 'Email này đã được đăng ký.');
            redirect('register');
        }

        $id = User::create([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'password' => $password,
        ]);

        Auth::loginById($id);
        flash('success', 'Tài khoản đã sẵn sàng. Bạn có thể đặt tour ngay.');
        redirect('account');
    }

    public function logout()
    {
        Auth::logout();
        flash('success', 'Bạn đã đăng xuất.');
        redirect('');
    }
}

