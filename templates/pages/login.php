<?php
use App\Core\Icon;

$title = 'Anmelden';
$layout = 'auth';
require __DIR__ . '/../layout/header.php';
?>
<div class="auth">
    <div class="card auth-card reveal">
        <div class="auth-head">
            <span class="brand-mark"><?= Icon::svg('bike') ?></span>
            <h1>Willkommen zurück</h1>
            <p>Melde dich an, um deine Touren einzutragen.</p>
        </div>

        <form name="loginForm" method="post" action="/login" class="form">
            <?php if (($_GET['reset'] ?? '') === 'success'): ?>
                <div class="alert alert-success" role="status">
                    <?= Icon::svg('check-circle') ?>
                    <span>Dein Passwort wurde geändert. Du kannst dich jetzt anmelden.</span>
                </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error" role="alert">
                    <?= Icon::svg('alert') ?>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <div class="field">
                <label class="field-label" for="email">E-Mail</label>
                <div class="input-wrap">
                    <?= Icon::svg('mail') ?>
                    <input class="input" type="email" id="email" name="email"
                           value="<?= htmlspecialchars($email ?? '') ?>"
                           autocomplete="username" placeholder="deine@email.de" required autofocus>
                </div>
            </div>

            <div class="field">
                <div class="field-row">
                    <label class="field-label" for="password">Passwort</label>
                    <a href="/forgot-password">Passwort vergessen?</a>
                </div>
                <div class="input-wrap">
                    <?= Icon::svg('lock') ?>
                    <input class="input" type="password" id="password" name="password"
                           autocomplete="current-password" placeholder="Dein Passwort" required>
                    <?php require __DIR__ . '/../partials/password-toggle.php'; ?>
                </div>
            </div>

            <?php require __DIR__ . '/../partials/remember-toggle.php'; ?>

            <button type="submit" class="btn btn-primary btn-lg btn-block">
                Anmelden <?= Icon::svg('arrow-right') ?>
            </button>
        </form>

        <p class="form-footer">
            Noch keinen Account?
            <a href="/register">Jetzt registrieren</a>
        </p>
    </div>
</div>
<?php require __DIR__ . '/../layout/footer.php'; ?>
