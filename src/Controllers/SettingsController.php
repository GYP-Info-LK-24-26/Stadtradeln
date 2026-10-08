<?php

namespace App\Controllers;

use App\Core\RememberMe;
use App\Core\Request;
use App\Core\Session;
use App\Core\View;
use App\Models\User;
use App\Repository\UserRepository;
use App\Repository\RateLimitRepository;

class SettingsController
{
    private UserRepository $userRepository;
    private RateLimitRepository $rateLimitRepository;

    // Falsche Eingaben des aktuellen Passworts, pro Account (E-Mail- und Passwortänderung gemeinsam)
    private const PASSWORD_MAX_ATTEMPTS = 5;
    private const PASSWORD_WINDOW_MINUTES = 15;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
        $this->rateLimitRepository = new RateLimitRepository();
    }

    public function index(): void
    {
        Session::requireLogin();

        $user = $this->userRepository->findById(Session::getUserId());
        $this->render($user);
    }

    public function updatePassword(): void
    {
        Session::requireLogin();

        $userId = Session::getUserId();
        $currentPassword = Request::post('current_password');
        $newPassword = Request::post('new_password');
        $confirmPassword = Request::post('confirm_password');

        $user = $this->userRepository->findById($userId);
        $error = null;
        $success = null;

        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            $error = 'Bitte alle Felder ausfüllen.';
        } elseif ($newPassword !== $confirmPassword) {
            $error = 'Neue Passwörter stimmen nicht überein.';
        } else {
            $error = $this->checkPassword($user, $currentPassword, 'Aktuelles Passwort ist falsch.');
        }

        if ($error === null) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $this->userRepository->updatePassword($userId, $hashedPassword);
            // Andere Geräte abmelden, dieses Gerät bleibt ggf. angemeldet
            $rememberThisDevice = RememberMe::hasCookie();
            RememberMe::forgetAll($userId);
            if ($rememberThisDevice) {
                RememberMe::issue($userId);
            }
            $success = 'Passwort erfolgreich geändert.';
        }

        $this->render($user, 'password', $error, $success);
    }

    public function updateName(): void
    {
        Session::requireLogin();

        $userId = Session::getUserId();
        $name = trim(Request::post('name'));

        $user = $this->userRepository->findById($userId);
        $error = null;
        $success = null;

        if (empty($name)) {
            $error = 'Name darf nicht leer sein.';
        } elseif (mb_strlen($name) > User::NAME_MAX_LENGTH) {
            $error = 'Der Name darf höchstens ' . User::NAME_MAX_LENGTH . ' Zeichen lang sein.';
        } else {
            $this->userRepository->updateName($userId, $name);
            Session::setName($name);
            $user->name = $name;
            $success = 'Name erfolgreich geändert.';
        }

        $this->render($user, 'name', $error, $success);
    }

    public function updateEmail(): void
    {
        Session::requireLogin();

        $userId = Session::getUserId();
        $newEmail = trim(Request::post('email'));
        $password = Request::post('password');

        $user = $this->userRepository->findById($userId);
        $error = null;
        $success = null;

        if (empty($newEmail)) {
            $error = 'E-Mail-Adresse darf nicht leer sein.';
        } elseif (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
            $error = 'Bitte eine gültige E-Mail-Adresse eingeben.';
        } elseif (empty($password)) {
            $error = 'Bitte Passwort zur Bestätigung eingeben.';
        } else {
            $error = $this->checkPassword($user, $password, 'Passwort zur Bestätigung ist falsch.');
            // Erst nach dem Passwort prüfen, damit niemand ohne Passwort fremde Adressen abfragen kann
            if ($error === null && $newEmail !== $user->email && $this->userRepository->emailExists($newEmail)) {
                $error = 'Diese E-Mail-Adresse ist bereits registriert.';
            }
        }

        if ($error === null) {
            $this->userRepository->updateEmail($userId, $newEmail);
            $user->email = $newEmail;
            $success = 'E-Mail-Adresse erfolgreich geändert.';
        }

        $this->render($user, 'email', $error, $success);
    }

    /**
     * Prüft das aktuelle Passwort mit Limit pro Account, damit es sich über eine offene
     * Sitzung (z. B. an einem Schulrechner) nicht durchprobieren lässt.
     * Gibt die Fehlermeldung zurück oder null, wenn das Passwort stimmt.
     */
    private function checkPassword(User $user, string $password, string $wrongMessage): ?string
    {
        $key = RateLimitRepository::userKey($user->id);

        if ($this->rateLimitRepository->isRateLimited(
            $key, 'password_check',
            self::PASSWORD_MAX_ATTEMPTS, self::PASSWORD_WINDOW_MINUTES
        )) {
            return 'Zu viele falsche Passwort-Eingaben. Bitte versuche es in '
                . self::PASSWORD_WINDOW_MINUTES . ' Minuten erneut.';
        }

        if (!password_verify($password, $user->password)) {
            $this->rateLimitRepository->record($key, 'password_check');
            return $wrongMessage;
        }

        return null;
    }

    /** $section ('name' | 'email' | 'password') bestimmt, wo die Meldung erscheint. */
    private function render(User $user, ?string $section = null, ?string $error = null, ?string $success = null): void
    {
        View::render('pages/settings', [
            'email' => $user->email,
            'name' => $user->name,
            'section' => $section,
            'error' => $error,
            'success' => $success,
        ]);
    }
}
