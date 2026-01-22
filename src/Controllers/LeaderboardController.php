<?php

namespace App\Controllers;

use App\Core\Session;
use App\Core\View;
use App\Repository\TeamRepository;
use App\Repository\UserRepository;

class LeaderboardController
{
    private TeamRepository $teamRepository;
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->teamRepository = new TeamRepository();
        $this->userRepository = new UserRepository();
    }

    public function index(): void
    {
        $isLoggedIn = Session::isLoggedIn();
        $type = $_GET['type'] ?? 'users';
        $page = (int)($_GET['page'] ?? 0);

        if ($type === 'my-team' && !$isLoggedIn) {
            header('Location: /login?redirect=' . urlencode('/leaderboard?type=my-team'));
            exit;
        }

        $viewUsers = ($type === 'users' || $type === 'my-team');
        $teamId = ($type === 'my-team') ? Session::getTeamId() : null;

        View::render('pages/leaderboard', [
            'viewUsers' => $viewUsers,
            'users' => $viewUsers ? $this->userRepository->findByTeamWithDistance($teamId, $page) : [],
            'teams' => $this->teamRepository->findAllWithStats(),
            'currentType' => $type,
            'isLoggedIn' => $isLoggedIn
        ]);
    }
}
