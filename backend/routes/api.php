<?php

require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/UserController.php';
require_once __DIR__ . '/../app/controllers/AppointmentController.php';
require_once __DIR__ . '/../app/utils/Response.php';

$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri    = rtrim(preg_replace('#/+#', '/', $uri), '/');
$method = $_SERVER['REQUEST_METHOD'];

// Strip optional base path (e.g. /backend)
$uri = preg_replace('#^/backend#', '', $uri);

$routes = [
    'POST /api/auth/register' => fn() => (new AuthController())->register(),
    'POST /api/auth/login'    => fn() => (new AuthController())->login(),
    'GET /api/user/profile'   => fn() => (new UserController())->profile(),
    'GET /api/appointments'   => fn() => (new AppointmentController())->index(),
    'POST /api/appointments'  => fn() => (new AppointmentController())->store(),
];

$key = "$method $uri";

if (isset($routes[$key])) {
    $routes[$key]();
    exit;
}

if (preg_match('#^(PUT|PATCH) /api/appointments/(\d+)$#', $key, $m)) {
    (new AppointmentController())->update((int)$m[2]);
    exit;
}

if (preg_match('#^DELETE /api/appointments/(\d+)$#', $key, $m)) {
    (new AppointmentController())->cancel((int)$m[1]);
    exit;
}

Response::error('Route not found.', 404);
