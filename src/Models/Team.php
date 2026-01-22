<?php

namespace App\Models;

class Team
{
    public function __construct(
        public int $id = 0,
        public string $name = '',
        public int $memberCount = 0,
        public int $teamleiterId = 0,
        public float $totalDistance = 0,
        public int $totalTours = 0
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['teamID'] ?? 0,
            $data['teamName'] ?? '',
            $data['memberCount'] ?? 0,
            $data['teamleiterID'] ?? 0,
            (float) ($data['totalDistance'] ?? 0),
            (int) ($data['totalTours'] ?? 0)
        );
    }
}
