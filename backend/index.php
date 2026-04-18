<?php
declare(strict_types=1);


// Load .env manually
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) continue;
        [$key, $value] = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value);
    }
} else {
    die(json_encode(['error' => '.env file not found at: ' . $envFile]));
}

require_once __DIR__ . '/config/cors.php';
setCorsHeaders();

require_once __DIR__ . '/routes/api.php';