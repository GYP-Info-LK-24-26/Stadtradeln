<?php

namespace App\Controllers;

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
        $page = (int)($_GET['page'] ?? 0);
        $viewUsers = (($_GET['type'] ?? 'users') !== 'teams');

        View::render('pages/leaderboard', [
            'viewUsers' => $viewUsers,
            'users' => $viewUsers ? $this->userRepository->findByTeamWithDistance(null, $page) : [],
            'teams' => $this->teamRepository->findAllWithStats(),
            'currentType' => $viewUsers ? 'users' : 'teams',
        ]);
    }
}
