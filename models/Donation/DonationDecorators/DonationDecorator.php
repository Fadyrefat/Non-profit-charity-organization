<?php
require_once 'IDonation.php';
abstract class DonationDecorator implements IDonation{

    protected $wrappedDonation;
    protected $data;

    public function __construct(IDonation $donation,$data) {
        $this->wrappedDonation = $donation;
        $this->data=$data;
    }

    public function donate() :void{
        $this->wrappedDonation->donate();
    }

}