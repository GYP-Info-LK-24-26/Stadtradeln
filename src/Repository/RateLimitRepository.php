<?php

namespace App\Repository;

use App\Core\Database;

class RateLimitRepository
{
    // Längstes Zeitfenster aller Rate Limits; ältere Einträge werden nie mehr gelesen
    private const RETENTION_MINUTES = 60;

    public function record(string $ipAddress, string $action): bool
    {
        $this->deleteOld();

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

    private function deleteOld(): void
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare(
            "DELETE FROM rate_limits WHERE createdAt < DATE_SUB(NOW(), INTERVAL ? MINUTE)"
        );
        $retention = self::RETENTION_MINUTES;
        $stmt->bind_param("i", $retention);
        $stmt->execute();
    }

    // Bewusst nur REMOTE_ADDR: X-Forwarded-For kann jeder Client frei setzen
    // und damit die Rate Limits umgehen.
    public static function getClientIp(): string
    {
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
}
