<?php

namespace App\Controllers;

use App\Core\Session;
use App\Core\View;
use App\Models\User;
use App\Repository\UserRepository;

class AuthController
{
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    public function showLogin(): void
    {
        Session::start();

        if (Session::isLoggedIn()) {
            header("Location: /dashboard");
            exit;
        }

        $error = '';
        View::render('pages/login', ['error' => $error]);
    }

    public function login(): void
    {
        Session::start();

        $error = '';
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email)) {
            $error = 'Du musst eine E-Mail eingeben';
        } elseif (empty($password)) {
            $error = 'Du musst ein Passwort eingeben';
        }

        if (empty($error)) {
            $user = $this->userRepository->findByEmail($email);

            if ($user === null) {
                $error = 'Dieser Account existiert nicht';
            } elseif (!$this->userRepository->verifyPassword($user, $password)) {
                $error = 'Falsches Passwort';
            } else {
                Session::login($user->id, $user->name, $user->teamId);
                $this->userRepository->updateLastLogin($user->id);

                $redirect = $_GET['redirect'] ?? '/dashboard';
                header("Location: " . $redirect);
                exit;
            }
        }

        View::render('pages/login', ['error' => $error, 'email' => $email]);
    }

    public function showRegister(): void
    {
        Session::start();

        if (Session::isLoggedIn()) {
            header("Location: /dashboard");
            exit;
        }

        View::render('pages/register', ['error' => '']);
    }

    public function register(): void
    {
        Session::start();

        $error = '';
        $data = array_map('trim', $_POST);

        // Validation
        if (empty($data['name'])) {
            $error = 'Du musst einen Namen eingeben';
        } elseif (empty($data['email'])) {
            $error = 'Du musst eine E-Mail eingeben';
        } elseif (empty($data['password'])) {
            $error = 'Du musst ein Passwort eingeben';
        } elseif ($data['password'] !== ($data['confirm_password'] ?? '')) {
            $error = 'Passwörter stimmen nicht überein';
        }

        if (empty($error)) {
            if ($this->userRepository->emailExists($data['email'])) {
                $error = 'Diese E-Mail ist bereits registriert';
            }
        }

        if (empty($error)) {
            $user = new User();
            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->password = $data['password'];

            try {
                $userId = $this->userRepository->create($user);
                Session::login($userId, $user->name, null);
                $this->userRepository->updateLastLogin($userId);

                $redirect = $_GET['redirect'] ?? '/dashboard';
                header("Location: " . $redirect);
                exit;
            } catch (\Exception $e) {
                $error = 'Interner Fehler';
            }
        }

        View::render('pages/register', ['error' => $error, 'data' => $data]);
    }

    public function logout(): void
    {
        Session::logout();
        header("Location: /");
        exit;
    }
}
