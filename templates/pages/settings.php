<?php
use App\Core\Csrf;
use App\Core\Icon;
use App\Core\View;

$title = 'Einstellungen';
$scripts = ['zxcvbn.js', 'password-strength.js'];

// Rückmeldungen dem Bereich zuordnen, den der Controller angibt ($section)
$section = $section ?? null;
$nameSuccess = $section === 'name' && $success;
$nameError = $section === 'name' && $error;
$emailSuccess = $section === 'email' && $success;
$emailError = $section === 'email' && $error;
$passwordSuccess = $section === 'password' && $success;
$passwordError = $section === 'password' && $error;

$inlineScript = "initPasswordStrength('new_password');";

require __DIR__ . '/../layout/header.php';
?>
<div class="container container-narrow page">
    <header class="page-header">
        <div class="page-header-text reveal">
            <h1>Einstellungen</h1>
            <p class="lead">Verwalte dein Profil und deine Zugangsdaten.</p>
        </div>
    </header>

    <div class="stack-lg">
        <section class="card profile-card reveal" style="--i: 1" aria-label="Profil">
            <?= View::avatar($name, 'xl') ?>
            <div class="profile-text">
                <div class="profile-name">
                    <form method="post" action="/settings/name" class="inline-edit" data-inline-edit>
                        <?= Csrf::field() ?>
                        <span class="inline-edit-text"><?= htmlspecialchars($name) ?></span>
                        <input type="text" name="name" class="inline-edit-input" value="<?= htmlspecialchars($name) ?>"
                               maxlength="<?= \App\Models\User::NAME_MAX_LENGTH ?>" aria-label="Name" required>
                        <button type="button" class="inline-edit-btn" title="Name bearbeiten" aria-label="Name bearbeiten">
                            <?= Icon::svg('pencil') ?>
                        </button>
                    </form>
                </div>
                <div class="profile-email"><?= htmlspecialchars($email) ?></div>
            </div>
        </section>

        <?php if ($nameSuccess): ?>
            <div class="alert alert-success" role="status"><?= Icon::svg('check-circle') ?><span><?= htmlspecialchars($success) ?></span></div>
        <?php elseif ($nameError): ?>
            <div class="alert alert-error" role="alert"><?= Icon::svg('alert') ?><span><?= htmlspecialchars($error) ?></span></div>
        <?php endif; ?>

        <section class="reveal" style="--i: 2">
            <div class="section-title mt-0"><h2>Zugangsdaten</h2></div>
            <div class="card card-flush">
                <details class="setting" <?= $emailSuccess || $emailError ? 'open' : '' ?>>
                    <summary>
                        <span class="setting-icon"><?= Icon::svg('mail') ?></span>
                        <span class="setting-info">
                            <span class="setting-label">E-Mail-Adresse</span>
                            <span class="setting-value"><?= htmlspecialchars($email) ?></span>
                        </span>
                        <?= Icon::svg('chevron-down', 'icon setting-chevron') ?>
                    </summary>
                    <div class="setting-body">
                        <form method="post" action="/settings/email" class="form">
                            <?= Csrf::field() ?>
                            <?php if ($emailSuccess): ?>
                                <div class="alert alert-success" role="status"><?= Icon::svg('check-circle') ?><span><?= htmlspecialchars($success) ?></span></div>
                            <?php elseif ($emailError): ?>
                                <div class="alert alert-error" role="alert"><?= Icon::svg('alert') ?><span><?= htmlspecialchars($error) ?></span></div>
                            <?php endif; ?>
                            <div class="field">
                                <label class="field-label" for="email">Neue E-Mail-Adresse</label>
                                <input class="input" type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" autocomplete="email" required>
                            </div>
                            <div class="field">
                                <label class="field-label" for="email_password">Passwort zur Bestätigung</label>
                                <div class="input-wrap">
                                    <input class="input" type="password" id="email_password" name="password" autocomplete="current-password" required>
                                    <?php require __DIR__ . '/../partials/password-toggle.php'; ?>
                                </div>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">E-Mail speichern</button>
                            </div>
                        </form>
                    </div>
                </details>

                <details class="setting" id="passwordSetting" <?= $passwordSuccess || $passwordError ? 'open' : '' ?>>
                    <summary>
                        <span class="setting-icon"><?= Icon::svg('lock') ?></span>
                        <span class="setting-info">
                            <span class="setting-label">Passwort</span>
                            <span class="setting-value is-masked">••••••••</span>
                        </span>
                        <?= Icon::svg('chevron-down', 'icon setting-chevron') ?>
                    </summary>
                    <div class="setting-body">
                        <form method="post" action="/settings" class="form">
                            <?= Csrf::field() ?>
                            <?php if ($passwordSuccess): ?>
                                <div class="alert alert-success" role="status"><?= Icon::svg('check-circle') ?><span><?= htmlspecialchars($success) ?></span></div>
                            <?php elseif ($passwordError): ?>
                                <div class="alert alert-error" role="alert"><?= Icon::svg('alert') ?><span><?= htmlspecialchars($error) ?></span></div>
                            <?php endif; ?>
                            <div class="field">
                                <label class="field-label" for="current_password">Aktuelles Passwort</label>
                                <div class="input-wrap">
                                    <input class="input" type="password" id="current_password" name="current_password" autocomplete="current-password" required>
                                    <?php require __DIR__ . '/../partials/password-toggle.php'; ?>
                                </div>
                            </div>
                            <div class="field">
                                <label class="field-label" for="new_password">Neues Passwort</label>
                                <div class="input-wrap">
                                    <input class="input" type="password" id="new_password" name="new_password" autocomplete="new-password" placeholder="Mindestens mäßige Stärke" required>
                                    <?php require __DIR__ . '/../partials/password-toggle.php'; ?>
                                </div>
                                <?php $meterId = 'new_password'; require __DIR__ . '/../partials/password-meter.php'; ?>
                            </div>
                            <div class="field">
                                <label class="field-label" for="confirm_password">Neues Passwort bestätigen</label>
                                <div class="input-wrap">
                                    <input class="input" type="password" id="confirm_password" name="confirm_password" autocomplete="new-password" required>
                                    <?php require __DIR__ . '/../partials/password-toggle.php'; ?>
                                </div>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">Passwort ändern</button>
                            </div>
                        </form>
                    </div>
                </details>
            </div>
        </section>

        <section class="reveal" style="--i: 3">
            <div class="section-title mt-0"><h2>Sitzung</h2></div>
            <form method="post" action="/logout" class="card card-row">
                <?= Csrf::field() ?>
                <div>
                    <strong>Abmelden</strong>
                    <p>Beendet deine Sitzung auf diesem Gerät.</p>
                </div>
                <button type="submit" class="btn btn-secondary"><?= Icon::svg('log-out') ?> Abmelden</button>
            </form>
        </section>
    </div>
</div>
<?php require __DIR__ . '/../layout/footer.php'; ?>
