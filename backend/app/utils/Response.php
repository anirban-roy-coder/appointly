<?php

class Response {
    public static function json(bool $success, mixed $data, string $message, int $code = 200): void {
        http_response_code($code);
        echo json_encode([
            'success' => $success,
            'data'    => $data,
            'message' => $message,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    public static function success(mixed $data = null, string $message = 'Success', int $code = 200): void {
        self::json(true, $data, $message, $code);
    }

    public static function error(string $message = 'Error', int $code = 400, mixed $data = null): void {
        self::json(false, $data, $message, $code);
    }
}
