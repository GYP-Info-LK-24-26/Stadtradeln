<?php

namespace App\Core;

use App\Repository\UserRepository;

class Session
{
    private const MAX_INACTIVE_TIME = 1800; // 30 minutes

    /** Ob Team und Namensstatus in diesem Request schon aus der Datenbank geladen wurden. */
    private static bool $refreshed = false;

    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function isLoggedIn(): bool
    {
        self::start();

        if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
            return false;
        }

        if (isset($_SESSION["last_activity"]) &&
            $_SESSION["last_activity"] + self::MAX_INACTIVE_TIME < time()) {
            self::logout();
            return false;
        }

        // Account gelöscht oder Passwort geändert: Sitzung beenden
        if (!self::refresh()) {
            self::logout();
            return false;
        }

        $_SESSION["last_activity"] = time();
        return true;
    }

    /**
     * Team und Namensstatus einmal pro Request aus der Datenbank übernehmen, damit
     * Änderungen von anderen Geräten (z. B. Team verlassen) sofort gelten.
     * Gibt false zurück, wenn der Account nicht mehr existiert oder das Passwort
     * seit dem Login geändert wurde (meldet andere Sitzungen ab).
     */
    private static function refresh(): bool
    {
        if (self::$refreshed) {
            return true;
        }

        $user = (new UserRepository())->findById((int) $_SESSION["id"]);
        if ($user === null || !hash_equals($_SESSION["pass"] ?? '', self::passFingerprint($user->password))) {
            return false;
        }

        $_SESSION["teamID"] = $user->teamId;
        $_SESSION["needsName"] = $user->name === null;
        self::$refreshed = true;
        return true;
    }

    /**
     * Accounts ohne Namen (gelöscht oder vor dessen Einführung angelegt) werden zu
     * /name umgeleitet, bis sie ihn angeben; nur diese Route setzt $allowMissingName.
     */
    public static function requireLogin(bool $allowMissingName = false): void
    {
        if (!self::isLoggedIn()) {
            header("Location: /login");
            exit("Nicht eingeloggt.");
        }

        if (!$allowMissingName && self::needsName()) {
            header("Location: /name");
            exit;
        }
    }

    public static function needsName(): bool
    {
        return $_SESSION["needsName"] ?? false;
    }

    public static function setNameProvided(): void
    {
        $_SESSION["needsName"] = false;
    }

    /** $passHash: aktueller Passwort-Hash aus der Datenbank (für refresh()). */
    public static function login(int $userId, ?string $name, ?int $teamId, string $passHash): void
    {
        self::start();
        if (!headers_sent()) {
            session_regenerate_id(true);
        }
        $_SESSION["loggedin"] = true;
        $_SESSION["id"] = $userId;
        $_SESSION["teamID"] = $teamId;
        $_SESSION["needsName"] = $name === null;
        $_SESSION["pass"] = self::passFingerprint($passHash);
        $_SESSION["last_activity"] = time();
        self::$refreshed = true;
    }

    /** Nach eigener Passwortänderung: diese Sitzung bleibt gültig. */
    public static function setPassHash(string $passHash): void
    {
        $_SESSION["pass"] = self::passFingerprint($passHash);
    }

    /**
     * Nur ein Fingerabdruck des Hashes kommt in die Sitzung, damit Sitzungsdateien
     * keinen offline angreifbaren bcrypt-Hash enthalten.
     */
    private static function passFingerprint(string $passHash): string
    {
        return hash('sha256', $passHash);
    }

    public static function logout(): void
    {
        self::start();
        $_SESSION["loggedin"] = false;
        session_unset();
        session_destroy();
    }

    public static function getUserId(): ?int
    {
        return $_SESSION["id"] ?? null;
    }

    /** Pseudonym der angemeldeten Person (der Klarname wird nie angezeigt). */
    public static function getDisplayName(): ?string
    {
        $userId = self::getUserId();

        return $userId === null ? null : Pseudonym::forUser($userId);
    }

    public static function getTeamId(): ?int
    {
        return $_SESSION["teamID"] ?? null;
    }

    public static function setTeamId(?int $teamId): void
    {
        $_SESSION["teamID"] = $teamId;
    }

    public static function setFlash(string $key, string $message): void
    {
        self::start();
        $_SESSION["flash"][$key] = $message;
    }

    public static function getFlash(string $key): ?string
    {
        self::start();
        $message = $_SESSION["flash"][$key] ?? null;
        unset($_SESSION["flash"][$key]);
        return $message;
    }
}
