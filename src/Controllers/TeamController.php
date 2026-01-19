<?php

namespace App\Controllers;

use App\Core\Session;
use App\Core\View;
use App\Repository\TeamRepository;
use App\Repository\TourRepository;
use App\Repository\UserRepository;

class TeamController
{
    private TeamRepository $teamRepository;
    private UserRepository $userRepository;
    private TourRepository $tourRepository;

    public function __construct()
    {
        $this->teamRepository = new TeamRepository();
        $this->userRepository = new UserRepository();
        $this->tourRepository = new TourRepository();
    }

    public function index(): void
    {
        Session::requireLogin();

        $teamId = Session::getTeamId();
        $userId = Session::getUserId();

        if ($teamId === -1) {
            View::render('pages/team', [
                'hasTeam' => false,
                'team' => null,
                'members' => [],
                'stats' => null,
                'userId' => $userId
            ]);
            return;
        }

        $team = $this->teamRepository->findById($teamId);
        $members = $this->userRepository->findByTeam($teamId);
        $stats = $this->tourRepository->getStatsForTeam($teamId);

        View::render('pages/team', [
            'hasTeam' => true,
            'team' => $team,
            'members' => $members,
            'stats' => $stats,
            'userId' => $userId
        ]);
    }

    public function showJoinCreate(): void
    {
        Session::requireLogin();

        $showCreate = ($_GET['type'] ?? '') === 'create' || ($_POST['type'] ?? '') === 'create';
        $teams = $this->teamRepository->findAllWithStats();

        View::render('pages/team-join', [
            'showCreate' => $showCreate,
            'teams' => $teams,
            'error' => ''
        ]);
    }

    public function joinOrCreate(): void
    {
        Session::requireLogin();

        $error = '';
        $teamName = trim($_POST['team_name'] ?? '');
        $isCreate = ($_POST['type'] ?? '') === 'create';
        $userId = Session::getUserId();

        if (Session::getTeamId() !== -1) {
            $error = 'Du bist schon in einem Team';
        } elseif (empty($teamName)) {
            $error = 'Du musst einen Teamnamen eingeben';
        } else {
            $teamExists = $this->teamRepository->exists($teamName);

            if ($isCreate) {
                if ($teamExists) {
                    $error = 'Es gibt bereits ein Team mit diesem Namen';
                } else {
                    try {
                        $teamId = $this->teamRepository->create(htmlspecialchars($teamName), $userId);
                        $this->userRepository->updateTeam($userId, $teamId);
                        Session::setTeamId($teamId);
                        
                        header("Location: /dashboard");
                        exit;
                    } catch (\Exception $e) {
                        $error = 'Interner Fehler';
                    }
                }
            } else {
                // Join existing team
                if (!$teamExists) {
                    $error = 'Dieses Team existiert nicht';
                } else {
                    $teamId = $this->teamRepository->getIdByName($teamName);
                    $this->userRepository->updateTeam($userId, $teamId);
                    Session::setTeamId($teamId);
                    
                    header("Location: /dashboard");
                    exit;
                }
            }
        }

        $teams = $this->teamRepository->findAllWithStats();
        View::render('pages/team-join', [
            'showCreate' => $isCreate,
            'teams' => $teams,
            'error' => $error
        ]);
    }

    public function leave(): void
    {
        Session::requireLogin();

        $userId = Session::getUserId();
        $teamId = Session::getTeamId();

        if ($teamId === -1) {
            header("Location: /team");
            exit;
        }

        // Remove user from team
        $this->userRepository->updateTeam($userId, -1);
        Session::setTeamId(-1);

        // Delete team if it's now empty
        $this->teamRepository->deleteIfEmpty($teamId);

        header("Location: /team");
        exit;
    }
}
