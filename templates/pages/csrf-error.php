<?php
use App\Core\Icon;

/** 403-Seite bei fehlendem oder abgelaufenem CSRF-Token. Variable: $back (Pfad zurück zum Formular) */
$title = 'Formular abgelaufen';
$layout = 'auth';
require __DIR__ . '/../layout/header.php';
?>
<div class="auth">
    <div class="card auth-card reveal">
        <div class="auth-head">
            <span class="brand-mark"><?= Icon::svg('alert') ?></span>
            <h1>Formular abgelaufen</h1>
            <p>Die Seite war zu lange geöffnet oder deine Sitzung ist inzwischen abgelaufen. Lade das Formular neu und versuche es noch einmal.</p>
        </div>
        <a href="<?= htmlspecialchars($back) ?>" class="btn btn-primary btn-lg btn-block">
            Zurück zum Formular <?= Icon::svg('arrow-right') ?>
        </a>
    </div>
</div>
<?php require __DIR__ . '/../layout/footer.php'; ?>
