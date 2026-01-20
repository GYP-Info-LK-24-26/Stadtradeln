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

        $userRepo = new UserRepository();
        $memberCount = count($userRepo->findByTeam($teamId));

        return new Team($teamId, $teamName, $memberCount, $teamleiterId);
    }

    public function findByName(string $teamName): ?Team
    {
        $teamId = $this->getIdByName($teamName);
        
        if ($teamId === null) {
            return null;
        }

        return $this->findById($teamId);
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

    public function getNameById(int $teamId): ?string
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare("SELECT teamName FROM teams WHERE teamID = ?");
        $stmt->bind_param("i", $teamId);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows !== 1) {
            return null;
        }

        $stmt->bind_result($teamName);
        $stmt->fetch();

        return $teamName;
    }

    public function exists(string $teamName): bool
    {
        return $this->getIdByName($teamName) !== null;
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

    public function findAll(): array
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare("SELECT teamID, teamName, teamleiterID FROM teams");
        $stmt->execute();
        $result = $stmt->get_result();

        $userRepo = new UserRepository();
        $teams = [];

        while ($row = $result->fetch_assoc()) {
            $memberCount = count($userRepo->findByTeam($row['teamID']));
            $teams[] = new Team(
                $row['teamID'],
                $row['teamName'],
                $memberCount,
                $row['teamleiterID']
            );
        }

        return $teams;
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
            $team = new Team(
                $row['teamID'],
                $row['teamName'],
                $row['memberCount'],
                $row['teamleiterID']
            );
            $team->totalDistance = $row['totalDistance'];
            $teams[] = $team;
        }

        return $teams;
    }
}
