<?php
use App\Core\Csrf;
use App\Core\Icon;

/**
 * Bestätigungslink aus der Registrierungs-E-Mail.
 * Variablen: $valid; bei gültigem Link $token, $name, $email; sonst $error
 */
$title = 'Account aktivieren';
$layout = 'auth';
require __DIR__ . '/../layout/header.php';
?>
<div class="auth">
    <div class="card auth-card reveal">
        <div class="auth-head">
            <span class="brand-mark"><?= Icon::svg($valid ? 'check-circle' : 'mail') ?></span>
            <h1>Account aktivieren</h1>
            <p><?= $valid
                ? 'Hallo ' . htmlspecialchars($name) . '! Bestätige deine E-Mail-Adresse <strong>' . htmlspecialchars($email) . '</strong>, um loszulegen.'
                : 'Dieser Link funktioniert leider nicht.' ?></p>
        </div>

        <?php if ($valid): ?>
            <form method="post" action="/verify-email" class="form">
                <?= Csrf::field() ?>
                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

                <?php $remember = false; require __DIR__ . '/../partials/remember-toggle.php'; ?>

                <button type="submit" class="btn btn-primary btn-lg btn-block">
                    Account aktivieren <?= Icon::svg('arrow-right') ?>
                </button>
            </form>
        <?php else: ?>
            <div class="stack">
                <div class="alert alert-error" role="alert">
                    <?= Icon::svg('alert') ?>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
                <a href="/login" class="btn btn-primary btn-lg btn-block">Zur Anmeldung</a>
                <a href="/register" class="btn btn-secondary btn-lg btn-block">Erneut registrieren</a>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php require __DIR__ . '/../layout/footer.php'; ?>
