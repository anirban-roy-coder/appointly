<?php

class JWT {
    private static function base64UrlEncode(string $data): string {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function base64UrlDecode(string $data): string {
        return base64_decode(strtr($data, '-_', '+/'));
    }

    public static function generate(array $payload, int $expireSeconds = 86400): string {
        $secret = $_ENV['JWT_SECRET'] ?? 'your-very-secret-key-change-in-production';
        $header = self::base64UrlEncode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
        $payload['iat'] = time();
        $payload['exp'] = time() + $expireSeconds;
        $payloadEncoded = self::base64UrlEncode(json_encode($payload));
        $signature = self::base64UrlEncode(hash_hmac('sha256', "$header.$payloadEncoded", $secret, true));
        return "$header.$payloadEncoded.$signature";
    }

    public static function verify(string $token): ?array {
        $secret = $_ENV['JWT_SECRET'] ?? 'your-very-secret-key-change-in-production';
        $parts = explode('.', $token);
        if (count($parts) !== 3) return null;

        [$header, $payloadEncoded, $signature] = $parts;
        $expectedSig = self::base64UrlEncode(hash_hmac('sha256', "$header.$payloadEncoded", $secret, true));

        if (!hash_equals($expectedSig, $signature)) return null;

        $payload = json_decode(self::base64UrlDecode($payloadEncoded), true);
        if (!$payload || $payload['exp'] < time()) return null;

        return $payload;
    }

    public static function getBearerToken(): ?string {
        $headers = getallheaders();
        $auth = $headers['Authorization'] ?? $headers['authorization'] ?? '';
        if (str_starts_with($auth, 'Bearer ')) {
            return substr($auth, 7);
        }
        return null;
    }
}
