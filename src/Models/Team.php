<?php

namespace App\Models;

class Team
{
    public int $id;
    public string $name;
    public int $memberCount;
    public int $teamleiterId;
    public float $totalDistance;
    public int $totalTours;

    public function __construct(
        int $id = 0,
        string $name = '',
        int $memberCount = 0,
        int $teamleiterId = 0
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->memberCount = $memberCount;
        $this->teamleiterId = $teamleiterId;
        $this->totalDistance = 0;
        $this->totalTours = 0;
    }

    public static function fromArray(array $data): self
    {
        $team = new self(
            $data['teamID'] ?? $data['id'] ?? 0,
            $data['teamName'] ?? $data['name'] ?? '',
            $data['memberCount'] ?? $data['teamMemberCount'] ?? 0,
            $data['teamleiterID'] ?? $data['teamleiterId'] ?? 0
        );

        $team->totalDistance = $data['totalDistance'] ?? $data['teamTotalDistance'] ?? 0;
        $team->totalTours = $data['totalTours'] ?? $data['teamTotalTours'] ?? 0;

        return $team;
    }

    public function getAverageDistancePerTour(): float
    {
        if ($this->totalTours === 0) {
            return 0.0;
        }
        return $this->totalDistance / $this->totalTours;
    }
}
