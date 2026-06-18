<?php

namespace App\Controllers;

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

    /** Anzahl der im Kalender angezeigten und bearbeitbaren Tage (zwei Wochen inkl. heute). */
    private const CALENDAR_DAYS = 14;

    public function index(): void
    {
        Session::requireLogin();

        $tours = $this->tourRepository->findByUser(Session::getUserId());

        View::render('pages/dashboard', [
            'teamId' => Session::getTeamId(),
            'totalDistance' => array_sum(array_map(fn($t) => $t->distance, $tours)),
            'calendar' => $this->buildCalendar($tours),
        ]);
    }

    /**
     * Baut ein wochenweise (Mo–So) ausgerichtetes Kalender-Raster der letzten
     * zwei Wochen. Jede Zelle enthält Tagessumme, Farbstufe (0–4, GitHub-Stil)
     * und die einzelnen Touren des Tages.
     */
    private function buildCalendar(array $tours): array
    {
        // Touren nach Datum gruppieren
        $toursByDate = [];
        foreach ($tours as $tour) {
            $toursByDate[$tour->date][] = ['id' => $tour->id, 'distance' => $tour->distance];
        }

        $today = new \DateTimeImmutable('today');
        $windowStart = $today->modify('-' . (self::CALENDAR_DAYS - 1) . ' days');

        // Tagessummen im Fenster für die Farbskalierung sammeln
        $dailyTotals = [];
        for ($d = $windowStart; $d <= $today; $d = $d->modify('+1 day')) {
            $key = $d->format('Y-m-d');
            $dailyTotals[$key] = array_sum(array_column($toursByDate[$key] ?? [], 'distance'));
        }
        $maxDistance = $dailyTotals ? max($dailyTotals) : 0.0;

        // Raster auf volle Wochen erweitern (Montag … Sonntag)
        $gridStart = $windowStart->modify('-' . ((int)$windowStart->format('N') - 1) . ' days');
        $gridEnd = $today->modify('+' . (7 - (int)$today->format('N')) . ' days');

        $weeks = [];
        $week = [];
        for ($d = $gridStart; $d <= $gridEnd; $d = $d->modify('+1 day')) {
            $key = $d->format('Y-m-d');
            $inRange = ($d >= $windowStart && $d <= $today);
            $total = $inRange ? $dailyTotals[$key] : 0.0;

            $week[] = [
                'date'     => $key,
                'day'      => (int)$d->format('j'),
                'label'    => $d->format('d.m.Y'),
                'total'    => $total,
                'level'    => $this->intensityLevel($total, $maxDistance),
                'inRange'  => $inRange,
                'isToday'  => $key === $today->format('Y-m-d'),
                'isFuture' => $d > $today,
                'tours'    => $inRange ? ($toursByDate[$key] ?? []) : [],
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

    private function validateTourInput(float $distance, string $date, ?int $excludeTourId = null): ?string
    {
        // Distanz muss positiv sein
        if ($distance <= 0) {
            return 'Die Distanz muss größer als 0 km sein.';
        }

        // Datum muss innerhalb der angezeigten zwei Wochen (inkl. heute) liegen
        $today = new \DateTimeImmutable('today');
        $minDate = $today->modify('-' . (self::CALENDAR_DAYS - 1) . ' days');
        $tourDate = \DateTimeImmutable::createFromFormat('Y-m-d', $date);

        if ($tourDate === false) {
            return 'Das Datum ist ungültig.';
        }

        // Zeit auf Mitternacht normieren, damit der Vergleich rein auf Tagesbasis erfolgt
        $tourDate = $tourDate->setTime(0, 0, 0);

        if ($tourDate < $minDate || $tourDate > $today) {
            return 'Das Datum muss zwischen ' . $minDate->format('d.m.Y') . ' und heute (' . $today->format('d.m.Y') . ') liegen.';
        }

        // Tagessumme darf 300 km nicht überschreiten
        $userId = Session::getUserId();
        $existingTotal = $this->tourRepository->getDailyTotalByUser($userId, $date, $excludeTourId);
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

        $distance = (float)str_replace(',', '.', trim($_POST['distance'] ?? ''));
        $date = trim($_POST['date'] ?? '');

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

        $tourId = (int)($_POST['tour_id'] ?? 0);
        $distance = (float)str_replace(',', '.', trim($_POST['distance'] ?? ''));
        $date = trim($_POST['date'] ?? '');

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

        $tourId = (int)($_POST['tour_id'] ?? 0);
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
