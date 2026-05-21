<?php

$basePath = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
$basePath = rtrim($basePath, '/');
$target = ($basePath !== '' ? $basePath : '') . '/public/';

header('Location: ' . $target, true, 302);
exit;
