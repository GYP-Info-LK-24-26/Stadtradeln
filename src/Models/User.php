<?php

namespace App\Models;

use App\Core\Pseudonym;

class User
{
    /** Maximale Länge des Klarnamens (Zeichen). */
    public const NAME_MAX_LENGTH = 50;

    /** Klarname nur für Auswertung und Ehrung; null, wenn er gelöscht wurde. Nie anzeigen. */
    public ?string $name = null;
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

        $user->name = $data['name'] ?? null;
        $user->email = $data['email'] ?? null;
        $user->password = $data['passHash'] ?? null;
        $user->teamName = $data['teamName'] ?? null;

        return $user;
    }

    /** Fehlermeldung oder null, wenn der (getrimmte) Name gültig ist. */
    public static function validateName(string $name): ?string
    {
        if ($name === '') {
            return 'Du musst deinen Namen eingeben';
        }
        if (mb_strlen($name) > self::NAME_MAX_LENGTH) {
            return 'Der Name darf höchstens ' . self::NAME_MAX_LENGTH . ' Zeichen lang sein';
        }

        return null;
    }

    /** Anzeigename ist das Pseudonym; der Klarname wird nirgends angezeigt. */
    public function getDisplayName(): string
    {
        return Pseudonym::forUser($this->id);
    }
}
