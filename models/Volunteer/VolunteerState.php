<?php

interface VolunteerState
{
    public function __construct(Volunteer $volunteer);

    public function checkForUpgrade(): void;

    public function getStateName(): string;

    public function getPermissions(): array;

}
