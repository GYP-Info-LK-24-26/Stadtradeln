<?php

namespace App\Models;

class Tour
{
    public function __construct(
        public int $id = 0,
        public int $userId = 0,
        public string $date = '',
        public float $distance = 0
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['tourID'] ?? 0,
            $data['userID'] ?? 0,
            $data['date'] ?? '',
            $data['distance'] ?? 0
        );
    }

    public function getFormattedDate(): string
    {
        $timestamp = strtotime($this->date);
        return $timestamp === false ? $this->date : date('d.m.Y', $timestamp);
    }
}
