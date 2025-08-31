<?php

require_once __DIR__ . '/../VolunteerState.php';

class ChampionState implements VolunteerState
{

    public const PERMISSIONS = [
        'manage_team',
        'access_reports',
        'approve_events',
    ];

    private Volunteer $volunteer;

public function __construct(Volunteer $volunteer = null) {
    if ($volunteer) {
        $this->volunteer = $volunteer;
    }
}

    public function setVolunteer(Volunteer $volunteer) {
        $this->volunteer = $volunteer;
    }

    public function checkForUpgrade(): void
    {
        // Highest State for now
        // This method does nothing but needs to exist for interface
    }

    public function getStateName(): string
    {
        return 'Champion';
    }

    public function getPermissions(): array
    {
        return self::PERMISSIONS;
    }

}
