<?php

class UserData{
    public $userID;
    public $userName;
    public $teamID;
    public $totalDistance = 0;

    public function __construct($userID, $userName, $teamID,$totalDistance) {
        $this->userID = $userID;
        $this->userName = $userName;
        $this->teamID = $teamID;
        $this->totalDistance = $totalDistance;
    }
}

class ComplexUserData extends UserData{
    public $firstName;
    public $lastName;
    public $email;
    public $password;
}

class Tour{
    public $userID;
    public $tourID;
    public $date;
    public $distance;

    public function __construct($userID,$tourID,$date,$distance){
        $this->userID = $userID;
        $this->tourID = $tourID;
        $this->date = $date;
        $this->distance = $distance;
    }
}

class TeamData{
    public $teamID = 0;
    public $teamName;
    public $teamMemberCount;
    public $teamTotalDistance;
    public $teamTotalTours;
    public $captainID;

    public function __construct($teamID,$teamName,$teamMemberCount,$captainID){
        $this->teamID = $teamID;
        $this->teamName = $teamName;
        $this->teamMemberCount = $teamMemberCount;
        $this->teamTotalDistance = 0;
        $this->captainID = $captainID;
    }

    public function countTotalDistance(): void{
        $teamTours = SQLSelector::getUserToursForTeam($this->teamID);

        $this->teamTotalTours = count($teamTours);
        foreach($teamTours as $teamTour) {
            $this->teamTotalDistance += $teamTour->distance;
        }
    }
}