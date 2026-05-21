<?php

class Controller
{
    public function view($view, array $data = [], $layout = 'main')
    {
        extract($data, EXTR_SKIP);

        ob_start();
        require APP_PATH . '/views/' . $view . '.php';
        $content = ob_get_clean();

        require APP_PATH . '/views/layouts/' . $layout . '.php';
    }

    protected function requireAuth()
    {
        if (!Auth::check()) {
            flash('error', 'Bạn cần đăng nhập để tiếp tục.');
            redirect('login');
        }
    }

    protected function requireAdmin()
    {
        $this->requireAuth();
        if (!Auth::isAdmin()) {
            http_response_code(403);
            $this->view('errors/403', ['title' => 'Không có quyền']);
            exit;
        }
    }

    protected function verifyCsrf()
    {
        if (!csrf_verify()) {
            flash('error', 'Phiên gửi biểu mẫu đã hết hạn. Vui lòng thử lại.');
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? url('')));
            exit;
        }
    }
}
