<?php

session_start();

define('ROOT_PATH', __DIR__);
define('APP_PATH', ROOT_PATH . '/app');

require APP_PATH . '/config/config.php';
require APP_PATH . '/core/helpers.php';

spl_autoload_register(function ($class) {
    $paths = [
        APP_PATH . '/core/' . $class . '.php',
        APP_PATH . '/controllers/' . $class . '.php',
        APP_PATH . '/models/' . $class . '.php',
    ];

    foreach ($paths as $path) {
        if (is_file($path)) {
            require $path;
            return;
        }
    }
});

$app = new App();
require ROOT_PATH . '/routes/web.php';
$app->dispatch();