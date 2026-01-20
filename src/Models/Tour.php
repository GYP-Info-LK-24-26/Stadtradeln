<?php

namespace App\Models;

class Tour
{
    public int $id;
    public int $userId;
    public string $date;
    public float $distance;

    public function __construct(
        int $id = 0,
        int $userId = 0,
        string $date = '',
        float $distance = 0
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->date = $date;
        $this->distance = $distance;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['tourID'] ?? $data['id'] ?? 0,
            $data['userID'] ?? $data['userId'] ?? 0,
            $data['date'] ?? '',
            $data['distance'] ?? 0
        );
    }

    public function getFormattedDate(): string
    {
        $timestamp = strtotime($this->date);
        if ($timestamp === false) {
            return $this->date;
        }
        return date('d.m.Y', $timestamp);
    }
}
