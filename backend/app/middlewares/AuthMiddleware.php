<?php

require_once __DIR__ . '/../utils/JWT.php';
require_once __DIR__ . '/../utils/Response.php';

class AuthMiddleware {
    public static function handle(): array {
        $token = JWT::getBearerToken();
        if (!$token) {
            Response::error('Unauthorized: No token provided.', 401);
        }
        $payload = JWT::verify($token);
        if (!$payload) {
            Response::error('Unauthorized: Invalid or expired token.', 401);
        }
        return $payload;
    }
}
