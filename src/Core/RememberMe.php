<?php

namespace App\Core;

use App\Repository\RememberTokenRepository;
use App\Repository\UserRepository;

/**
 * "Angemeldet bleiben" per Selector/Validator-Cookie.
 *
 * Cookie: "<selector>:<validator>". Die Datenbank kennt den Selector im Klartext
 * (zum Nachschlagen) und nur den SHA-256-Hash des Validators. Bei jeder Nutzung
 * wird das Token rotiert.
 */
class RememberMe
{
    private const COOKIE_NAME = 'remember';
    private const LIFETIME_DAYS = 30;

    /** Neues Token für den Nutzer ausstellen und als Cookie setzen. */
    public static function issue(int $userId): void
    {
        $selector = bin2hex(random_bytes(12));
        $validator = bin2hex(random_bytes(32));
        $expiresAt = new \DateTime('+' . self::LIFETIME_DAYS . ' days');

        $repository = new RememberTokenRepository();
        $repository->deleteExpired();
        $repository->create($userId, $selector, hash('sha256', $validator), $expiresAt);

        self::setCookie($selector . ':' . $validator, $expiresAt->getTimestamp());
    }

    /**
     * Sitzung aus dem Cookie wiederherstellen. Gibt true zurück, wenn der Nutzer
     * danach eingeloggt ist.
     */
    public static function restore(): bool
    {
        $parsed = self::parseCookie();
        if ($parsed === null) {
            return false;
        }
        [$selector, $validator] = $parsed;

        $repository = new RememberTokenRepository();
        $token = $repository->findBySelector($selector);

        // Unbekannter Selector: Cookie nicht löschen – bei parallelen Requests kann
        // ein anderer Request das Token gerade rotiert und neu gesetzt haben.
        if ($token === null) {
            return false;
        }

        if (!hash_equals($token['tokenHash'], hash('sha256', $validator))) {
            self::clearCookie();
            return false;
        }

        $user = (new UserRepository())->findById((int) $token['userID']);
        $repository->deleteBySelector($selector);

        if ($user === null) {
            self::clearCookie();
            return false;
        }

        Session::login($user->id, $user->name, $user->teamId, $user->password);
        self::issue($user->id);
        return true;
    }

    /** Token dieses Geräts löschen (Logout). */
    public static function forget(): void
    {
        $parsed = self::parseCookie();
        if ($parsed !== null) {
            (new RememberTokenRepository())->deleteBySelector($parsed[0]);
        }
        self::clearCookie();
    }

    /** Alle Tokens des Nutzers löschen (z. B. nach Passwortänderung). */
    public static function forgetAll(int $userId): void
    {
        (new RememberTokenRepository())->deleteByUserId($userId);
    }

    public static function hasCookie(): bool
    {
        return self::parseCookie() !== null;
    }

    /** @return array{0: string, 1: string}|null */
    private static function parseCookie(): ?array
    {
        $value = $_COOKIE[self::COOKIE_NAME] ?? '';
        if (!is_string($value) || !preg_match('/^([0-9a-f]{24}):([0-9a-f]{64})$/', $value, $m)) {
            return null;
        }
        return [$m[1], $m[2]];
    }

    private static function clearCookie(): void
    {
        unset($_COOKIE[self::COOKIE_NAME]);
        self::setCookie('', time() - 3600);
    }

    private static function setCookie(string $value, int $expires): void
    {
        if (headers_sent()) {
            return;
        }

        setcookie(self::COOKIE_NAME, $value, [
            'expires' => $expires,
            'path' => '/',
            'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        if ($value !== '') {
            $_COOKIE[self::COOKIE_NAME] = $value;
        }
    }
}
