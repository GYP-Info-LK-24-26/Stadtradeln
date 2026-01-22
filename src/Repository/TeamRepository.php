<?php

namespace App\Repository;

use App\Core\Database;
use App\Models\Team;

class TeamRepository
{
    public function findById(int $teamId): ?Team
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare("SELECT teamName, teamleiterID FROM teams WHERE teamID = ?");
        $stmt->bind_param("i", $teamId);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows !== 1) {
            return null;
        }

        $stmt->bind_result($teamName, $teamleiterId);
        $stmt->fetch();

        return Team::fromArray([
            'teamID' => $teamId,
            'teamName' => $teamName,
            'memberCount' => $this->getMemberCount($teamId),
            'teamleiterID' => $teamleiterId,
        ]);
    }

    public function getIdByName(string $teamName): ?int
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare("SELECT teamID FROM teams WHERE teamName = ?");
        $stmt->bind_param("s", $teamName);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows !== 1) {
            return null;
        }

        $stmt->bind_result($teamId);
        $stmt->fetch();

        return $teamId;
    }

    public function create(string $teamName, int $teamleiterId): int
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare("INSERT INTO teams (teamName, teamleiterID) VALUES (?, ?)");
        $stmt->bind_param("si", $teamName, $teamleiterId);

        if (!$stmt->execute()) {
            throw new \RuntimeException("Team konnte nicht erstellt werden");
        }

        return $conn->insert_id;
    }

    public function updateName(int $teamId, string $name): bool
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare("UPDATE teams SET teamName = ? WHERE teamID = ?");
        $stmt->bind_param("si", $name, $teamId);

        return $stmt->execute();
    }

    public function delete(int $teamId): bool
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare("DELETE FROM teams WHERE teamID = ?");
        $stmt->bind_param("i", $teamId);

        return $stmt->execute();
    }

    public function getMemberCount(int $teamId): int
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE teamID = ?");
        $stmt->bind_param("i", $teamId);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();

        return $count;
    }

    public function deleteIfEmpty(int $teamId): bool
    {
        if ($this->getMemberCount($teamId) === 0) {
            return $this->delete($teamId);
        }
        return false;
    }

    public function findAllWithStats(): array
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare(
            "SELECT teams.teamID, teams.teamName,
                    COALESCE(SUM(tours.distance), 0) AS totalDistance,
                    COUNT(DISTINCT users.id) AS memberCount,
                    teams.teamleiterID
             FROM users
             LEFT JOIN tours ON users.id = tours.userID
             INNER JOIN teams ON users.teamID = teams.teamID
             GROUP BY teams.teamID, teams.teamName
             ORDER BY totalDistance DESC"
        );
        $stmt->execute();
        $result = $stmt->get_result();

        $teams = [];
        while ($row = $result->fetch_assoc()) {
            $teams[] = Team::fromArray($row);
        }

        return $teams;
    }
}
