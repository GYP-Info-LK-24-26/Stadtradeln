<?php
use App\Core\Icon;

$title = 'Neues Passwort';
$layout = 'auth';
if ($valid) {
    $scripts = ['zxcvbn.js', 'password-strength.js'];
    $inlineScript = "initPasswordStrength('password');";
}
require __DIR__ . '/../layout/header.php';
?>
<div class="auth">
    <div class="card auth-card">
        <div class="auth-head">
            <span class="brand-mark"><?= Icon::svg('key') ?></span>
            <h1>Neues Passwort</h1>
            <p><?= $valid ? 'Wähle ein neues Passwort für deinen Account.' : 'Dieser Link funktioniert leider nicht mehr.' ?></p>
        </div>

        <?php if ($valid): ?>
            <form method="post" action="/reset-password" class="form">
                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

                <?php if (!empty($error)): ?>
                    <div class="alert alert-error" role="alert">
                        <?= Icon::svg('alert') ?>
                        <span><?= htmlspecialchars($error) ?></span>
                    </div>
                <?php endif; ?>

                <div class="field">
                    <label class="field-label" for="password">Neues Passwort</label>
                    <div class="input-wrap">
                        <?= Icon::svg('lock') ?>
                        <input class="input" type="password" id="password" name="password"
                               autocomplete="new-password" placeholder="Mindestens mäßige Stärke" required autofocus>
                    </div>
                    <?php $meterId = 'password'; require __DIR__ . '/../partials/password-meter.php'; ?>
                </div>

                <div class="field">
                    <label class="field-label" for="confirm_password">Passwort bestätigen</label>
                    <div class="input-wrap">
                        <?= Icon::svg('lock') ?>
                        <input class="input" type="password" id="confirm_password" name="confirm_password"
                               autocomplete="new-password" placeholder="Passwort wiederholen" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-lg btn-block">Passwort ändern</button>
            </form>
        <?php else: ?>
            <div class="stack">
                <div class="alert alert-error" role="alert">
                    <?= Icon::svg('alert') ?>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
                <a href="/forgot-password" class="btn btn-primary btn-lg btn-block">Neuen Link anfordern</a>
                <a href="/login" class="btn btn-secondary btn-lg btn-block">Zur Anmeldung</a>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php require __DIR__ . '/../layout/footer.php'; ?>
