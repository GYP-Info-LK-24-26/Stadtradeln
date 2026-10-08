<?php

namespace App\Core;

/**
 * CSRF-Schutz per Token: ein zufälliges Token pro Sitzung, das jedes
 * POST-Formular als verstecktes Feld mitschickt (Csrf::field()). Der Router
 * lehnt POST-Anfragen ohne gültiges Token ab.
 */
class Csrf
{
    public const FIELD = 'csrf_token';

    /** Token der aktuellen Sitzung; wird beim ersten Aufruf erzeugt. */
    public static function token(): string
    {
        Session::start();

        if (!is_string($_SESSION['csrf_token'] ?? null)) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    /** Verstecktes Formularfeld mit dem Token – gehört in jedes POST-Formular. */
    public static function field(): string
    {
        return '<input type="hidden" name="' . self::FIELD . '" value="' . self::token() . '">';
    }

    public static function isValid(string $token): bool
    {
        Session::start();
        $expected = $_SESSION['csrf_token'] ?? null;

        return is_string($expected) && $token !== '' && hash_equals($expected, $token);
    }
}
