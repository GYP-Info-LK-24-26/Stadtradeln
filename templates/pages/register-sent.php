<?php
use App\Core\Icon;

/** Nach dem Absenden der Registrierung. Variablen: $email (Adresse, an die der Link ging), $expiryHours */
$title = 'E-Mail bestätigen';
$layout = 'auth';
require __DIR__ . '/../layout/header.php';
?>
<div class="auth">
    <div class="card auth-card reveal">
        <div class="auth-head">
            <span class="brand-mark"><?= Icon::svg('mail') ?></span>
            <h1>Fast geschafft!</h1>
            <p>Wir haben dir eine E-Mail an <strong><?= htmlspecialchars($email) ?></strong> geschickt. Klicke auf den Link darin, um deinen Account zu aktivieren.</p>
        </div>

        <div class="stack">
            <div class="alert alert-info" role="status">
                <?= Icon::svg('info') ?>
                <span>Keine E-Mail bekommen? Schau auch im Spam-Ordner nach. Der Link ist <?= (int) $expiryHours ?> Stunden gültig – danach kannst du dich einfach noch einmal registrieren.</span>
            </div>
            <a href="/register" class="btn btn-secondary btn-lg btn-block">Andere E-Mail-Adresse verwenden</a>
        </div>
    </div>
</div>
<?php require __DIR__ . '/../layout/footer.php'; ?>
