<?php

function url($path = '')
{
    $path = trim($path, '/');
    return BASE_URL . ($path ? '/' . $path : '/');
}

function asset($path)
{
    return url('assets/' . ltrim($path, '/'));
}

function media_url($path)
{
    $path = trim((string) $path);
    if ($path === '') {
        return '';
    }

    if (preg_match('#^(https?:)?//#', $path) || str_starts_with($path, '/') || str_starts_with($path, 'data:')) {
        return $path;
    }

    if (str_starts_with($path, 'assets/')) {
        return url($path);
    }

    return asset($path);
}

function versioned_asset($path)
{
    $relativePath = 'assets/' . ltrim($path, '/');
    $fullPath = ROOT_PATH . '/public/' . $relativePath;
    $version = is_file($fullPath) ? filemtime($fullPath) : time();

    return url($relativePath) . '?v=' . $version;
}

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function money($amount)
{
    return number_format((float) $amount, 0, ',', '.') . 'đ';
}

function short_money($amount)
{
    if ((float) $amount >= 1000000) {
        return rtrim(rtrim(number_format((float) $amount / 1000000, 1, ',', ''), '0'), ',') . 'tr';
    }

    return money($amount);
}

function redirect($path)
{
    header('Location: ' . url($path));
    exit;
}

function active_nav($segment)
{
    $current = trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/');
    $segment = trim($segment, '/');
    if ($segment === '') {
        return $current === trim(BASE_URL, '/') ? 'is-active' : '';
    }

    return strpos($current, $segment) !== false ? 'is-active' : '';
}

function csrf_token()
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['_csrf'];
}

function csrf_field()
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

function csrf_verify()
{
    $token = $_POST['_csrf'] ?? '';
    return is_string($token) && hash_equals($_SESSION['_csrf'] ?? '', $token);
}

function flash($key, $message = null)
{
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return null;
    }

    if (!isset($_SESSION['flash'][$key])) {
        return null;
    }

    $value = $_SESSION['flash'][$key];
    unset($_SESSION['flash'][$key]);
    return $value;
}

function old($key, $default = '')
{
    return e($_POST[$key] ?? $_GET[$key] ?? $default);
}

function json_list($value)
{
    if (is_array($value)) {
        return $value;
    }

    $decoded = json_decode((string) $value, true);
    return is_array($decoded) ? $decoded : [];
}

function selected($value, $current)
{
    return (string) $value === (string) $current ? 'selected' : '';
}

function checked($value, $current)
{
    if (is_array($current)) {
        return in_array($value, $current, true) ? 'checked' : '';
    }

    return (string) $value === (string) $current ? 'checked' : '';
}

function status_label($status)
{
    $map = [
        'pending' => 'Chờ xác nhận',
        'confirmed' => 'Đã xác nhận',
        'completed' => 'Hoàn tất',
        'cancelled' => 'Đã hủy',
    ];

    return $map[$status] ?? $status;
}

function slugify($text)
{
    $text = trim((string) $text);
    $ascii = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
    $text = $ascii !== false ? $ascii : $text;
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    $text = trim($text, '-');

    return $text ?: 'tour-' . time();
}
