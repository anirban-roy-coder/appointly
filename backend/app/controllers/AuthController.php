<?php

require_once __DIR__ . '/../services/UserService.php';
require_once __DIR__ . '/../utils/Response.php';

class AuthController {
    private UserService $service;

    public function __construct() {
        $this->service = new UserService();
    }

    public function register(): void {
        $input  = json_decode(file_get_contents('php://input'), true) ?? [];
        $result = $this->service->register($input);

        if (!$result['ok']) {
            Response::error($result['error'], $result['code'], $result['errors'] ?? null);
        }
        Response::success($result['data'], 'Registration successful.', 201);
    }

    public function login(): void {
        $input  = json_decode(file_get_contents('php://input'), true) ?? [];
        $result = $this->service->login($input);

        if (!$result['ok']) {
            Response::error($result['error'], $result['code'], $result['errors'] ?? null);
        }
        Response::success($result['data'], 'Logged in.');
    }
}
