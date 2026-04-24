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

    public function index(): void
    {
        Session::requireLogin();

        $tours = $this->tourRepository->findByUser(Session::getUserId());

        View::render('pages/dashboard', [
            'teamId' => Session::getTeamId(),
            'tours' => $tours,
            'totalDistance' => array_sum(array_map(fn($t) => $t->distance, $tours))
        ]);
    }

    private const MAX_DISTANCE_PER_DAY = 300.0;

    private function validateTourInput(float $distance, string $date, ?int $excludeTourId = null): ?string
    {
        // Distanz muss positiv sein
        if ($distance <= 0) {
            return 'Die Distanz muss größer als 0 km sein.';
        }

        // Datum muss zwischen vor einer Woche und heute liegen
        $today = new \DateTimeImmutable('today');
        $minDate = $today->modify('-7 days');
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
                Session::setFlash('tour_popup', 'add');
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
                    Session::setFlash('tour_popup', 'edit');
                    Session::setFlash('tour_popup_data', json_encode([
                        'id' => $tourId,
                        'date' => $date,
                        'distance' => $distance,
                    ]));
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
