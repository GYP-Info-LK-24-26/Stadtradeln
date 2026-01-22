<?php

namespace App\Repository;

use App\Core\Database;

class PasswordResetRepository
{
    public function create(int $userId, string $token, \DateTime $expiresAt): bool
    {
        // Delete any existing tokens for this user
        $this->deleteByUserId($userId);

        $conn = Database::getConnection();
        $stmt = $conn->prepare(
            "INSERT INTO password_resets (userID, token, expiresAt) VALUES (?, ?, ?)"
        );

        $expiresAtStr = $expiresAt->format('Y-m-d H:i:s');
        $stmt->bind_param("iss", $userId, $token, $expiresAtStr);

        return $stmt->execute();
    }

    public function findByToken(string $token): ?array
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare(
            "SELECT id, userID, token, expiresAt FROM password_resets WHERE token = ?"
        );
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows !== 1) {
            return null;
        }

        return $result->fetch_assoc();
    }

    public function isValid(string $token): bool
    {
        $reset = $this->findByToken($token);

        if ($reset === null) {
            return false;
        }

        $expiresAt = new \DateTime($reset['expiresAt']);
        return $expiresAt > new \DateTime();
    }

    public function deleteByToken(string $token): bool
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
        $stmt->bind_param("s", $token);

        return $stmt->execute();
    }

    public function deleteByUserId(int $userId): bool
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare("DELETE FROM password_resets WHERE userID = ?");
        $stmt->bind_param("i", $userId);

        return $stmt->execute();
    }

    public function deleteExpired(): int
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare("DELETE FROM password_resets WHERE expiresAt < NOW()");
        $stmt->execute();

        return $stmt->affected_rows;
    }

    public function hasRecentReset(int $userId, int $cooldownMinutes = 10): bool
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare(
            "SELECT id FROM password_resets
             WHERE userID = ? AND createdAt > DATE_SUB(NOW(), INTERVAL ? MINUTE)
             LIMIT 1"
        );
        $stmt->bind_param("ii", $userId, $cooldownMinutes);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->num_rows > 0;
    }

    public static function generateToken(): string
    {
        return bin2hex(random_bytes(32));
    }
}
