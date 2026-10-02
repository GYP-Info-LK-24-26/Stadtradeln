<?php
/**
 * Gemeinsamer Seitenfuß. Enthält den Bestätigungsdialog, der von app.js für
 * Formulare mit data-confirm verwendet wird.
 *
 * Variablen:
 *   $scripts      – optionale Liste zusätzlicher Skripte aus /js/
 *   $inlineScript – optionales Inline-JavaScript (läuft nach allen Skripten)
 */

use App\Core\Icon;
use App\Core\View;
?>
    </main>

    <dialog class="dialog dialog-sm" id="confirmDialog" aria-labelledby="confirmTitle">
        <div class="dialog-body">
            <div class="dialog-icon" id="confirmIcon"><?= Icon::svg('alert') ?></div>
            <h2 class="dialog-title" id="confirmTitle">Bist du sicher?</h2>
            <p class="dialog-text" id="confirmText"></p>
        </div>
        <div class="dialog-actions">
            <button type="button" class="btn btn-secondary" data-dialog-close>Abbrechen</button>
            <button type="button" class="btn btn-primary" id="confirmOk">Bestätigen</button>
        </div>
    </dialog>

    <?php foreach ($scripts ?? [] as $script): ?>
        <script src="<?= View::asset('/js/' . $script) ?>" defer></script>
    <?php endforeach; ?>
    <?php if (!empty($inlineScript)): ?>
        <script>document.addEventListener('DOMContentLoaded', function () { <?= $inlineScript ?> });</script>
    <?php endif; ?>
</body>
</html>
