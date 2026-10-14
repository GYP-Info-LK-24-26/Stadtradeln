<?php
/**
 * Datenschutzerklärung (Informationen nach Art. 13 DSGVO).
 * Aufbau nach dem Muster für Datenschutzhinweise im Internetauftritt staatlicher Schulen
 * in Bayern und der Datenschutzerklärung der Schulhomepage, angepasst an die tatsächliche
 * Datenverarbeitung dieser Anwendung. Bei Änderungen an gespeicherten Daten, Cookies oder
 * Fristen (z. B. RememberMe, RateLimitRepository) diese Seite mit anpassen.
 */
$title = 'Datenschutzerklärung';
require __DIR__ . '/../layout/header.php';
?>
<div class="container container-narrow page">
    <header class="page-header">
        <div class="page-header-text reveal">
            <h1>Datenschutzerklärung</h1>
            <p class="lead">Informationen zur Verarbeitung deiner personenbezogenen Daten nach Art. 13 DSGVO</p>
        </div>
    </header>

    <div class="card prose reveal" style="--i: 1">
        <h2>1. Verantwortliche Stelle</h2>
        <p>
            Staatliches Gymnasium Penzberg<br>
            Karlstraße 38–42<br>
            82377 Penzberg<br>
            vertreten durch den Schulleiter OStD Dr. Matthias Langensteiner
        </p>
        <p>
            Telefon: <a href="tel:+49885692710">08856 9271-0</a><br>
            E-Mail: <a href="mailto:sekretariat@gymnasium-penzberg.de">sekretariat@gymnasium-penzberg.de</a>
        </p>

        <h2>2. Datenschutzbeauftragte/r</h2>
        <p>
            Behördliche/r Datenschutzbeauftragte/r des Gymnasiums Penzberg<br>
            Karlstraße 38–42, 82377 Penzberg<br>
            E-Mail: <a href="mailto:datenschutz@gymnasium-penzberg.de">datenschutz@gymnasium-penzberg.de</a>
        </p>

        <h2>3. Zweck und Rechtsgrundlage</h2>
        <p>GYP-Radeln ist die Anwendung des Gymnasiums Penzberg für die schulinterne Stadtradeln-Aktion. Wir verarbeiten deine Daten, um die Teilnahme zu ermöglichen: Benutzerkonto, Teams, gefahrene Kilometer und Ranglisten. Die Teilnahme ist freiwillig.</p>
        <p>Rechtsgrundlage ist Art. 6 Abs. 1 Satz 1 Buchst. e DSGVO in Verbindung mit Art. 4 Abs. 1 Bayerisches Datenschutzgesetz (BayDSG) und Art. 2 Abs. 1 Bayerisches Gesetz über das Erziehungs- und Unterrichtswesen (BayEUG). Wenn du eine Funktion ausdrücklich auswählst, zum Beispiel „Angemeldet bleiben“, ist die Rechtsgrundlage deine Einwilligung (Art. 6 Abs. 1 Satz 1 Buchst. a DSGVO).</p>

        <h2>4. Welche Daten wir verarbeiten</h2>
        <h3>Aufruf der Seiten (Server-Logfiles)</h3>
        <p>Bei jedem Aufruf speichert der Webserver automatisch technische Daten: IP-Adresse, Datum und Uhrzeit, aufgerufene Seite, übertragene Datenmenge, Referrer-URL sowie Browser und Betriebssystem. Diese Daten dienen nur dem sicheren und stabilen Betrieb. Wir führen sie nicht mit anderen Daten zusammen und werten sie nicht zu Statistik- oder Werbezwecken aus.</p>

        <h3>Registrierung und Benutzerkonto</h3>
        <p>Für die Registrierung brauchen wir deinen Namen, deine E-Mail-Adresse und ein Passwort. Das Passwort speichern wir nur als sicheren Hash; im Klartext können wir es nicht einsehen. Solange du deine E-Mail-Adresse nicht bestätigt hast, liegen die Angaben in einer Warteliste. Die Einträge dort werden nach 24 Stunden gelöscht, spätestens aber mit der Bestätigung. Außerdem speichern wir den Zeitpunkt der Registrierung und den deiner letzten Anmeldung.</p>

        <h3>Touren und Teams</h3>
        <p>Wir speichern deine eingetragenen Touren (Datum und Kilometer), deine Teamzugehörigkeit sowie, falls du ein Team leitest, die Teamleitung.</p>

        <h3>Sichtbarkeit für andere Teilnehmende</h3>
        <p>Angemeldete Teilnehmende sehen in der Rangliste und in der Teamansicht deinen Namen, dein Team und deine Gesamtkilometer im Aktionszeitraum. Deine E-Mail-Adresse und deine einzelnen Touren sind für andere nicht sichtbar. Ohne Anmeldung sind keine personenbezogenen Daten einsehbar.</p>

        <h3>E-Mails</h3>
        <p>An deine E-Mail-Adresse schicken wir nur die nötigen Systemmails: einen Link zur Bestätigung der Adresse und, auf deine Anfrage, einen Link zum Zurücksetzen des Passworts. Dieser Link ist 1 Stunde gültig. Wir speichern nur Prüfwerte (Hashes) der Links. Werbung oder Newsletter verschicken wir nicht.</p>

        <h3>Schutz vor Missbrauch</h3>
        <p>Zum Schutz vor automatisierten Angriffen, zum Beispiel dem Durchprobieren von Passwörtern, speichern wir bei Anmeldung, Registrierung, Passwort-Zurücksetzen und Passwortprüfung die IP-Adresse oder eine Kennung des betroffenen Kontos zusammen mit dem Zeitpunkt. Diese Einträge werden nach 60 Minuten gelöscht.</p>

        <h2>5. Cookies und lokale Speicherung</h2>
        <p>Wir verwenden ausschließlich technisch notwendige Cookies (§ 25 Abs. 2 Nr. 2 TDDDG):</p>
        <ul>
            <li><strong>Sitzungscookie</strong> (<code>PHPSESSID</code>): hält dich während der Nutzung angemeldet und schützt Formulare vor Fremdzugriff (CSRF). Es wird beim Schließen des Browsers gelöscht; die Sitzung endet außerdem nach 30 Minuten Inaktivität.</li>
            <li><strong>„Angemeldet bleiben“</strong> (<code>remember</code>): wird nur gesetzt, wenn du diese Option auswählst, und ist 30 Tage gültig. Beim Abmelden und bei jeder Passwortänderung wird es ungültig.</li>
        </ul>
        <p>Damit die App schneller lädt und offline eine Hinweisseite zeigen kann, speichert dein Browser Stylesheets, Schriften und Symbole. Personenbezogene Daten sind darin nicht enthalten. Tracking- oder Analysewerkzeuge setzen wir nicht ein. Wir binden auch keine Inhalte von Drittanbietern ein, etwa Schriftarten oder Skripte von fremden Servern.</p>

        <h2>6. Empfänger und Hosting</h2>
        <p>Die Anwendung läuft bei einem Hosting-Dienstleister, der in unserem Auftrag Daten verarbeitet (Art. 28 DSGVO):</p>
        <p>
            ALL-INKL.COM – Neue Medien Münnich<br>
            Inhaber: René Münnich<br>
            Hauptstraße 68, 02742 Friedersdorf
        </p>
        <p>Die Daten werden auf Servern in Deutschland gespeichert. Eine Übermittlung in Drittländer findet nicht statt. Daten geben wir nur weiter, wenn eine gesetzliche Pflicht dazu besteht.</p>

        <h2>7. Speicherdauer</h2>
        <p>Deine Kontodaten, Touren und Teamzugehörigkeit speichern wir, bis du die Löschung verlangst oder die Daten für Durchführung und Auswertung der Aktion nicht mehr benötigt werden. Löschen wir dein Konto, werden auch deine Touren und Anmeldetokens gelöscht. Für einzelne Daten gelten die kürzeren Fristen aus den Abschnitten 4 und 5.</p>

        <h2>8. Deine Rechte</h2>
        <p>Du hast gegenüber der Schule folgende Rechte:</p>
        <ul>
            <li>Auskunft über deine gespeicherten Daten (Art. 15 DSGVO)</li>
            <li>Berichtigung unrichtiger Daten (Art. 16 DSGVO); Name, E-Mail-Adresse und Touren kannst du auch selbst in der App ändern</li>
            <li>Löschung (Art. 17 DSGVO)</li>
            <li>Einschränkung der Verarbeitung (Art. 18 DSGVO)</li>
            <li>Widerspruch gegen die Verarbeitung aus Gründen, die sich aus deiner besonderen Situation ergeben (Art. 21 DSGVO)</li>
            <li>Widerruf einer erteilten Einwilligung mit Wirkung für die Zukunft (Art. 7 Abs. 3 DSGVO)</li>
        </ul>
        <p>Wende dich dazu an die Schule oder an die/den Datenschutzbeauftragte/n (Kontakt siehe oben).</p>

        <h2>9. Beschwerderecht</h2>
        <p>Du kannst dich bei einer Datenschutz-Aufsichtsbehörde beschweren (Art. 77 DSGVO). Für staatliche Schulen in Bayern ist das:</p>
        <p>
            Der Bayerische Landesbeauftragte für den Datenschutz<br>
            Wagmüllerstraße 18, 80538 München<br>
            <a href="https://www.datenschutz-bayern.de" rel="noopener">www.datenschutz-bayern.de</a>
        </p>

        <h2>10. Verschlüsselung</h2>
        <p>Alle Seiten werden ausschließlich über eine verschlüsselte HTTPS-Verbindung übertragen.</p>

        <p class="text-muted">Stand: Oktober 2026</p>
    </div>
</div>
<?php require __DIR__ . '/../layout/footer.php'; ?>
