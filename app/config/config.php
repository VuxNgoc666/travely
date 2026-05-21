<?php

date_default_timezone_set('Asia/Ho_Chi_Minh');

define('APP_NAME', 'Travely');
define('APP_TAGLINE', 'Travel - Explore - Enjoy');

define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'travely_cinematic_mvc');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_CHARSET', 'utf8mb4');

$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
$baseUrl = rtrim($scriptDir, '/');
if ($baseUrl === '/' || $baseUrl === '.') {
    $baseUrl = '';
}

define('BASE_URL', $baseUrl);
