<?php

namespace App\Controllers;

use App\Core\Session;
use App\Core\View;
use App\Models\User;
use App\Repository\UserRepository;
use App\Repository\PasswordResetRepository;
use App\Repository\RateLimitRepository;

class AuthController
{
    private UserRepository $userRepository;
    private PasswordResetRepository $passwordResetRepository;
    private RateLimitRepository $rateLimitRepository;

    private const RESET_FROM_EMAIL = 'no-reply@stadtradeln.gymnasium-penzberg.de';
    private const RESET_FROM_NAME = 'Stadtradeln';
    private const RESET_EXPIRY_HOURS = 1;
    private const RESET_COOLDOWN_MINUTES = 10;
    private const RESET_IP_MAX_ATTEMPTS = 5;
    private const RESET_IP_WINDOW_MINUTES = 60;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
        $this->passwordResetRepository = new PasswordResetRepository();
        $this->rateLimitRepository = new RateLimitRepository();
    }

    public function showLogin(): void
    {
        if (Session::isLoggedIn()) {
            header("Location: /dashboard");
            exit;
        }

        View::render('pages/login', ['error' => '']);
    }

    public function login(): void
    {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $error = '';

        if (empty($email)) {
            $error = 'Du musst eine E-Mail eingeben';
        } elseif (empty($password)) {
            $error = 'Du musst ein Passwort eingeben';
        } else {
            $user = $this->userRepository->findByEmail($email);

            if ($user === null || !password_verify($password, $user->password)) {
                $error = 'E-Mail oder Passwort ist falsch';
            } else {
                Session::login($user->id, $user->name, $user->teamId);
                $this->userRepository->updateLastLogin($user->id);
                header("Location: " . ($_GET['redirect'] ?? '/dashboard'));
                exit;
            }
        }

        View::render('pages/login', ['error' => $error, 'email' => $email]);
    }

    public function showRegister(): void
    {
        if (Session::isLoggedIn()) {
            header("Location: /dashboard");
            exit;
        }

        View::render('pages/register', ['error' => '']);
    }

    public function register(): void
    {
        $data = array_map('trim', $_POST);
        $error = '';

        if (empty($data['name'])) {
            $error = 'Du musst einen Namen eingeben';
        } elseif (empty($data['email'])) {
            $error = 'Du musst eine E-Mail eingeben';
        } elseif (empty($data['password'])) {
            $error = 'Du musst ein Passwort eingeben';
        } elseif ($data['password'] !== ($data['confirm_password'] ?? '')) {
            $error = 'Passwörter stimmen nicht überein';
        } elseif ($this->userRepository->emailExists($data['email'])) {
            $error = 'Diese E-Mail ist bereits registriert';
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
                header("Location: " . ($_GET['redirect'] ?? '/dashboard'));
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

    public function showForgotPassword(): void
    {
        if (Session::isLoggedIn()) {
            header("Location: /dashboard");
            exit;
        }

        View::render('pages/forgot-password', ['error' => '', 'success' => '']);
    }

    public function forgotPassword(): void
    {
        $email = trim($_POST['email'] ?? '');
        $error = '';
        $success = '';
        $clientIp = RateLimitRepository::getClientIp();

        if ($this->rateLimitRepository->isRateLimited(
            $clientIp, 'password_reset',
            self::RESET_IP_MAX_ATTEMPTS, self::RESET_IP_WINDOW_MINUTES
        )) {
            $error = 'Zu viele Anfragen. Bitte versuche es später erneut.';
        } elseif (empty($email)) {
            $error = 'Bitte gib deine E-Mail-Adresse ein.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Bitte gib eine gültige E-Mail-Adresse ein.';
        } else {
            $success = 'Falls ein Account mit dieser E-Mail existiert, wurde ein Link zum Zurücksetzen des Passworts gesendet.';
            $this->rateLimitRepository->record($clientIp, 'password_reset');

            $user = $this->userRepository->findByEmail($email);
            if ($user !== null && !$this->passwordResetRepository->hasRecentReset($user->id, self::RESET_COOLDOWN_MINUTES)) {
                $token = PasswordResetRepository::generateToken();
                $expiresAt = new \DateTime('+' . self::RESET_EXPIRY_HOURS . ' hour');
                $this->passwordResetRepository->create($user->id, $token, $expiresAt);
                $this->sendResetEmail($user->email, $user->name, $token);
            }
        }

        View::render('pages/forgot-password', ['error' => $error, 'success' => $success, 'email' => $email]);
    }

    public function showResetPassword(): void
    {
        $token = $_GET['token'] ?? '';

        if (empty($token) || !$this->passwordResetRepository->isValid($token)) {
            View::render('pages/reset-password', ['error' => 'Dieser Link ist ungültig oder abgelaufen.', 'token' => '', 'valid' => false]);
            return;
        }

        View::render('pages/reset-password', ['error' => '', 'token' => $token, 'valid' => true]);
    }

    public function resetPassword(): void
    {
        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($token) || !$this->passwordResetRepository->isValid($token)) {
            View::render('pages/reset-password', ['error' => 'Dieser Link ist ungültig oder abgelaufen.', 'token' => '', 'valid' => false]);
            return;
        }

        $error = '';
        if (empty($password)) {
            $error = 'Bitte gib ein neues Passwort ein.';
        } elseif (strlen($password) < 6) {
            $error = 'Das Passwort muss mindestens 6 Zeichen lang sein.';
        } elseif ($password !== $confirmPassword) {
            $error = 'Die Passwörter stimmen nicht überein.';
        }

        if (!empty($error)) {
            View::render('pages/reset-password', ['error' => $error, 'token' => $token, 'valid' => true]);
            return;
        }

        $reset = $this->passwordResetRepository->findByToken($token);
        $this->userRepository->updatePassword($reset['userID'], password_hash($password, PASSWORD_DEFAULT));
        $this->passwordResetRepository->deleteByUserId($reset['userID']);

        header("Location: /login?reset=success");
        exit;
    }

    private function sendResetEmail(string $email, string $name, string $token): bool
    {
        $resetUrl = $this->getBaseUrl() . '/reset-password?token=' . $token;

        $message = "Hallo {$name},\n\n"
            . "du hast angefordert, dein Passwort zurückzusetzen.\n\n"
            . "Klicke auf folgenden Link, um ein neues Passwort zu setzen:\n"
            . "{$resetUrl}\n\n"
            . "Der Link ist " . self::RESET_EXPIRY_HOURS . " Stunde gültig.\n\n"
            . "Falls du diese Anfrage nicht gestellt hast, kannst du diese E-Mail ignorieren.\n\n"
            . "Viele Grüße\nDein Stadtradeln-Team";

        return mail($email, 'Passwort zurücksetzen - Stadtradeln', $message, [
            'From' => self::RESET_FROM_NAME . ' <' . self::RESET_FROM_EMAIL . '>',
            'Reply-To' => self::RESET_FROM_EMAIL,
            'Content-Type' => 'text/plain; charset=UTF-8'
        ]);
    }

    private function getBaseUrl(): string
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        return $protocol . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
    }
}
