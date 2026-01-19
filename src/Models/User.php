<?php

namespace App\Models;

class User
{
    public int $id;
    public string $username;
    public int $teamId;
    public float $totalDistance;
    
    // Extended properties
    public ?string $firstName = null;
    public ?string $lastName = null;
    public ?string $email = null;
    public ?string $password = null;

    public function __construct(
        int $id = 0,
        string $username = '',
        int $teamId = -1,
        float $totalDistance = 0
    ) {
        $this->id = $id;
        $this->username = $username;
        $this->teamId = $teamId;
        $this->totalDistance = $totalDistance;
    }

    public static function fromArray(array $data): self
    {
        $user = new self(
            $data['id'] ?? 0,
            $data['username'] ?? '',
            $data['teamID'] ?? $data['teamId'] ?? -1,
            $data['totalDistance'] ?? 0
        );

        $user->firstName = $data['firstName'] ?? null;
        $user->lastName = $data['lastName'] ?? null;
        $user->email = $data['email'] ?? null;

        return $user;
    }
}
