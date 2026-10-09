<?php

namespace App\Controllers;

use App\Core\EmailBlacklist;
use App\Core\RememberMe;
use App\Core\Request;
use App\Core\Session;
use App\Core\View;
use App\Models\User;
use App\Repository\UserRepository;
use App\Repository\PasswordResetRepository;
use App\Repository\PendingRegistrationRepository;
use App\Repository\RateLimitRepository;

class AuthController
{
    private UserRepository $userRepository;
    private PasswordResetRepository $passwordResetRepository;
    private PendingRegistrationRepository $pendingRegistrationRepository;
    private RateLimitRepository $rateLimitRepository;

    // Fest statt aus dem Host-Header, damit Reset-Links nie auf fremde Domains zeigen
    private const APP_URL = 'https://stadtradeln.gymnasium-penzberg.de';

    private const MAIL_FROM_EMAIL = 'no-reply@stadtradeln.gymnasium-penzberg.de';
    private const MAIL_FROM_NAME = 'GYP-Radeln';
    private const RESET_EXPIRY_HOURS = 1;
    private const RESET_COOLDOWN_MINUTES = 10;
    // IP-Limits großzügig: an der Schule teilen sich alle Geräte eine öffentliche IP
    private const RESET_IP_MAX_ATTEMPTS = 50;
    private const RESET_IP_WINDOW_MINUTES = 60;

    private const LOGIN_MAX_ATTEMPTS = 100;
    private const LOGIN_WINDOW_MINUTES = 15;

    // Fehlversuche pro Account (gegen das Durchprobieren eines einzelnen Passworts)
    private const LOGIN_ACCOUNT_MAX_ATTEMPTS = 10;
    private const LOGIN_ACCOUNT_WINDOW_MINUTES = 15;

    private const REGISTER_MAX_ATTEMPTS = 100;
    private const REGISTER_WINDOW_MINUTES = 60;

    // Bestätigungslink der Registrierung; höchstens 5 E-Mails pro Adresse und Stunde,
    // damit sich über das Formular kein fremdes Postfach fluten lässt
    private const VERIFY_EXPIRY_HOURS = 24;
    private const VERIFY_EMAIL_MAX_ATTEMPTS = 5;
    private const VERIFY_EMAIL_WINDOW_MINUTES = 60;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
        $this->passwordResetRepository = new PasswordResetRepository();
        $this->pendingRegistrationRepository = new PendingRegistrationRepository();
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
        $email = trim(Request::post('email'));
        $password = Request::post('password');
        $remember = Request::post('remember') !== '';
        $error = '';
        $clientIp = RateLimitRepository::getClientIp();

        if ($this->rateLimitRepository->isRateLimited(
            $clientIp, 'login_failed',
            self::LOGIN_MAX_ATTEMPTS, self::LOGIN_WINDOW_MINUTES
        )) {
            $error = 'Zu viele fehlgeschlagene Versuche. Bitte versuche es später erneut.';
        } elseif (empty($email)) {
            $error = 'Du musst eine E-Mail eingeben';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // Nur ASCII-Adressen (wie bei der Registrierung): Die Datenbank-Collation setzt
            // "léa@…" und "lea@…" gleich, emailKey() aber nicht – sonst ließe sich die
            // Sperre pro Account mit Schreibvarianten umgehen.
            $error = 'Bitte gib eine gültige E-Mail-Adresse ein';
        } elseif (empty($password)) {
            $error = 'Du musst ein Passwort eingeben';
        } elseif ($this->rateLimitRepository->isRateLimited(
            RateLimitRepository::emailKey($email), 'login_failed',
            self::LOGIN_ACCOUNT_MAX_ATTEMPTS, self::LOGIN_ACCOUNT_WINDOW_MINUTES
        )) {
            $error = 'Zu viele fehlgeschlagene Versuche für diesen Account. Bitte versuche es in '
                . self::LOGIN_ACCOUNT_WINDOW_MINUTES . ' Minuten erneut.';
        } else {
            $user = $this->userRepository->findByEmail($email);

            if ($user === null && $this->isPendingRegistration($email, $password)) {
                $error = 'Dein Account ist noch nicht aktiviert. Klicke auf den Link in der Bestätigungs-E-Mail, '
                    . 'die wir dir geschickt haben (schau auch im Spam-Ordner nach).';
            } elseif ($user === null || !password_verify($password, $user->password)) {
                $this->rateLimitRepository->record($clientIp, 'login_failed');
                $this->rateLimitRepository->record(RateLimitRepository::emailKey($email), 'login_failed');
                $error = 'E-Mail oder Passwort ist falsch';
            } else {
                Session::login($user->id, $user->name, $user->teamId, $user->password);
                if ($remember) {
                    RememberMe::issue($user->id);
                }
                $this->userRepository->updateLastLogin($user->id);
                header("Location: /dashboard");
                exit;
            }
        }

