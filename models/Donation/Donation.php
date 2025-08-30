<?php

require_once __DIR__ . '/DonationDecorators/IDonation.php';
require_once 'Donor.php';

abstract class Donation implements IDonation {
    private $id;
    private Donor $donor;
    

    public function __construct($donor_id) {
    $this->donor=Donor::getDonorbyID($donor_id);}

    public function getId() {
        return $this->id;
    }
    public function getDonor(){
        return $this->donor;
    }
 
    public function setDonor($donor) {
        $this->donor = $donor;
    }


    abstract public function donate(): void;
}