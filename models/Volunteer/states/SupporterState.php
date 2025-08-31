<?php

require_once __DIR__ . '/../VolunteerState.php';
require_once __DIR__ . '/LeaderState.php';

class SupporterState implements VolunteerState
{

    public const PERMISSIONS = [
        'organize_events',
        'mentor_helpers',
        'view_reports',
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
        if ($this->volunteer->getHours() >= 200) {
            $this->volunteer->setState(new LeaderState($this->volunteer));
            $this->volunteer->getState()->checkForUpgrade();
        }
    }

    public function getStateName(): string
    {
        return 'Supporter';
    }

    public function getPermissions(): array
    {
        return self::PERMISSIONS;
    }

}
