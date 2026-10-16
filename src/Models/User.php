<?php

namespace App\Models;

use App\Core\Pseudonym;

class User
{
    public ?string $email = null;
    public ?string $password = null;
    public ?string $teamName = null;

    public function __construct(
        public int $id = 0,
        public ?int $teamId = null,
        public float $totalDistance = 0
    ) {}

    public static function fromArray(array $data): self
    {
        $user = new self(
            $data['id'] ?? 0,
            $data['teamID'] ?? null,
            (float) ($data['totalDistance'] ?? 0)
        );

        $user->email = $data['email'] ?? null;
        $user->password = $data['passHash'] ?? null;
        $user->teamName = $data['teamName'] ?? null;

        return $user;
    }

    /** Anzeigename ist das Pseudonym; Klarnamen werden nicht gespeichert. */
    public function getDisplayName(): string
    {
        return Pseudonym::forUser($this->id);
    }
}
