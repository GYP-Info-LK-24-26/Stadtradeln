<?php
use App\Core\Csrf;
use App\Core\Icon;

$title = 'Passwort vergessen';
$layout = 'auth';
require __DIR__ . '/../layout/header.php';
?>
<div class="auth">
    <div class="card auth-card reveal">
        <div class="auth-head">
            <span class="brand-mark"><?= Icon::svg('key') ?></span>
            <h1>Passwort vergessen?</h1>
            <p>Gib deine E-Mail-Adresse ein. Wir schicken dir einen Link zum Zurücksetzen.</p>
        </div>

        <form method="post" action="/forgot-password" class="form">
            <?= Csrf::field() ?>
            <?php if (!empty($error)): ?>
                <div class="alert alert-error" role="alert">
                    <?= Icon::svg('alert') ?>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success" role="status">
                    <?= Icon::svg('check-circle') ?>
                    <span><?= htmlspecialchars($success) ?></span>
                </div>
            <?php endif; ?>

            <div class="field">
                <label class="field-label" for="email">E-Mail</label>
                <div class="input-wrap">
                    <?= Icon::svg('mail') ?>
                    <input class="input" type="email" id="email" name="email"
                           value="<?= htmlspecialchars($email ?? '') ?>"
                           autocomplete="email" placeholder="deine@email.de" required autofocus>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-lg btn-block">Link senden</button>
        </form>

        <p class="form-footer">
            Doch wieder eingefallen? <a href="/login">Zur Anmeldung</a>
        </p>
    </div>
</div>
<?php require __DIR__ . '/../layout/footer.php'; ?>
