<?php

namespace App\Core;

/**
 * Stabiles Pseudonym (Name eines bayerischen Bergs) für Personen, deren Name verborgen
 * bleibt; die Liste steht in config/pseudonyms.php. Ergibt sich allein aus users.id,
 * so dass jeder Name einmal vergeben ist, bevor sich einer wiederholt.
 */
class Pseudonym
{
    private const CONFIG_FILE = __DIR__ . '/../../config/pseudonyms.php';

    /** @var string[]|null */
    private static ?array $names = null;

    public static function forUser(int $userId): string
    {
        self::$names ??= array_values(require self::CONFIG_FILE);

        $index = max(0, $userId - 1);
        $count = count(self::$names);
        $name = self::$names[$index % $count];
        $round = intdiv($index, $count);

        return $round === 0 ? $name : $name . ' ' . ($round + 1);
    }
}
