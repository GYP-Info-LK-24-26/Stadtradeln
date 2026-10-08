<?php

namespace App\Core;

use App\Repository\UserRepository;

class Session
{
    private const MAX_INACTIVE_TIME = 1800; // 30 minutes

    /** Ob Name und Team in diesem Request schon aus der Datenbank geladen wurden. */
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
            return RememberMe::restore();
        }

        if (isset($_SESSION["last_activity"]) &&
            $_SESSION["last_activity"] + self::MAX_INACTIVE_TIME < time()) {
            self::logout();
            return RememberMe::restore();
        }

        if (!self::refresh()) {
            self::logout();
            return false;
        }

        $_SESSION["last_activity"] = time();
        return true;
    }

    /**
     * Name und Team einmal pro Request aus der Datenbank übernehmen, damit
     * Änderungen von anderen Geräten (z. B. Team verlassen) sofort gelten.
     * Gibt false zurück, wenn der Account nicht mehr existiert.
     */
    private static function refresh(): bool
    {
        if (self::$refreshed) {
            return true;
        }

        $user = (new UserRepository())->findById((int) $_SESSION["id"]);
        if ($user === null) {
            return false;
        }

        $_SESSION["name"] = $user->name;
        $_SESSION["teamID"] = $user->teamId;
        self::$refreshed = true;
        return true;
    }

    public static function requireLogin(): void
    {
        if (!self::isLoggedIn()) {
            header("Location: /login");
            exit("Nicht eingeloggt.");
        }
    }

    public static function login(int $userId, string $name, ?int $teamId): void
    {
        self::start();
        if (!headers_sent()) {
            session_regenerate_id(true);
        }
        $_SESSION["loggedin"] = true;
        $_SESSION["id"] = $userId;
        $_SESSION["name"] = $name;
        $_SESSION["teamID"] = $teamId;
        $_SESSION["last_activity"] = time();
        self::$refreshed = true;
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

    public static function getDisplayName(): ?string
    {
        return $_SESSION["name"] ?? null;
    }

    public static function setName(string $name): void
    {
        $_SESSION["name"] = $name;
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
