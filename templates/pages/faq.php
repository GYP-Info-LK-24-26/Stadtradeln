<?php
$title = 'FAQ';
require __DIR__ . '/../layout/header.php';
?>
<div class="container container-narrow page">
    <header class="page-header">
        <div class="page-header-text reveal">
            <h1>Häufige Fragen</h1>
            <p class="lead">Antworten rund ums Stadtradeln und die Nutzung der App.</p>
        </div>
    </header>

    <div class="card card-flush reveal" style="--i: 1">
    <details class="setting faq-prize">
            <summary>
                <span class="setting-info"><span class="setting-value">Gibt es einen Preis für die Siegerklasse?</span></span>
                <?= \App\Core\Icon::svg('chevron-down', 'icon setting-chevron') ?>
            </summary>
            <div class="setting-body">
                <p>Die Siegerklasse gewinnt einen Ausflug mit der ganzen Klasse.</p>
            </div>
        </details>  
    <details class="setting">
            <summary>
                <span class="setting-info"><span class="setting-value">Wie trage ich eine Fahrradtour ein?</span></span>
                <?= \App\Core\Icon::svg('chevron-down', 'icon setting-chevron') ?>
            </summary>
            <div class="setting-body">
                <p>Öffne dein Dashboard, wähle den passenden Tag im Kalender und gib die gefahrenen Kilometer ein. Speichere den Eintrag anschließend ab.</p>
            </div>
        </details>
        <details class="setting">
            <summary>
                <span class="setting-info"><span class="setting-value">Welche Fahrten zählen?</span></span>
                <?= \App\Core\Icon::svg('chevron-down', 'icon setting-chevron') ?>
            </summary>
            <div class="setting-body">
                <p>Es zählen Fahrradtouren, die du im Aktionszeitraum vom 10. bis 31. Oktober gefahren bist. Du kannst Einträge nur für vergangene oder heutige Tage speichern.</p>
            </div>
        </details>
        <details class="setting">
            <summary>
                <span class="setting-info"><span class="setting-value">Kann ich einen Eintrag später ändern?</span></span>
                <?= \App\Core\Icon::svg('chevron-down', 'icon setting-chevron') ?>
            </summary>
            <div class="setting-body">
                <p>Ja. Wähle den Tag im Dashboard erneut aus, um die Kilometer zu bearbeiten oder den Eintrag zu löschen.</p>
            </div>
        </details>
        <details class="setting">
            <summary>
                <span class="setting-info"><span class="setting-value">Wie trete ich einem Team bei?</span></span>
                <?= \App\Core\Icon::svg('chevron-down', 'icon setting-chevron') ?>
            </summary>
            <div class="setting-body">
                <p>Öffne den Bereich „Team“ und wähle „Team beitreten“. Dort kannst du ein bestehendes Team auswählen oder ein neues Team gründen.</p>
            </div>
        </details>
        <details class="setting">
            <summary>
                <span class="setting-info"><span class="setting-value">Wo sehe ich meinen aktuellen Platz?</span></span>
                <?= \App\Core\Icon::svg('chevron-down', 'icon setting-chevron') ?>
            </summary>
            <div class="setting-body">
                <p>In der Rangliste findest du die Kilometerstände von Personen und Teams. Deine eigenen Einträge werden dort hervorgehoben.</p>
            </div>
        </details>
        <details class="setting">
            <summary>
                <span class="setting-info"><span class="setting-value">Wie ändere ich meine Profildaten?</span></span>
                <?= \App\Core\Icon::svg('chevron-down', 'icon setting-chevron') ?>
            </summary>
            <div class="setting-body">
                <p>Öffne über deinen Namen die Einstellungen. Dort kannst du deinen Namen, deine E-Mail-Adresse und dein Passwort verwalten.</p>
            </div>
        </details>
        <details class="setting">
            <summary>
                <span class="setting-info"><span class="setting-value">Wer darft teilnehmen?</span></span>
                <?= \App\Core\Icon::svg('chevron-down', 'icon setting-chevron') ?>
            </summary>
            <div class="setting-body">
                <p>Alle Schüler vom Gymnasium sind herzlich willkommen teilzunehmen und wir freuen uns über jeden Teilnehmer.</p>
            </div>
        </details>
        <details class="setting">
            <summary>
                <span class="setting-info"><span class="setting-value">Wer erstellt die Teams und in welchem Team sollte ich sein?</span></span>
                <?= \App\Core\Icon::svg('chevron-down', 'icon setting-chevron') ?>
            </summary>
            <div class="setting-body">
                <p>Die Klassensprecher erstellen das jeweilige Team ihrer Klasse, diesem tretet ihr bitte bei.</p>
            </div>
        </details>
    </div>
</div>
<?php require __DIR__ . '/../layout/footer.php'; ?>