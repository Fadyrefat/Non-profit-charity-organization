<?php

require_once __DIR__ . '/../VolunteerState.php';
require_once __DIR__ . '/SupporterState.php';

class ContributorState implements VolunteerState
{

    public const PERMISSIONS = [
        'create_events',
        'edit_content',
        'view_reports',
    ];

    private Volunteer $volunteer;

    public function __construct(Volunteer $volunteer)
    {
        $this->volunteer = $volunteer;
    }

    public function checkForUpgrade(): void
    {
        if ($this->volunteer->getHours() >= 100) {
            $this->volunteer->setState(new SupporterState($this->volunteer));
            $this->volunteer->getState()->checkForUpgrade();
        }
    }

    public function getStateName(): string
    {
        return 'Contributor';
    }

    public function getPermissions(): array
    {
        return self::PERMISSIONS;
    }

}
