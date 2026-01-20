<?php

namespace App\Controllers;

use App\Core\Session;
use App\Core\View;
use App\Repository\TeamRepository;
use App\Repository\TourRepository;

class DashboardController
{
    private TourRepository $tourRepository;
    private TeamRepository $teamRepository;

    public function __construct()
    {
        $this->tourRepository = new TourRepository();
        $this->teamRepository = new TeamRepository();
    }

    public function index(): void
    {
        Session::requireLogin();

        $userId = Session::getUserId();
        $teamId = Session::getTeamId();

        $tours = $this->tourRepository->findByUser($userId);
        $totalDistance = array_sum(array_map(fn($t) => $t->distance, $tours));
        $teamName = $teamId !== -1 ? $this->teamRepository->getNameById($teamId) : null;

        View::render('pages/dashboard', [
            'username' => Session::getUsername(),
            'userId' => $userId,
            'teamId' => $teamId,
            'teamName' => $teamName,
            'tours' => $tours,
            'totalDistance' => $totalDistance
        ]);
    }

    public function addTour(): void
    {
        Session::requireLogin();

        $distance = trim($_POST['distance'] ?? '');
        $date = trim($_POST['date'] ?? '');

        if (!empty($distance) && !empty($date)) {
            $this->tourRepository->create(
                Session::getUserId(),
                (float)$distance,
                $date
            );
        }

        header("Location: /dashboard");
        exit;
    }

    public function updateTour(): void
    {
        Session::requireLogin();

        $tourId = (int)($_POST['tour_id'] ?? 0);
        $distance = trim($_POST['distance'] ?? '');
        $date = trim($_POST['date'] ?? '');

        if ($tourId > 0 && !empty($distance) && !empty($date)) {
            $tour = $this->tourRepository->findById($tourId);

            if ($tour && $tour->userId === Session::getUserId()) {
                $this->tourRepository->update($tourId, (float)$distance, $date);
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
