<?php

/**
 * Gesperrte E-Mail-Adressen für Registrierung und E-Mail-Änderung (App\Core\EmailBlacklist).
 *
 * Jeder Eintrag ist ein regulärer Ausdruck (PCRE, mit Begrenzern), der gegen die
 * kleingeschriebene Adresse geprüft wird. Passt einer, ist die Adresse gesperrt.
 * Ungültige Ausdrücke werden übersprungen und ins Error-Log geschrieben.
 *
 * Beispiele:
 *   '/^spam@gmail\.com$/'             genau diese Adresse
 *   '/^spam(\+.*)?@gmail\.com$/'      dazu Varianten wie spam+1@gmail.com
 *   '/@myspamdomain\.de$/'            die Domain myspamdomain.de
 *   '/[@.]myspamdomain\.de$/'         die Domain samt Subdomains (a@mail.myspamdomain.de)
 *
 * Punkte mit "\." maskieren und mit "$" am Ende verankern, sonst sperrt
 * '/@spam.de/' auch a@spam.de.example.org oder a@spamxde.org.
 * Bestehende Accounts bleiben unberührt.
 */

return [
    // '/^spam@gmail\.com$/',
    // '/[@.]myspamdomain\.de$/',
];
