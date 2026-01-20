<?php

namespace App\Controllers;

use App\Core\Session;
use App\Core\View;
use App\Repository\UserRepository;

class SettingsController
{
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    public function index(): void
    {
        Session::requireLogin();

        $userId = Session::getUserId();
        $user = $this->userRepository->findById($userId);

        View::render('pages/settings', [
            'username' => $user->username,
            'email' => $user->email,
            'firstName' => $user->firstName ?? '',
            'lastName' => $user->lastName ?? '',
            'error' => null,
            'success' => null
        ]);
    }

    public function updatePassword(): void
    {
        Session::requireLogin();

        $userId = Session::getUserId();
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        $user = $this->userRepository->findById($userId);
        $error = null;
        $success = null;

        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            $error = 'Bitte alle Felder ausfüllen.';
        } elseif (!password_verify($currentPassword . $user->email, $user->password)) {
            $error = 'Aktuelles Passwort ist falsch.';
        } elseif ($newPassword !== $confirmPassword) {
            $error = 'Neue Passwörter stimmen nicht überein.';
        } elseif (strlen($newPassword) < 6) {
            $error = 'Neues Passwort muss mindestens 6 Zeichen lang sein.';
        } else {
            $hashedPassword = password_hash($newPassword . $user->email, PASSWORD_DEFAULT);
            $this->userRepository->updatePassword($userId, $hashedPassword);
            $success = 'Passwort erfolgreich geändert.';
        }

        View::render('pages/settings', [
            'username' => $user->username,
            'email' => $user->email,
            'firstName' => $user->firstName ?? '',
            'lastName' => $user->lastName ?? '',
            'error' => $error,
            'success' => $success
        ]);
    }

    public function updateName(): void
    {
        Session::requireLogin();

        $userId = Session::getUserId();
        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');

        $user = $this->userRepository->findById($userId);

        $this->userRepository->updateName($userId, $firstName, $lastName);

        View::render('pages/settings', [
            'username' => $user->username,
            'email' => $user->email,
            'firstName' => $firstName,
            'lastName' => $lastName,
            'error' => null,
            'success' => 'Name erfolgreich geändert.'
        ]);
    }

    public function updateUsername(): void
    {
        Session::requireLogin();

        $userId = Session::getUserId();
        $newUsername = trim($_POST['username'] ?? '');

        $user = $this->userRepository->findById($userId);
        $error = null;
        $success = null;

        if (empty($newUsername)) {
            $error = 'Benutzername darf nicht leer sein.';
        } elseif (strlen($newUsername) < 3) {
            $error = 'Benutzername muss mindestens 3 Zeichen lang sein.';
        } elseif ($newUsername !== $user->username && $this->userRepository->usernameExists($newUsername)) {
            $error = 'Dieser Benutzername ist bereits vergeben.';
        } else {
            $this->userRepository->updateUsername($userId, $newUsername);
            Session::setUsername($newUsername);
            $user->username = $newUsername;
            $success = 'Benutzername erfolgreich geändert.';
        }

        View::render('pages/settings', [
            'username' => $user->username,
            'email' => $user->email,
            'firstName' => $user->firstName ?? '',
            'lastName' => $user->lastName ?? '',
            'error' => $error,
            'success' => $success
        ]);
    }

    public function updateEmail(): void
    {
        Session::requireLogin();

        $userId = Session::getUserId();
        $newEmail = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $user = $this->userRepository->findById($userId);
        $error = null;
        $success = null;

        if (empty($newEmail)) {
            $error = 'E-Mail-Adresse darf nicht leer sein.';
        } elseif (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
            $error = 'Bitte eine gültige E-Mail-Adresse eingeben.';
        } elseif (empty($password)) {
            $error = 'Bitte Passwort zur Bestätigung eingeben.';
        } elseif (!password_verify($password . $user->email, $user->password)) {
            $error = 'Passwort zur Bestätigung ist falsch.';
        } elseif ($newEmail !== $user->email && $this->userRepository->emailExists($newEmail)) {
            $error = 'Diese E-Mail-Adresse ist bereits registriert.';
        } else {
            // Rehash password with new email as salt
            $newPasswordHash = password_hash($password . $newEmail, PASSWORD_DEFAULT);
            $this->userRepository->updateEmail($userId, $newEmail, $newPasswordHash);
            $user->email = $newEmail;
            $success = 'E-Mail-Adresse erfolgreich geändert.';
        }

        View::render('pages/settings', [
            'username' => $user->username,
            'email' => $user->email,
            'firstName' => $user->firstName ?? '',
            'lastName' => $user->lastName ?? '',
            'error' => $error,
            'success' => $success
        ]);
    }
}