        View::render('pages/login', ['error' => $error, 'email' => $email, 'remember' => $remember]);
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
        // Passwörter bewusst nicht trimmen: Login vergleicht sie ebenfalls ungetrimmt
        $data = [
            'name' => trim(Request::post('name')),
            'email' => trim(Request::post('email')),
            'password' => Request::post('password'),
            'confirm_password' => Request::post('confirm_password'),
        ];
        $error = '';
        $clientIp = RateLimitRepository::getClientIp();

        if ($this->rateLimitRepository->isRateLimited(
            $clientIp, 'register',
            self::REGISTER_MAX_ATTEMPTS, self::REGISTER_WINDOW_MINUTES
        )) {
            $error = 'Zu viele Registrierungsversuche. Bitte versuche es später erneut.';
            View::render('pages/register', ['error' => $error, 'data' => $data]);
            return;
        }

        $this->rateLimitRepository->record($clientIp, 'register');

        if (empty($data['name'])) {
            $error = 'Du musst einen Namen eingeben';
        } elseif (mb_strlen($data['name']) > User::NAME_MAX_LENGTH) {
            $error = 'Der Name darf höchstens ' . User::NAME_MAX_LENGTH . ' Zeichen lang sein';
        } elseif (empty($data['email'])) {
            $error = 'Du musst eine E-Mail eingeben';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $error = 'Bitte gib eine gültige E-Mail-Adresse ein';
        } elseif (EmailBlacklist::isBlocked($data['email'])) {
            $error = EmailBlacklist::ERROR;
        } elseif (empty($data['password'])) {
            $error = 'Du musst ein Passwort eingeben';
        } elseif ($data['password'] !== $data['confirm_password']) {
            $error = 'Passwörter stimmen nicht überein';
        } elseif ($this->userRepository->emailExists($data['email'])) {
            $error = 'Diese E-Mail ist bereits registriert';
        }

        // Der Account entsteht erst nach Bestätigung der E-Mail-Adresse (verifyEmail())
        if (empty($error)) {
            $emailKey = RateLimitRepository::emailKey($data['email']);

            if ($this->rateLimitRepository->isRateLimited(
                $emailKey, 'verify_email',
                self::VERIFY_EMAIL_MAX_ATTEMPTS, self::VERIFY_EMAIL_WINDOW_MINUTES
            )) {
                $error = 'An diese Adresse haben wir gerade schon mehrere Bestätigungs-E-Mails geschickt. '
                    . 'Schau in dein Postfach (auch im Spam-Ordner) oder versuche es später erneut.';
            } else {
                $token = PendingRegistrationRepository::generateToken();
                $expiresAt = new \DateTime('+' . self::VERIFY_EXPIRY_HOURS . ' hours');
                $this->pendingRegistrationRepository->create(
                    $data['name'], $data['email'],
                    password_hash($data['password'], PASSWORD_DEFAULT),
                    $token, $expiresAt
                );
                $this->rateLimitRepository->record($emailKey, 'verify_email');

                if ($this->sendVerificationEmail($data['email'], $token)) {
                    View::render('pages/register-sent', [
                        'email' => $data['email'],
                        'expiryHours' => self::VERIFY_EXPIRY_HOURS,
                    ]);
                    return;
                }
                $error = 'Die Bestätigungs-E-Mail konnte nicht gesendet werden. Bitte versuche es später erneut.';
            }
        }

