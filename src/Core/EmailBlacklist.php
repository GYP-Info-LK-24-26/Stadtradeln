<?php

namespace App\Core;

/** Gesperrte E-Mail-Adressen und Domains; die Liste steht in config/email-blacklist.php. */
class EmailBlacklist
{
    private const CONFIG_FILE = __DIR__ . '/../../config/email-blacklist.php';

    public const ERROR = 'Diese E-Mail-Adresse kann nicht verwendet werden.';

    public static function isBlocked(string $email): bool
    {
        $email = mb_strtolower(trim($email));

        foreach (self::entries() as $entry) {
            $entry = mb_strtolower(trim($entry));

            if ($entry === '') {
                continue;
            }

            if (str_contains($entry, '@')) {
                if (str_ends_with($email, $entry)) {
                    return true;
                }
            } elseif (str_ends_with($email, '@' . $entry) || str_ends_with($email, '.' . $entry)) {
                // Nur ganze Domain-Teile: "spam.de" sperrt nicht "nospam.de"
                return true;
            }
        }

        return false;
    }

    /** @return string[] */
    private static function entries(): array
    {
        if (!is_file(self::CONFIG_FILE)) {
            return [];
        }

        $entries = require self::CONFIG_FILE;

        return is_array($entries) ? array_filter($entries, 'is_string') : [];
    }
}
