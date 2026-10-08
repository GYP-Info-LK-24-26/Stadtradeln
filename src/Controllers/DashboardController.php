<?php

namespace App\Controllers;

use App\Core\Event;
use App\Core\Request;
use App\Core\Session;
use App\Core\View;
use App\Repository\TourRepository;

class DashboardController
{
    private TourRepository $tourRepository;

    public function __construct()
    {
        $this->tourRepository = new TourRepository();
    }

    public function index(): void
    {
        Session::requireLogin();

        // Nur Touren im Aktionszeitraum zählen (ältere Einträge bleiben unberücksichtigt)
        $start = Event::start()->format('Y-m-d');
        $end = Event::end()->format('Y-m-d');
        $tours = array_filter(
            $this->tourRepository->findByUser(Session::getUserId()),
            fn($t) => $t->date >= $start && $t->date <= $end
        );

        View::render('pages/dashboard', [
            'teamId' => Session::getTeamId(),
            'totalDistance' => array_sum(array_map(fn($t) => $t->distance, $tours)),
            'calendar' => $this->buildCalendar($tours),
        ]);
    }

    /**
     * Baut ein wochenweise (Mo–So) ausgerichtetes Kalender-Raster über den
     * Aktionszeitraum. Jede Zelle enthält Tagessumme, Farbstufe (0–4, GitHub-Stil)
     * und die einzelnen Touren des Tages. Bearbeitbar sind nur Aktionstage bis heute.
     */
    private function buildCalendar(array $tours): array
    {
        // Touren nach Datum gruppieren
        $toursByDate = [];
        foreach ($tours as $tour) {
            $toursByDate[$tour->date][] = ['id' => $tour->id, 'distance' => $tour->distance];
        }

        $today = new \DateTimeImmutable('today');
        $eventStart = Event::start();
        $eventEnd = Event::end();

        // Tagessummen im Aktionszeitraum für die Farbskalierung sammeln
        $dailyTotals = [];
        for ($d = $eventStart; $d <= $eventEnd; $d = $d->modify('+1 day')) {
            $key = $d->format('Y-m-d');
            $dailyTotals[$key] = array_sum(array_column($toursByDate[$key] ?? [], 'distance'));
        }
        $maxDistance = $dailyTotals ? max($dailyTotals) : 0.0;

        // Raster auf volle Wochen erweitern (Montag … Sonntag)
        $gridStart = $eventStart->modify('-' . ((int)$eventStart->format('N') - 1) . ' days');
        $gridEnd = $eventEnd->modify('+' . (7 - (int)$eventEnd->format('N')) . ' days');

        $weeks = [];
        $week = [];
        for ($d = $gridStart; $d <= $gridEnd; $d = $d->modify('+1 day')) {
            $key = $d->format('Y-m-d');
            $inEvent = ($d >= $eventStart && $d <= $eventEnd);
            $total = $inEvent ? $dailyTotals[$key] : 0.0;

            $week[] = [
                'date'     => $key,
                'day'      => (int)$d->format('j'),
                'label'    => $d->format('d.m.Y'),
                'total'    => $total,
                'level'    => $this->intensityLevel($total, $maxDistance),
                'inEvent'  => $inEvent,
                'editable' => $inEvent && $d <= $today,
                'isToday'  => $key === $today->format('Y-m-d'),
                'isFuture' => $d > $today,
                'tours'    => $inEvent ? ($toursByDate[$key] ?? []) : [],
            ];

            if (count($week) === 7) {
                $weeks[] = $week;
                $week = [];
            }
        }

        return $weeks;
    }

    /** GitHub-artige Intensitätsstufe 0–4, relativ zum Maximum des Fensters. */
    private function intensityLevel(float $total, float $max): int
    {
        if ($total <= 0 || $max <= 0) {
            return 0;
        }
        return (int)max(1, min(4, ceil($total / $max * 4)));
    }

    private const MAX_DISTANCE_PER_DAY = 300.0;
    public const MAX_TOURS_PER_DAY = 10;

