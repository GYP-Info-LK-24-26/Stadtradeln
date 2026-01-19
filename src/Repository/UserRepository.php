<?php

namespace App\Repository;

use App\Core\Database;
use App\Models\User;

class UserRepository
{
    public function findByEmail(string $email): ?User
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare("SELECT id, teamID, username, passHash FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows !== 1) {
            return null;
        }

        $stmt->bind_result($id, $teamId, $username, $passHash);
        $stmt->fetch();

        $user = new User($id, $username, $teamId);
        $user->email = $email;
        $user->password = $passHash; // hashed password for verification

        return $user;
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
        $stmt = $conn->prepare(
            "INSERT INTO users (username, passHash, firstName, lastName, email, teamID) 
             VALUES (?, ?, ?, ?, ?, -1)"
        );

        $hashedPassword = password_hash($user->password . $user->email, PASSWORD_DEFAULT);

        $stmt->bind_param(
            "sssss",
            $user->username,
            $hashedPassword,
            $user->firstName,
            $user->lastName,
            $user->email
        );

        if (!$stmt->execute()) {
            throw new \RuntimeException("Benutzer konnte nicht erstellt werden");
        }

        return $conn->insert_id;
    }

    public function updateTeam(int $userId, int $teamId): bool
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare("UPDATE users SET teamID = ? WHERE id = ?");
        $stmt->bind_param("ii", $teamId, $userId);

        return $stmt->execute();
    }

    public function verifyPassword(User $user, string $password): bool
    {
        return password_verify($password . $user->email, $user->password);
    }

    public function findByTeam(int $teamId): array
    {
        $conn = Database::getConnection();
        
        $sql = "SELECT id, username FROM users";
        if ($teamId !== -1) {
            $sql .= " WHERE teamID = ?";
        }

        $stmt = $conn->prepare($sql);
        
        if ($teamId !== -1) {
            $stmt->bind_param("i", $teamId);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = new User($row['id'], $row['username'], $teamId, 0);
        }

        return $users;
    }

    public function findById(int $id): ?User
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare("SELECT id, teamID, username, email, passHash FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows !== 1) {
            return null;
        }

        $stmt->bind_result($id, $teamId, $username, $email, $passHash);
        $stmt->fetch();

        $user = new User($id, $username, $teamId);
        $user->email = $email;
        $user->password = $passHash;

        return $user;
    }

    public function updatePassword(int $userId, string $hashedPassword): bool
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare("UPDATE users SET passHash = ? WHERE id = ?");
        $stmt->bind_param("si", $hashedPassword, $userId);

        return $stmt->execute();
    }

    public function findByTeamWithDistance(int $teamId, int $page = 0): array
    {
        $conn = Database::getConnection();

        $sql = "SELECT users.id, users.username, COALESCE(SUM(tours.distance), 0) AS totalDistance 
                FROM users 
                LEFT JOIN tours ON users.id = tours.userID ";

        if ($teamId !== -1) {
            $sql .= "WHERE teamID = ? ";
        }

        $sql .= "GROUP BY users.id, users.username ORDER BY totalDistance DESC ";

        if ($teamId === -1) {
            $sql .= "LIMIT 20 OFFSET ?";
        }

        $stmt = $conn->prepare($sql);

        if ($teamId !== -1) {
            $stmt->bind_param("i", $teamId);
        } else {
            $offset = $page * 20;
            $stmt->bind_param("i", $offset);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = new User(
                $row['id'],
                $row['username'],
                $teamId,
                $row['totalDistance']
            );
        }

        return $users;
    }
}
