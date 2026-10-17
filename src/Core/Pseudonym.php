<?php

namespace App\Core;

/**
 * Stabiles Pseudonym (Name eines bayerischen Bergs) für Personen, deren Name verborgen
 * bleibt; die Liste steht in config/pseudonyms.php. Ergibt sich allein aus users.id,
 * so dass jeder Name einmal vergeben ist, bevor sich einer wiederholt. Lücken in den IDs
 * (config/pseudonym-gaps.php) werden übersprungen, damit dort keine Namen verloren gehen.
 */
class Pseudonym
{
    private const CONFIG_FILE = __DIR__ . '/../../config/pseudonyms.php';
    private const GAPS_FILE = __DIR__ . '/../../config/pseudonym-gaps.php';

    /** @var string[]|null */
    private static ?array $names = null;

    /** @var array<int, array{int, int}>|null aufsteigend sortiert, ohne Überschneidungen */
    private static ?array $gaps = null;

    public static function forUser(int $userId): string
    {
        self::$names ??= array_values(require self::CONFIG_FILE);

        $index = max(0, $userId - 1 - self::skippedBefore($userId));
        $count = count(self::$names);
        $name = self::$names[$index % $count];
        $round = intdiv($index, $count);

        return $round === 0 ? $name : $name . ' ' . ($round + 1);
    }

    /** Anzahl der übersprungenen IDs kleiner als $userId. */
    private static function skippedBefore(int $userId): int
    {
        $skipped = 0;

        foreach (self::gaps() as [$from, $to]) {
            if ($from >= $userId) {
                break;
            }
            $skipped += min($to, $userId - 1) - $from + 1;
        }

        return $skipped;
    }

    /** @return array<int, array{int, int}> */
    private static function gaps(): array
    {
        if (self::$gaps !== null) {
            return self::$gaps;
        }

        $config = is_file(self::GAPS_FILE) ? require self::GAPS_FILE : [];
        $ranges = [];

        foreach (is_array($config) ? $config : [] as $range) {
            if (!is_array($range) || count($range) !== 2
                || !is_int($range[0] ?? null) || !is_int($range[1] ?? null) || $range[0] > $range[1]) {
                error_log('Ungültiger Bereich in config/pseudonym-gaps.php: ' . json_encode($range));
                continue;
            }
            $ranges[] = [$range[0], $range[1]];
        }

        sort($ranges);

        // Überschneidende oder angrenzende Bereiche zusammenfassen, damit keine ID doppelt zählt
        $merged = [];
        foreach ($ranges as [$from, $to]) {
            $last = count($merged) - 1;
            if ($last >= 0 && $from <= $merged[$last][1] + 1) {
                $merged[$last][1] = max($merged[$last][1], $to);
            } else {
                $merged[] = [$from, $to];
            }
        }

        return self::$gaps = $merged;
    }
}
