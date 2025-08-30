<?php
require_once __DIR__ . '/../Donation.php';

class CompositeDonation extends Donation{

 private array $donations = [];

public function __construct($donor_id){
    parent::__construct($donor_id);
} 

     public function add(Donation $donation) {
        $this->donations[] = $donation;
    }
    public function donate(): void {

        foreach ($this->donations as $donation) {
        $donation->donate();
        }
        
    }

}