        View::render('pages/register', ['error' => $error, 'data' => $data]);
    }

    public function showVerifyEmail(): void
    {
        $token = Request::get('token');
        $pending = $token === '' ? null : $this->pendingRegistrationRepository->findValidByToken($token);

        if ($pending === null) {
            $this->renderInvalidVerification('Dieser Link ist ungültig oder abgelaufen.');
            return;
        }

        // Aktiviert wird erst per POST: E-Mail-Scanner, die Links vorab öffnen,
        // sollen den Link nicht verbrauchen
        View::render('pages/verify-email', [
            'valid' => true,
            'token' => $token,
            'name' => $pending['name'],
            'email' => $pending['email'],
        ]);
    }

    public function verifyEmail(): void
    {
        $token = Request::post('token');
        $remember = Request::post('remember') !== '';
        $pending = $token === '' ? null : $this->pendingRegistrationRepository->findValidByToken($token);

        if ($pending === null) {
            $this->renderInvalidVerification('Dieser Link ist ungültig oder abgelaufen.');
            return;
        }

        // Die Sperrliste kann sich seit der Registrierung geändert haben
        if (EmailBlacklist::isBlocked($pending['email'])) {
            $this->pendingRegistrationRepository->deleteByEmail($pending['email']);
            $this->renderInvalidVerification(EmailBlacklist::ERROR);
            return;
        }

        $alreadyRegistered = 'Für diese E-Mail-Adresse gibt es bereits einen Account. Du kannst dich direkt anmelden.';

        if ($this->userRepository->emailExists($pending['email'])) {
            $this->pendingRegistrationRepository->deleteByEmail($pending['email']);
            $this->renderInvalidVerification($alreadyRegistered);
            return;
        }

        try {
            $userId = $this->userRepository->create($pending['name'], $pending['email'], $pending['passHash']);
        } catch (\mysqli_sql_exception $e) {
            // Gleichzeitige zweite Anfrage (z. B. Doppelklick) scheitert am UNIQUE-Key der E-Mail
            $this->renderInvalidVerification($alreadyRegistered);
            return;
        }

        $this->pendingRegistrationRepository->deleteByEmail($pending['email']);

        Session::login($userId, $pending['name'], null, $pending['passHash']);
        if ($remember) {
            RememberMe::issue($userId);
        }
        $this->userRepository->updateLastLogin($userId);
        header("Location: /dashboard");
        exit;
    }

    private function renderInvalidVerification(string $error): void
    {
        View::render('pages/verify-email', ['valid' => false, 'error' => $error]);
    }

    /** Stimmt das Passwort mit einer noch nicht bestätigten Registrierung überein? */
    private function isPendingRegistration(string $email, string $password): bool
    {
        $pending = $this->pendingRegistrationRepository->findValidByEmail($email);

        return $pending !== null && password_verify($password, $pending['passHash']);
    }

    public function logout(): void
    {
        RememberMe::forget();
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
        $email = trim(Request::post('email'));
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
        $token = Request::get('token');

        if (empty($token) || !$this->passwordResetRepository->isValid($token)) {
            View::render('pages/reset-password', ['error' => 'Dieser Link ist ungültig oder abgelaufen.', 'token' => '', 'valid' => false]);
            return;
        }

        View::render('pages/reset-password', ['error' => '', 'token' => $token, 'valid' => true]);
    }

    public function resetPassword(): void
    {
        $token = Request::post('token');
        $password = Request::post('password');
        $confirmPassword = Request::post('confirm_password');

        if (empty($token) || !$this->passwordResetRepository->isValid($token)) {
            View::render('pages/reset-password', ['error' => 'Dieser Link ist ungültig oder abgelaufen.', 'token' => '', 'valid' => false]);
            return;
        }

        $error = '';
        if (empty($password)) {
            $error = 'Bitte gib ein neues Passwort ein.';
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
        RememberMe::forgetAll($reset['userID']);

        header("Location: /login?reset=success");
        exit;
    }

    private function sendResetEmail(string $email, string $name, string $token): bool
    {
        $resetUrl = self::APP_URL . '/reset-password?token=' . $token;

        $message = "Hallo {$name},\n\n"
            . "du hast angefordert, dein Passwort zurückzusetzen.\n\n"
            . "Klicke auf folgenden Link, um ein neues Passwort zu setzen:\n"
            . "{$resetUrl}\n\n"
            . "Der Link ist " . self::RESET_EXPIRY_HOURS . " Stunde gültig.\n\n"
            . "Falls du diese Anfrage nicht gestellt hast, kannst du diese E-Mail ignorieren.\n\n"
            . "Viele Grüße\nDein GYP-Radeln-Team";

        return $this->sendMail($email, 'Passwort zurücksetzen - GYP-Radeln', $message);
    }

    /**
     * Ohne den eingegebenen Namen: Die Adresse ist hier noch unbestätigt, sonst ließe sich
     * über das Formular beliebiger Text an fremde Postfächer schicken.
     */
    private function sendVerificationEmail(string $email, string $token): bool
    {
        $verifyUrl = self::APP_URL . '/verify-email?token=' . $token;

        $message = "Hallo,\n\n"
            . "schön, dass du bei GYP-Radeln mitmachst!\n\n"
            . "Klicke auf folgenden Link, um deine E-Mail-Adresse zu bestätigen und deinen Account zu aktivieren:\n"
            . "{$verifyUrl}\n\n"
            . "Der Link ist " . self::VERIFY_EXPIRY_HOURS . " Stunden gültig.\n\n"
            . "Falls du dich nicht registriert hast, kannst du diese E-Mail ignorieren – dann wird kein Account angelegt.\n\n"
            . "Viele Grüße\nDein GYP-Radeln-Team";

        return $this->sendMail($email, 'E-Mail-Adresse bestätigen - GYP-Radeln', $message);
    }

    private function sendMail(string $email, string $subject, string $message): bool
    {
        // Betreff mit Umlauten nach RFC 2047 kodieren
        return mail($email, mb_encode_mimeheader($subject, 'UTF-8'), $message, [
            'From' => self::MAIL_FROM_NAME . ' <' . self::MAIL_FROM_EMAIL . '>',
            'Reply-To' => self::MAIL_FROM_EMAIL,
            'MIME-Version' => '1.0',
            'Content-Type' => 'text/plain; charset=UTF-8'
        ]);
    }
}
