<?php

namespace App\Repository;

use App\Core\Database;

class RateLimitRepository
{
    public function record(string $ipAddress, string $action): bool
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare(
            "INSERT INTO rate_limits (ipAddress, action) VALUES (?, ?)"
        );
        $stmt->bind_param("ss", $ipAddress, $action);

        return $stmt->execute();
    }

    public function countRecent(string $ipAddress, string $action, int $minutes = 60): int
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare(
            "SELECT COUNT(*) as count FROM rate_limits
             WHERE ipAddress = ? AND action = ? AND createdAt > DATE_SUB(NOW(), INTERVAL ? MINUTE)"
        );
        $stmt->bind_param("ssi", $ipAddress, $action, $minutes);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        return (int) $row['count'];
    }

    public function isRateLimited(string $ipAddress, string $action, int $maxAttempts = 5, int $minutes = 60): bool
    {
        return $this->countRecent($ipAddress, $action, $minutes) >= $maxAttempts;
    }

    public function deleteOld(int $olderThanMinutes = 1440): int
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare(
            "DELETE FROM rate_limits WHERE createdAt < DATE_SUB(NOW(), INTERVAL ? MINUTE)"
        );
        $stmt->bind_param("i", $olderThanMinutes);
        $stmt->execute();

        return $stmt->affected_rows;
    }

    public static function getClientIp(): string
    {
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($ips[0]);
        }

        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
}
