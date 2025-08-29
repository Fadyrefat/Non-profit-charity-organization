<?php

require_once __DIR__ . '/../VolunteerState.php';

class HelperState implements VolunteerState
{

    public const PERMISSIONS = [
        'assist_events',
        'view_content',
    ];

    private Volunteer $volunteer;

    public function __construct(Volunteer $volunteer)
    {
        $this->volunteer = $volunteer;
    }

    public function checkForUpgrade(): void
    {
        if ($this->volunteer->getHours() >= 50) {
            $this->volunteer->setState(new ContributorState($this->volunteer));
            $this->volunteer->getState()->checkForUpgrade();
        }
    }

    public function getStateName(): string
    {
        return 'Helper';
    }

    public function getPermissions(): array
    {
        return self::PERMISSIONS;
    }

}
