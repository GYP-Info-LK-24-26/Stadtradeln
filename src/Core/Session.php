<?php

namespace App\Core;

class Session
{
    private const MAX_INACTIVE_TIME = 1800; // 30 minutes

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

        $_SESSION["last_activity"] = time();
        return true;
    }

    public static function requireLogin(): void
    {
        if (!self::isLoggedIn()) {
            $redirect = urlencode($_SERVER["REQUEST_URI"] ?? '/');
            header("Location: /login?redirect=" . $redirect);
            exit("Nicht eingeloggt.");
        }
    }

    public static function login(int $userId, string $username, int $teamId): void
    {
        self::start();
        $_SESSION["loggedin"] = true;
        $_SESSION["id"] = $userId;
        $_SESSION["username"] = $username;
        $_SESSION["teamID"] = $teamId;
        $_SESSION["last_activity"] = time();
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

    public static function getUsername(): ?string
    {
        return $_SESSION["username"] ?? null;
    }

    public static function getTeamId(): int
    {
        return $_SESSION["teamID"] ?? -1;
    }

    public static function setTeamId(int $teamId): void
    {
        $_SESSION["teamID"] = $teamId;
    }
}
