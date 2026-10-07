<?php use App\Core\Icon; /* Button zum Anzeigen des Passworts; steht in .input-wrap direkt hinter dem Passwortfeld. Gesteuert von app.js */ ?>
<button type="button" class="input-action" data-password-toggle aria-label="Passwort anzeigen" aria-pressed="false">
    <?= Icon::svg('eye', 'icon icon-show') ?><?= Icon::svg('eye-off', 'icon icon-hide') ?>
</button>
