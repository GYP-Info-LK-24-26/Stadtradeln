<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Session;
use App\Core\View;
use App\Models\Team;
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

        if ($teamId === null) {
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
        $members = $this->userRepository->findByTeamWithDistance($teamId);
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

        $showCreate = Request::get('type') === 'create';
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
        $teamName = trim(Request::post('team_name'));
        $isCreate = Request::post('type') === 'create';
        $userId = Session::getUserId();

        if (Session::getTeamId() !== null) {
            $error = 'Du bist schon in einem Team';
        } elseif (empty($teamName)) {
            $error = 'Du musst einen Teamnamen eingeben';
        } else {
            $existingId = $this->teamRepository->getIdByName($teamName);

            if ($isCreate) {
                if ($existingId !== null) {
                    $error = 'Es gibt bereits ein Team mit diesem Namen';
                } elseif (mb_strlen($teamName) > Team::NAME_MAX_LENGTH) {
                    $error = 'Der Teamname darf höchstens ' . Team::NAME_MAX_LENGTH . ' Zeichen lang sein';
                } else {
                    try {
                        $teamId = $this->teamRepository->create($teamName, $userId);
                        $this->userRepository->updateTeam($userId, $teamId);
                        Session::setTeamId($teamId);

                        header("Location: /team");
                        exit;
                    } catch (\Exception $e) {
                        $error = 'Interner Fehler';
                    }
                }
            } else {
                if ($existingId === null) {
                    $error = 'Dieses Team existiert nicht';
                } else {
                    $this->userRepository->updateTeam($userId, $existingId);
                    Session::setTeamId($existingId);

                    header("Location: /team");
                    exit;
                }
            }
        }

        $teams = $this->teamRepository->findAllWithStats();
        View::render('pages/team-join', [
            'showCreate' => $isCreate,
            'teams' => $teams,
            'error' => $error,
            'teamName' => $teamName
        ]);
    }

    public function leave(): void
    {
        Session::requireLogin();

        $userId = Session::getUserId();
        $teamId = Session::getTeamId();

        if ($teamId === null) {
            header("Location: /team");
            exit;
        }

        $team = $this->teamRepository->findById($teamId);
        $wasLeader = $team !== null && $team->teamleiterId === $userId;

        // Remove user from team
        $this->userRepository->updateTeam($userId, null);
        Session::setTeamId(null);

        // Delete the team if it's now empty; otherwise, if the Teamleiter left,
        // promote the remaining member with the most kilometers.
        if (!$this->teamRepository->deleteIfEmpty($teamId) && $wasLeader) {
            $members = $this->userRepository->findByTeamWithDistance($teamId);
            if (!empty($members)) {
                $this->teamRepository->updateLeader($teamId, $members[0]->id);
            }
        }

        header("Location: /team");
        exit;
    }

    public function updateName(): void
    {
        Session::requireLogin();

        $userId = Session::getUserId();
        $teamId = Session::getTeamId();
        $newName = trim(Request::post('team_name'));

        if ($teamId === null) {
            header("Location: /team");
            exit;
        }

        $team = $this->teamRepository->findById($teamId);

        // Only Teamleiter can rename
        if ($team === null || $team->teamleiterId !== $userId) {
            header("Location: /team");
            exit;
        }

        if (!empty($newName) && mb_strlen($newName) <= Team::NAME_MAX_LENGTH
            && $newName !== $team->name && $this->teamRepository->getIdByName($newName) === null) {
            $this->teamRepository->updateName($teamId, $newName);
        }

        header("Location: /team");
        exit;
    }

    public function transferLeader(): void
    {
        Session::requireLogin();

        $userId = Session::getUserId();
        $teamId = Session::getTeamId();
        $newLeaderId = (int)Request::post('new_leader');

        if ($teamId === null) {
            header("Location: /team");
            exit;
        }

        $team = $this->teamRepository->findById($teamId);

        // Only the current Teamleiter may hand over the role
        if ($team === null || $team->teamleiterId !== $userId) {
            header("Location: /team");
            exit;
        }

        // The new leader must be another member of the same team
        $newLeader = $this->userRepository->findById($newLeaderId);
        if ($newLeader !== null && $newLeader->teamId === $teamId && $newLeaderId !== $userId) {
            $this->teamRepository->updateLeader($teamId, $newLeaderId);
        }

        header("Location: /team");
        exit;
    }
}
