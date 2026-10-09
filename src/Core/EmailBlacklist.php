<?php

namespace App\Core;

/** Gesperrte E-Mail-Adressen als reguläre Ausdrücke; die Liste steht in config/email-blacklist.php. */
class EmailBlacklist
{
    private const CONFIG_FILE = __DIR__ . '/../../config/email-blacklist.php';

    public const ERROR = 'Diese E-Mail-Adresse kann nicht verwendet werden.';

    public static function isBlocked(string $email): bool
    {
        $email = mb_strtolower(trim($email));

        foreach (self::patterns() as $pattern) {
            // @: ein Tippfehler in der Liste soll keine PHP-Warnung auf der Seite erzeugen
            $result = @preg_match($pattern, $email);

            if ($result === 1) {
                return true;
            }
            if ($result === false) {
                error_log('Ungültiger Ausdruck in config/email-blacklist.php: ' . $pattern);
            }
        }

        return false;
    }

    /** @return string[] */
    private static function patterns(): array
    {
        if (!is_file(self::CONFIG_FILE)) {
            return [];
        }

        $patterns = require self::CONFIG_FILE;

        return is_array($patterns) ? array_filter($patterns, 'is_string') : [];
    }
}
