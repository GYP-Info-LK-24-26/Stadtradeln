<?php

namespace App\Core;

/**
 * Typsicherer Zugriff auf Request-Parameter. Fehlende Felder und Arrays
 * (z. B. "name[]=x") ergeben '', damit Controller immer mit Strings arbeiten.
 */
class Request
{
    public static function post(string $key): string
    {
        $value = $_POST[$key] ?? '';
        return is_string($value) ? $value : '';
    }

    public static function get(string $key): string
    {
        $value = $_GET[$key] ?? '';
        return is_string($value) ? $value : '';
    }
}
