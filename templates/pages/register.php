<?php
use App\Core\Csrf;
use App\Core\Icon;

$title = 'Registrieren';
$layout = 'auth';
$scripts = ['zxcvbn.js', 'password-strength.js'];
require __DIR__ . '/../layout/header.php';
?>
<div class="auth">
    <div class="card auth-card reveal">
        <div class="auth-head">
            <span class="brand-mark"><?= Icon::svg('bike') ?></span>
            <h1>Account erstellen</h1>
            <p>In einer Minute startklar – dann zählt jeder Kilometer. Zum Schluss bestätigst du noch deine E-Mail-Adresse.</p>
        </div>

        <form name="registerForm" method="post" action="/register" class="form">
            <?= Csrf::field() ?>
            <?php if (!empty($error)): ?>
                <div class="alert alert-error" role="alert">
                    <?= Icon::svg('alert') ?>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <div class="field">
                <label class="field-label" for="name">Name</label>
                <div class="input-wrap">
                    <?= Icon::svg('user') ?>
                    <input class="input" type="text" id="name" name="name"
                           value="<?= htmlspecialchars($data['name'] ?? '') ?>"
                           autocomplete="name" placeholder="Max Mustermann"
                           maxlength="<?= \App\Models\User::NAME_MAX_LENGTH ?>" required>
                </div>
            </div>

            <div class="field">
                <label class="field-label" for="email">E-Mail</label>
                <div class="input-wrap">
                    <?= Icon::svg('mail') ?>
                    <input class="input" type="email" id="email" name="email"
                           value="<?= htmlspecialchars($data['email'] ?? '') ?>"
                           autocomplete="username" placeholder="deine@email.de" required>
                </div>
            </div>

            <div class="field">
                <label class="field-label" for="password">Passwort</label>
                <div class="input-wrap">
                    <?= Icon::svg('lock') ?>
                    <input class="input" type="password" id="password" name="password"
                           autocomplete="new-password" placeholder="Mindestens mäßige Stärke" required>
                    <?php require __DIR__ . '/../partials/password-toggle.php'; ?>
                </div>
                <?php $meterId = 'password'; require __DIR__ . '/../partials/password-meter.php'; ?>
            </div>

            <div class="field">
                <label class="field-label" for="confirm_password">Passwort bestätigen</label>
                <div class="input-wrap">
                    <?= Icon::svg('lock') ?>
                    <input class="input" type="password" id="confirm_password" name="confirm_password"
                           autocomplete="new-password" placeholder="Passwort wiederholen" required>
                    <?php require __DIR__ . '/../partials/password-toggle.php'; ?>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-lg btn-block">
                Registrieren <?= Icon::svg('arrow-right') ?>
            </button>
        </form>

        <p class="form-footer">
            Schon registriert?
            <a href="/login">Anmelden</a>
        </p>
    </div>
</div>
<?php
$inlineScript = "initPasswordStrength('password', ['name', 'email']);";
require __DIR__ . '/../layout/footer.php';
