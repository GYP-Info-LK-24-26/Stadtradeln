<?php

namespace App\Repository;

use App\Core\Database;
use App\Models\User;

class UserRepository
{
    public function findByEmail(string $email): ?User
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare("SELECT id, teamID, name, passHash FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows !== 1) {
            return null;
        }

        $stmt->bind_result($id, $teamId, $name, $passHash);
        $stmt->fetch();

        return User::fromArray([
            'id' => $id,
            'name' => $name,
            'teamID' => $teamId,
            'email' => $email,
            'passHash' => $passHash,
        ]);
    }

    public function emailExists(string $email): bool
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        return $stmt->num_rows > 0;
    }

    public function create(User $user): int
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare("INSERT INTO users (passHash, name, email) VALUES (?, ?, ?)");

        $hashedPassword = password_hash($user->password, PASSWORD_DEFAULT);
        $stmt->bind_param("sss", $hashedPassword, $user->name, $user->email);

        if (!$stmt->execute()) {
            throw new \RuntimeException("Benutzer konnte nicht erstellt werden");
        }

        return $conn->insert_id;
    }

    public function updateLastLogin(int $userId): bool
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare("UPDATE users SET lastLogin = NOW() WHERE id = ?");
        $stmt->bind_param("i", $userId);

        return $stmt->execute();
    }

    public function updateTeam(int $userId, ?int $teamId): bool
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare("UPDATE users SET teamID = ? WHERE id = ?");
        $stmt->bind_param("ii", $teamId, $userId);

        return $stmt->execute();
    }

    public function findByTeam(int $teamId): array
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare("SELECT id, name FROM users WHERE teamID = ?");
        $stmt->bind_param("i", $teamId);
        $stmt->execute();
        $result = $stmt->get_result();

        $users = [];
        while ($row = $result->fetch_assoc()) {
            $row['teamID'] = $teamId;
            $users[] = User::fromArray($row);
        }

        return $users;
    }

    public function findById(int $id): ?User
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare("SELECT id, teamID, email, passHash, name FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows !== 1) {
            return null;
        }

        $stmt->bind_result($id, $teamId, $email, $passHash, $name);
        $stmt->fetch();

        return User::fromArray([
            'id' => $id,
            'name' => $name,
            'teamID' => $teamId,
            'email' => $email,
            'passHash' => $passHash,
        ]);
    }

    public function updatePassword(int $userId, string $hashedPassword): bool
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare("UPDATE users SET passHash = ? WHERE id = ?");
        $stmt->bind_param("si", $hashedPassword, $userId);

        return $stmt->execute();
    }

    public function updateName(int $userId, string $name): bool
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare("UPDATE users SET name = ? WHERE id = ?");
        $stmt->bind_param("si", $name, $userId);

        return $stmt->execute();
    }

    public function updateEmail(int $userId, string $email): bool
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare("UPDATE users SET email = ? WHERE id = ?");
        $stmt->bind_param("si", $email, $userId);

        return $stmt->execute();
    }

    public function findByTeamWithDistance(?int $teamId, int $page = 0): array
    {
        $conn = Database::getConnection();

        $sql = "SELECT users.id, users.name, COALESCE(SUM(tours.distance), 0) AS totalDistance
                FROM users
                LEFT JOIN tours ON users.id = tours.userID ";

        if ($teamId !== null) {
            $sql .= "WHERE teamID = ? ";
        }

        $sql .= "GROUP BY users.id, users.name ORDER BY totalDistance DESC ";

        if ($teamId === null) {
            $sql .= "LIMIT 20 OFFSET ?";
        }

        $stmt = $conn->prepare($sql);

        if ($teamId !== null) {
            $stmt->bind_param("i", $teamId);
        } else {
            $offset = $page * 20;
            $stmt->bind_param("i", $offset);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $users = [];
        while ($row = $result->fetch_assoc()) {
            $row['teamID'] = $teamId;
            $users[] = User::fromArray($row);
        }

        return $users;
    }
}
