<?php

namespace App\Core;

/**
 * Aktionszeitraum des Stadtradelns. Touren können nur für Tage innerhalb
 * dieses Zeitraums (und nicht in der Zukunft) eingetragen werden.
 */
class Event
{
    /** Erster und letzter Aktionstag (Monat-Tag, jeweils im aktuellen Jahr). */
    private const START = '10-10';
    private const END = '10-31';

    public static function start(): \DateTimeImmutable
    {
        return new \DateTimeImmutable(date('Y') . '-' . self::START);
    }

    public static function end(): \DateTimeImmutable
    {
        return new \DateTimeImmutable(date('Y') . '-' . self::END);
    }

    /** Ob die Aktion noch nicht begonnen hat. */
    public static function isUpcoming(): bool
    {
        return new \DateTimeImmutable('today') < self::start();
    }

    /** Ob die Aktion vorbei ist. */
    public static function isOver(): bool
    {
        return new \DateTimeImmutable('today') > self::end();
    }

    /** Zeitraum als Text, z. B. "10.10. – 31.10.2026". */
    public static function label(): string
    {
        return self::start()->format('d.m.') . ' – ' . self::end()->format('d.m.Y');
    }
}
