<?php

class UserData{
    public $userID;
    public $userName;
    public $teamID;

    public function __construct($userID, $userName, $teamID) {
        $this->userID = $userID;
        $this->userName = $userName;
        $this->teamID = $teamID;
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
    public $teamID;
    public $teamName;
    public $teamMemberCount;
    public $teamTotalDistance;

    public function __construct($teamID,$teamName,$teamMemberCount){
        $this->teamID = $teamID;
        $this->teamName = $teamName;
        $this->teamMemberCount = $teamMemberCount;
        $this->teamTotalDistance = 0;
    }
}