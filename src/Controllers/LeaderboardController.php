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
        Session::requireLogin();

        $type = $_GET['type'] ?? 'global';
        $page = (int)($_GET['page'] ?? 0);

        $viewUsers = false;
        $teamId = -1;

        if ($type === 'team') {
            $teamId = Session::getTeamId();
            $viewUsers = true;
        } elseif ($type === 'all') {
            $viewUsers = true;
        }

        $users = [];
        $teams = [];

        if ($viewUsers) {
            $users = $this->userRepository->findByTeamWithDistance($teamId, $page);
        }

        $teams = $this->teamRepository->findAllWithStats();

        View::render('pages/leaderboard', [
            'viewUsers' => $viewUsers,
            'users' => $users,
            'teams' => $teams,
            'currentType' => $type
        ]);
    }
}
