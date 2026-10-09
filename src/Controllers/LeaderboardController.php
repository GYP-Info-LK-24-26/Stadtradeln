<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Session;
use App\Core\View;
use App\Repository\TeamRepository;
use App\Repository\UserRepository;

class LeaderboardController
{
    /** page=@me: die Seite, auf der die angemeldete Person beim Aufruf steht (Link vom Dashboard) */
    public const PAGE_ME = '@me';

    private TeamRepository $teamRepository;
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->teamRepository = new TeamRepository();
        $this->userRepository = new UserRepository();
    }

    public function index(): void
    {
        $viewUsers = Request::get('type') !== 'teams';
        $page = $viewUsers ? $this->resolvePage(Request::get('page')) : 0;

        View::render('pages/leaderboard', [
            'viewUsers' => $viewUsers,
            'users' => $viewUsers ? $this->userRepository->findByTeamWithDistance(null, $page) : [],
            'teams' => $viewUsers ? [] : $this->teamRepository->findAllWithStats(),
            'currentType' => $viewUsers ? 'users' : 'teams',
            'page' => $page,
        ]);
    }

    /**
     * Seitenzahl aus dem Parameter. "@me" wird erst hier aufgelöst statt im Dashboard-Link,
     * damit der Link auch dann stimmt, wenn sich der Platz inzwischen verschoben hat.
     */
    private function resolvePage(string $page): int
    {
        if ($page !== self::PAGE_ME) {
            return max(0, (int)$page);
        }

        if (!Session::isLoggedIn()) {
            return 0;
        }

        $position = $this->userRepository->findLeaderboardPosition(Session::getUserId())['position'];

        return intdiv($position - 1, UserRepository::LEADERBOARD_PAGE_SIZE);
    }
}