    private function validateTourInput(float $distance, string $date, ?int $excludeTourId = null): ?string
    {
        // Distanz muss positiv sein
        if ($distance <= 0) {
            return 'Die Distanz muss größer als 0 km sein.';
        }

        // Datum muss im Aktionszeitraum und nicht in der Zukunft liegen
        $today = new \DateTimeImmutable('today');
        $tourDate = \DateTimeImmutable::createFromFormat('Y-m-d', $date);

        // Rückumwandlung prüfen: PHP macht aus "2026-09-40" sonst stillschweigend den 10.10.
        if ($tourDate === false || $tourDate->format('Y-m-d') !== $date) {
            return 'Das Datum ist ungültig.';
        }

        // Zeit auf Mitternacht normieren, damit der Vergleich rein auf Tagesbasis erfolgt
        $tourDate = $tourDate->setTime(0, 0, 0);

        if ($tourDate < Event::start() || $tourDate > Event::end()) {
            return 'Touren können nur im Aktionszeitraum (' . Event::label() . ') eingetragen werden.';
        }
        if ($tourDate > $today) {
            return 'Touren in der Zukunft können nicht eingetragen werden.';
        }

        $userId = Session::getUserId();
        $daily = $this->tourRepository->getDailyStatsByUser($userId, $date, $excludeTourId);

        // Höchstens 10 Touren pro Tag (die bearbeitete Tour zählt nicht doppelt)
        if ($daily['count'] >= self::MAX_TOURS_PER_DAY) {
            return 'Du kannst höchstens ' . self::MAX_TOURS_PER_DAY . ' Touren pro Tag eintragen.';
        }

        // Tagessumme darf 300 km nicht überschreiten
        $existingTotal = $daily['total'];
        if ($existingTotal + $distance > self::MAX_DISTANCE_PER_DAY) {
            $remaining = self::MAX_DISTANCE_PER_DAY - $existingTotal;
            return sprintf(
                'Du hast an diesem Tag bereits %.1f km eingetragen. Das Tageslimit beträgt %d km (noch %.1f km möglich).',
                $existingTotal,
                (int)self::MAX_DISTANCE_PER_DAY,
                max(0, $remaining)
            );
        }

        return null;
    }

    public function addTour(): void
    {
        Session::requireLogin();

        $distance = (float)str_replace(',', '.', trim(Request::post('distance')));
        $date = trim(Request::post('date'));

        if ($distance > 0 && $date !== '') {
            $error = $this->validateTourInput($distance, $date);
            if ($error !== null) {
                Session::setFlash('tour_error', $error);
                Session::setFlash('tour_popup_date', $date);
                header("Location: /dashboard");
                exit;
            }
            $this->tourRepository->create(Session::getUserId(), $distance, $date);
        }

        header("Location: /dashboard");
        exit;
    }

    public function updateTour(): void
    {
        Session::requireLogin();

        $tourId = (int)Request::post('tour_id');
        $distance = (float)str_replace(',', '.', trim(Request::post('distance')));
        $date = trim(Request::post('date'));

        if ($tourId > 0 && $distance > 0 && $date !== '') {
            $tour = $this->tourRepository->findById($tourId);
            if ($tour && $tour->userId === Session::getUserId()) {
                $error = $this->validateTourInput($distance, $date, $tourId);
                if ($error !== null) {
                    Session::setFlash('tour_error', $error);
                    Session::setFlash('tour_popup_date', $date);
                    header("Location: /dashboard");
                    exit;
                }
                $this->tourRepository->update($tourId, $distance, $date);
            }
        }

        header("Location: /dashboard");
        exit;
    }

    public function deleteTour(): void
    {
        Session::requireLogin();

        $tourId = (int)Request::post('tour_id');
        if ($tourId > 0) {
            $tour = $this->tourRepository->findById($tourId);
            if ($tour && $tour->userId === Session::getUserId()) {
                $this->tourRepository->delete($tourId);
            }
        }

        header("Location: /dashboard");
        exit;
    }
}
