<?php
use App\Core\Icon;

/** Abschlussseite, solange die Aktion beendet ist (config/closed.php). */
$title = 'Aktion beendet';
$layout = 'auth';
require __DIR__ . '/../layout/header.php';
?>
<div class="auth">
    <div class="card auth-card reveal">
        <div class="auth-head">
            <span class="brand-mark"><?= Icon::svg('trophy') ?></span>
            <h1>Das GYP-Radeln ist vorbei</h1>
            <p>Danke an alle, die mitgeradelt sind! Die Aktion ist beendet: Anmeldung, Touren, Teams und Ranglisten sind nicht mehr verfügbar.</p>
        </div>
    </div>
</div>
<?php require __DIR__ . '/../layout/footer.php'; ?>
