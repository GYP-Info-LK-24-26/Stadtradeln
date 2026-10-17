<?php

/**
 * Lücken in users.id, die bei der Vergabe der Pseudonyme übersprungen werden (App\Core\Pseudonym).
 *
 * Kopieren nach pseudonym-gaps.php (nicht in Git). Fehlt die Datei, gibt es keine Lücken.
 *
 * Jeder Eintrag ist ein Bereich [von, bis] (beide einschließlich) von IDs, die in der
 * Datenbank nicht vergeben sind. Die erste ID nach einer Lücke erhält das Pseudonym,
 * das sonst die erste ID der Lücke bekommen hätte.
 *
 * Achtung: Ein neuer Eintrag ändert die Pseudonyme aller Accounts mit einer ID oberhalb
 * der Lücke. Nur Bereiche eintragen, in denen keine Accounts existieren, sonst teilen sich
 * Accounts ein Pseudonym. Ungültige Einträge werden übersprungen und ins Error-Log geschrieben.
 */

return [
    // [101, 1100],
    // [1250, 2249],
];
