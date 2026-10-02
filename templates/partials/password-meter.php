<?php /* Passwort-Stärkeanzeige; erwartet $meterId (ID des Passwortfelds). Gesteuert von password-strength.js */ ?>
<div class="pw-strength" id="pw-strength-<?= $meterId ?>" aria-live="polite">
    <div class="pw-strength-bar">
        <div class="pw-strength-seg"></div>
        <div class="pw-strength-seg"></div>
        <div class="pw-strength-seg"></div>
        <div class="pw-strength-seg"></div>
    </div>
    <span class="pw-strength-label"></span>
</div>
