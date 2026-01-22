<?php

namespace App\Repository;

use App\Core\Database;

class RateLimitRepository
{
    public function record(string $ipAddress, string $action): bool
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare("INSERT INTO rate_limits (ipAddress, action) VALUES (?, ?)");
        $stmt->bind_param("ss", $ipAddress, $action);

        return $stmt->execute();
    }

    public function isRateLimited(string $ipAddress, string $action, int $maxAttempts = 5, int $minutes = 60): bool
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare(
            "SELECT COUNT(*) FROM rate_limits
             WHERE ipAddress = ? AND action = ? AND createdAt > DATE_SUB(NOW(), INTERVAL ? MINUTE)"
        );
        $stmt->bind_param("ssi", $ipAddress, $action, $minutes);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();

        return $count >= $maxAttempts;
    }

    public static function getClientIp(): string
    {
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return trim(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0]);
        }

        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
}
