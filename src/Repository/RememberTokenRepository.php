<?php

namespace App\Repository;

use App\Core\Database;

class RememberTokenRepository
{
    public function create(int $userId, string $selector, string $tokenHash, \DateTime $expiresAt): bool
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare(
            "INSERT INTO remember_tokens (userID, selector, tokenHash, expiresAt) VALUES (?, ?, ?, ?)"
        );

        $expiresAtStr = $expiresAt->format('Y-m-d H:i:s');
        $stmt->bind_param("isss", $userId, $selector, $tokenHash, $expiresAtStr);

        return $stmt->execute();
    }

    /** Nur nicht abgelaufene Tokens. */
    public function findBySelector(string $selector): ?array
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare(
            "SELECT id, userID, tokenHash FROM remember_tokens WHERE selector = ? AND expiresAt > NOW()"
        );
        $stmt->bind_param("s", $selector);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows !== 1) {
            return null;
        }

        return $result->fetch_assoc();
    }

    public function deleteBySelector(string $selector): bool
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare("DELETE FROM remember_tokens WHERE selector = ?");
        $stmt->bind_param("s", $selector);

        return $stmt->execute();
    }

    public function deleteByUserId(int $userId): bool
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare("DELETE FROM remember_tokens WHERE userID = ?");
        $stmt->bind_param("i", $userId);

        return $stmt->execute();
    }

    public function deleteExpired(): bool
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare("DELETE FROM remember_tokens WHERE expiresAt <= NOW()");

        return $stmt->execute();
    }
}
