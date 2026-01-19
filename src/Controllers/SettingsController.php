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
            'error' => $error,
            'success' => $success
        ]);
    }
}
