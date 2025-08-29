<?php

require_once __DIR__ . '/../VolunteerState.php';
require_once __DIR__ . '/ChampionState.php';

class LeaderState implements VolunteerState
{

    public const PERMISSIONS = [
        'lead_projects',
        'manage_volunteers',
        'access_reports',
    ];

    private Volunteer $volunteer;

    public function __construct(Volunteer $volunteer)
    {
        $this->volunteer = $volunteer;
    }

    public function checkForUpgrade(): void
    {
        if ($this->volunteer->getHours() >= 500) {
            $this->volunteer->setState(new ChampionState($this->volunteer));
            $this->volunteer->getState()->checkForUpgrade();
        }
    }

    public function getStateName(): string
    {
        return 'Leader';
    }

    public function getPermissions(): array
    {
        return self::PERMISSIONS;
    }

}
