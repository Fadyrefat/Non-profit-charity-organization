<?php
require_once 'IDonation.php';
abstract class DonationDecorator implements IDonation{

    protected $wrappedDonation;

    public function __construct(IDonation $donation) {
        $this->wrappedDonation = $donation;
    }

    public function donate() :void{
        $this->wrappedDonation->donate();
    }

}