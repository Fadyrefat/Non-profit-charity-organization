<?php
require_once 'Donation.php'; 
require_once 'InKindStrategy.php';
require_once 'OnLineStrategy.php';
require_once 'CheckStrategy.php';

class DonationFactory {
    public static function createDonation($donor_id, $method, $amount) {
        // Convert string to strategy object
        switch ($method) {
            case 'OnLineDonation':
                $strategy = new OnlineStrategy();
                break;
            case 'CheckDonation':
                $strategy = new CheckStrategy();
                break;
            case 'InKindDonation':
                $strategy = new InKindStrategy();
                break;
            default:
                throw new Exception("Invalid donation method: $method");
        }

        // Return Donation object
        return new Donation($donor_id, $strategy, $amount);
    }
}