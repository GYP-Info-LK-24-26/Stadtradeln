<?php

namespace App\Repository;

use App\Core\Database;

/**
 * Registrierungen, deren E-Mail-Adresse noch nicht bestätigt ist. Der Account
 * (users) entsteht erst, wenn der Link aus der Bestätigungs-E-Mail geöffnet wird.
 * Gespeichert werden nur der Passwort-Hash und der SHA-256-Hash des Tokens.
 */
class PendingRegistrationRepository
{
    /** Legt die Registrierung an; eine ältere für dieselbe Adresse wird ersetzt. */
    public function create(string $name, string $email, string $passHash, string $token, \DateTime $expiresAt): bool
    {
        $this->deleteExpired();

        // Upsert statt DELETE + INSERT: zwei gleichzeitige Anfragen scheitern nicht am UNIQUE-Key
        $conn = Database::getConnection();
        $stmt = $conn->prepare(
            "INSERT INTO pending_registrations (name, email, passHash, token, expiresAt) VALUES (?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE name = VALUES(name), passHash = VALUES(passHash),
                 token = VALUES(token), expiresAt = VALUES(expiresAt), createdAt = CURRENT_TIMESTAMP"
        );

        $tokenHash = self::hash($token);
        $expiresAtStr = $expiresAt->format('Y-m-d H:i:s');
        $stmt->bind_param("sssss", $name, $email, $passHash, $tokenHash, $expiresAtStr);

        return $stmt->execute();
    }

    /** Noch gültige Registrierung zum Token aus dem E-Mail-Link, sonst null. */
    public function findValidByToken(string $token): ?array
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare(
            "SELECT id, name, email, passHash FROM pending_registrations
             WHERE token = ? AND expiresAt > NOW()"
        );
        $tokenHash = self::hash($token);
        $stmt->bind_param("s", $tokenHash);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->num_rows === 1 ? $result->fetch_assoc() : null;
    }

    /** Noch gültige Registrierung zur E-Mail-Adresse, sonst null. */
    public function findValidByEmail(string $email): ?array
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare(
            "SELECT id, name, email, passHash FROM pending_registrations
             WHERE email = ? AND expiresAt > NOW()"
        );
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->num_rows === 1 ? $result->fetch_assoc() : null;
    }

    public function deleteByEmail(string $email): bool
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare("DELETE FROM pending_registrations WHERE email = ?");
        $stmt->bind_param("s", $email);

        return $stmt->execute();
    }

    private function deleteExpired(): void
    {
        $conn = Database::getConnection();
        $conn->query("DELETE FROM pending_registrations WHERE expiresAt < NOW()");
    }

    public static function generateToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    private static function hash(string $token): string
    {
        return hash('sha256', $token);
    }
}
