<?php

/**
 * Gesperrte E-Mail-Adressen für Registrierung und E-Mail-Änderung (App\Core\EmailBlacklist).
 * Groß-/Kleinschreibung spielt keine Rolle.
 *
 * - Ohne "@" (Domain): sperrt die Domain samt Subdomains.
 *   'myspamdomain.de' sperrt a@myspamdomain.de und a@mail.myspamdomain.de,
 *   aber nicht a@notmyspamdomain.de.
 * - Mit "@": sperrt alle Adressen, die so enden.
 *   'spam@gmail.com' sperrt spam@gmail.com (und z. B. myspam@gmail.com),
 *   '@gmail.com' sperrt nur die Domain gmail.com selbst, ohne Subdomains.
 *
 * Bestehende Accounts bleiben unberührt.
 */

return [
    // 'spam@gmail.com',
    // 'myspamdomain.de',
];
