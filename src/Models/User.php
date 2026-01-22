<?php

namespace App\Models;

class User
{
    public ?string $email = null;
    public ?string $password = null;

    public function __construct(
        public int $id = 0,
        public string $name = '',
        public ?int $teamId = null,
        public float $totalDistance = 0
    ) {}

    public static function fromArray(array $data): self
    {
        $user = new self(
            $data['id'] ?? 0,
            $data['name'] ?? '',
            $data['teamID'] ?? null,
            (float) ($data['totalDistance'] ?? 0)
        );

        $user->email = $data['email'] ?? null;
        $user->password = $data['passHash'] ?? null;

        return $user;
    }
}
