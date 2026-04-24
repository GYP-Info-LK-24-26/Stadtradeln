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
        return $row ? Tour::fromArray($row) : null;
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
            $row['userID'] = $userId;
            $tours[] = Tour::fromArray($row);
        }

        return $tours;
    }

    public function create(int $userId, float $distance, string $date): int
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare("INSERT INTO tours (userID, distance, date) VALUES (?, ?, ?)");

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

    public function getDailyTotalByUser(int $userId, string $date, ?int $excludeTourId = null): float
    {
        $conn = Database::getConnection();

        if ($excludeTourId !== null) {
            $stmt = $conn->prepare(
                "SELECT COALESCE(SUM(distance), 0) AS total
                 FROM tours
                 WHERE userID = ? AND date = ? AND tourID != ?"
            );
            $stmt->bind_param("isi", $userId, $date, $excludeTourId);
        } else {
            $stmt = $conn->prepare(
                "SELECT COALESCE(SUM(distance), 0) AS total
                 FROM tours
                 WHERE userID = ? AND date = ?"
            );
            $stmt->bind_param("is", $userId, $date);
        }

        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return (float) $row['total'];
    }

    public function getStatsForTeam(int $teamId): array
    {
        $conn = Database::getConnection();
        $stmt = $conn->prepare(
            "SELECT COALESCE(SUM(tours.distance), 0) AS totalDistance, COUNT(*) AS totalTours
             FROM tours
             INNER JOIN users ON tours.userID = users.id
             WHERE users.teamID = ?"
        );
        $stmt->bind_param("i", $teamId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        return [
            'totalDistance' => (float) $row['totalDistance'],
            'totalTours' => (int) $row['totalTours'],
        ];
    }
}
