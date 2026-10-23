<?php
use App\Core\Csrf;
use App\Core\Icon;

/** Namensabfrage für Accounts ohne Namen. Variablen: $error, optional $name */
$title = 'Name angeben';
$layout = 'auth';
require __DIR__ . '/../layout/header.php';
?>
<div class="auth">
    <div class="card auth-card reveal">
        <div class="auth-head">
            <span class="brand-mark"><?= Icon::svg('user') ?></span>
            <h1>Wie heißt du?</h1>
            <p>Für die Auswertung und Ehrung brauchen wir deinen Vor- und Nachnamen. In der Rangliste und im Team erscheinst du weiterhin unter deinem Pseudonym.</p>
        </div>

        <form method="post" action="/name" class="form">
            <?= Csrf::field() ?>
            <div class="alert alert-info" role="status">
                <?= Icon::svg('info') ?>
                <span>Falls du schon einmal einen Namen angegeben hattest: Er wurde aus Datenschutzgründen gelöscht. Bitte gib ihn noch einmal an.</span>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error" role="alert">
                    <?= Icon::svg('alert') ?>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <div class="field">
                <label class="field-label" for="name">Vor- und Nachname</label>
                <div class="input-wrap">
                    <?= Icon::svg('user') ?>
                    <input class="input" type="text" id="name" name="name"
                           value="<?= htmlspecialchars($name ?? '') ?>"
                           autocomplete="name" placeholder="Max Mustermann"
                           maxlength="<?= \App\Models\User::NAME_MAX_LENGTH ?>" required autofocus>
                </div>
            </div>

            <label class="checkbox">
                <input type="checkbox" name="privacy" value="1" required>
                <span>Ich habe die <a href="/datenschutz" target="_blank" rel="noopener">Datenschutzhinweise</a> gelesen und willige in die Verarbeitung meiner Daten ein.</span>
            </label>

            <button type="submit" class="btn btn-primary btn-lg btn-block">
                Speichern <?= Icon::svg('arrow-right') ?>
            </button>
        </form>
    </div>
</div>
<?php require __DIR__ . '/../layout/footer.php'; ?>
