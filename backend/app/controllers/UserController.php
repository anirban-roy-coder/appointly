<?php

require_once __DIR__ . '/../services/UserService.php';
require_once __DIR__ . '/../middlewares/AuthMiddleware.php';
require_once __DIR__ . '/../utils/Response.php';

class UserController {
    private UserService $service;

    public function __construct() {
        $this->service = new UserService();
    }

    public function profile(): void {
        $payload = AuthMiddleware::handle();
        $result  = $this->service->getProfile($payload['user_id']);

        if (!$result['ok']) {
            Response::error($result['error'], $result['code']);
        }
        Response::success($result['data'], 'Profile fetched.');
    }
}
