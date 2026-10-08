<?php

namespace App\Controllers;

use App\Core\Request;
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
        $page = max(0, (int)Request::get('page'));
        $viewUsers = Request::get('type') !== 'teams';

        View::render('pages/leaderboard', [
            'viewUsers' => $viewUsers,
            'users' => $viewUsers ? $this->userRepository->findByTeamWithDistance(null, $page) : [],
            'teams' => $viewUsers ? [] : $this->teamRepository->findAllWithStats(),
            'currentType' => $viewUsers ? 'users' : 'teams',
            'page' => $page,
        ]);
    }
}
