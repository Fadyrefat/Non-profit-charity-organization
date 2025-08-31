<?php
require_once 'Observer/ObserverInterface.php';
require_once (__DIR__ . '/../Donation/Donor.php');
require_once (__DIR__ . '/../Volunteer/Volunteer.php');
require_once (__DIR__ . '/../Beneficiary/Beneficiary.php');


class ObserverFactory {
    private $observerMap = [
        'donor' => 'Donor',
        'volunteer' => 'Volunteer',
        'beneficiary' => 'Beneficiary'
    ];

    public function createObserver($userType, $userId) {

        if (!isset($this->observerMap[$userType])) {
            throw new InvalidArgumentException("Unknown user type: $userType");
        }

        $className = $this->observerMap[$userType];
        
        if (!class_exists($className)) {
            throw new RuntimeException("Class $className does not exist");
        }

        $observer = new $className($userId);

        
        if (!($observer instanceof ObserverInterface)) {
            throw new RuntimeException("$className does not implement ObserverInterface");
        }

        return $observer;
    }

}
