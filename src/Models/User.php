<?php

namespace App\Models;

class User
{
    public int $id;
    public string $name;
    public ?int $teamId;
    public float $totalDistance;

    // Extended properties
    public ?string $email = null;
    public ?string $password = null;

    public function __construct(
        int $id = 0,
        string $name = '',
        ?int $teamId = null,
        float $totalDistance = 0
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->teamId = $teamId;
        $this->totalDistance = $totalDistance;
    }

    public function getDisplayName(): string
    {
        return $this->name;
    }

    public static function fromArray(array $data): self
    {
        $user = new self(
            $data['id'] ?? 0,
            $data['name'] ?? '',
            $data['teamID'] ?? $data['teamId'] ?? null,
            $data['totalDistance'] ?? 0
        );

        $user->email = $data['email'] ?? null;

        return $user;
    }
}
