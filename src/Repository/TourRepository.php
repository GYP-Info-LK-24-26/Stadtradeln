<?php

namespace App\Repository;

use App\Core\Database;
use App\Models\Tour;

class TourRepository
{
    public function findById(int $tourId): ?Tour
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare("SELECT tourID, userID, distance, date FROM tours WHERE tourID = ?");
        $stmt->bind_param("i", $tourId);
        $stmt->execute();
        $result = $stmt->get_result();

        $row = $result->fetch_assoc();
        if (!$row) {
            return null;
        }

        return new Tour($row['tourID'], $row['userID'], $row['date'], $row['distance']);
    }

    public function findByUser(int $userId): array
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare(
            "SELECT tourID, distance, date FROM tours WHERE userID = ? ORDER BY date DESC"
        );
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        $tours = [];
        while ($row = $result->fetch_assoc()) {
            $tours[] = new Tour(
                $row['tourID'],
                $userId,
                $row['date'],
                $row['distance']
            );
        }

        return $tours;
    }

    public function findByTeam(int $teamId): array
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare(
            "SELECT distance, date, userID, tourID 
             FROM tours 
             INNER JOIN users ON tours.userID = users.id 
             WHERE users.teamID = ?"
        );
        $stmt->bind_param("i", $teamId);
        $stmt->execute();
        $result = $stmt->get_result();

        $tours = [];
        while ($row = $result->fetch_assoc()) {
            $tours[] = new Tour(
                $row['tourID'],
                $row['userID'],
                $row['date'],
                $row['distance']
            );
        }

        return $tours;
    }

    public function create(int $userId, float $distance, string $date): int
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare("INSERT INTO tours (userID, distance, date) VALUES (?, ?, ?)");
        
        // Ensure proper decimal format
        $distance = str_replace(',', '.', (string)$distance);
        
        $stmt->bind_param("ids", $userId, $distance, $date);

        if (!$stmt->execute()) {
            throw new \RuntimeException("Tour konnte nicht erstellt werden");
        }

        return $conn->insert_id;
    }

    public function update(int $tourId, float $distance, string $date): bool
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare("UPDATE tours SET distance = ?, date = ? WHERE tourID = ?");
        $stmt->bind_param("dsi", $distance, $date, $tourId);

        return $stmt->execute();
    }

    public function delete(int $tourId): bool
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare("DELETE FROM tours WHERE tourID = ?");
        $stmt->bind_param("i", $tourId);

        return $stmt->execute();
    }

    public function getTotalDistanceForUser(int $userId): float
    {
        $tours = $this->findByUser($userId);
        return array_sum(array_map(fn($t) => $t->distance, $tours));
    }

    public function getStatsForTeam(int $teamId): array
    {
        $tours = $this->findByTeam($teamId);
        $totalDistance = array_sum(array_map(fn($t) => $t->distance, $tours));
        $totalTours = count($tours);

        return [
            'totalDistance' => $totalDistance,
            'totalTours' => $totalTours,
            'averageDistance' => $totalTours > 0 ? $totalDistance / $totalTours : 0
        ];
    }
}
