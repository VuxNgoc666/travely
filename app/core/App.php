<?php

class App
{
    private $routes = [
        'GET' => [],
        'POST' => [],
    ];

    public function get($path, $handler)
    {
        $this->routes['GET'][] = [$this->normalize($path), $handler];
    }

    public function post($path, $handler)
    {
        $this->routes['POST'][] = [$this->normalize($path), $handler];
    }

    public function dispatch()
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $path = $this->currentPath();
        $routes = $this->routes[$method] ?? [];

        foreach ($routes as $route) {
            [$pattern, $handler] = $route;
            $params = $this->match($pattern, $path);
            if ($params !== false) {
                return $this->runHandler($handler, $params);
            }
        }

        http_response_code(404);
        $controller = new Controller();
        $controller->view('errors/404', ['title' => 'Không tìm thấy']);
        return null;
    }

    private function normalize($path)
    {
        return trim($path, '/');
    }

    private function currentPath()
    {
        if (isset($_GET['url'])) {
            return trim($_GET['url'], '/');
        }

        $requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));

        if ($scriptDir && $scriptDir !== '/' && strpos($requestPath, $scriptDir) === 0) {
            $requestPath = substr($requestPath, strlen($scriptDir));
        }

        $requestPath = preg_replace('#^/index\.php#', '', $requestPath);
        return trim($requestPath, '/');
    }

    private function match($pattern, $path)
    {
        $regex = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '(?P<$1>[^/]+)', $pattern);
        $regex = '#^' . $regex . '$#';

        if (!preg_match($regex, $path, $matches)) {
            return false;
        }

        $params = [];
        foreach ($matches as $key => $value) {
            if (!is_int($key)) {
                $params[] = $value;
            }
        }

        return $params;
    }

    private function runHandler($handler, array $params)
    {
        [$controllerName, $action] = explode('@', $handler);

        if (!class_exists($controllerName)) {
            throw new RuntimeException('Controller not found: ' . $controllerName);
        }

        $controller = new $controllerName();
        return call_user_func_array([$controller, $action], $params);
    }
}

