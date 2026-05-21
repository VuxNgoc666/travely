<?php

class ContactController extends Controller
{
    public function index()
    {
        $this->view('contact/index', [
            'title' => 'Liên hệ với Travely',
        ]);
    }

    public function send()
    {
        $this->verifyCsrf();

        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');

        if ($name === '' || $phone === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $message === '') {
            flash('error', 'Vui lòng nhập đầy đủ họ tên, số điện thoại, email và nội dung cần hỗ trợ.');
            redirect('contact');
        }

        ContactMessage::create([
            'name' => $name,
            'phone' => $phone,
            'email' => $email,
            'subject' => $subject,
            'message' => $message,
        ]);

        flash('success', 'Travely đã nhận yêu cầu. Tư vấn viên sẽ liên hệ lại trong thời gian sớm nhất.');
        redirect('contact');
    }
}
