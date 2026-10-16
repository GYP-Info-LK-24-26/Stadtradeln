<?php

namespace App\Core;

class View
{
    private static string $templatesPath = __DIR__ . '/../../templates/';

    public static function render(string $template, array $data = []): void
    {
        extract($data);
        require self::$templatesPath . $template . '.php';
    }

    /** Asset-URL mit Versions-Parameter, damit Browser & Service Worker Updates sofort laden. */
    public static function asset(string $path): string
    {
        $file = __DIR__ . '/../../public' . $path;
        return $path . (is_file($file) ? '?v=' . filemtime($file) : '');
    }

    /** Zahl im deutschen Format, z. B. 1.332,3 */
    public static function number(float $value, int $decimals = 1): string
    {
        return number_format($value, $decimals, ',', '.');
    }

    /** Initialen (max. 2 Buchstaben) für Avatare. */
    public static function initials(string $name): string
    {
        $parts = preg_split('/[\s\-]+/u', trim($name), -1, PREG_SPLIT_NO_EMPTY) ?: ['?'];
        $first = mb_substr($parts[0], 0, 1);
        $last = count($parts) > 1 ? mb_substr(end($parts), 0, 1) : '';
        return mb_strtoupper($first . $last);
    }

    /** Stabiler Farbton (0–359) pro Name, damit Avatare unterscheidbar sind. */
    public static function hue(string $name): int
    {
        return crc32($name) % 360;
    }

    /** Avatar-Element mit Initialen. */
    public static function avatar(string $name, string $size = ''): string
    {
        return sprintf(
            '<span class="avatar%s" style="--hue: %d" aria-hidden="true">%s</span>',
            $size !== '' ? ' avatar-' . $size : '',
            self::hue($name),
            htmlspecialchars(self::initials($name))
        );
    }

    /** Deutsches Datum, z. B. "Donnerstag, 2. Oktober". */
    public static function longDate(\DateTimeInterface $date): string
    {
        $weekdays = ['Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag', 'Sonntag'];
        $months = ['Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli',
                   'August', 'September', 'Oktober', 'November', 'Dezember'];
        return $weekdays[(int)$date->format('N') - 1] . ', ' . $date->format('j') . '. '
            . $months[(int)$date->format('n') - 1];
    }
}
