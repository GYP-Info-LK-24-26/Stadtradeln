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
        Session::start();
        $isLoggedIn = Session::isLoggedIn();

        $type = $_GET['type'] ?? 'users';
        $page = (int)($_GET['page'] ?? 0);

        // "my-team" requires login
        if ($type === 'my-team' && !$isLoggedIn) {
            header('Location: /login?redirect=' . urlencode('/leaderboard?type=my-team'));
            exit;
        }

        $viewUsers = false;
        $teamId = null;

        if ($type === 'users') {
            $viewUsers = true;
        } elseif ($type === 'my-team') {
            $teamId = Session::getTeamId();
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
            'currentType' => $type,
            'isLoggedIn' => $isLoggedIn
        ]);
    }
}
