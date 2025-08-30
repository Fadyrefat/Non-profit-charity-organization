<?php
require_once __DIR__ . '/../Donation.php';
require_once __DIR__ . '/../PaymentMethods/DonationStrategy.php';

class MoneyDonation extends Donation{

private DonationStrategy $paymentmethod;
private float $amount;

public function __construct($donor_id,DonationStrategy $paymentmethod,float $amount){

parent::__construct($donor_id);
$this->paymentmethod=$paymentmethod;
$this->amount=$amount;

}

public function getstrategy():DonationStrategy{
return $this->paymentmethod;
}

public function setstrategy(DonationStrategy $paymentmethod):void{
 $this->paymentmethod=$paymentmethod;
}



public function donate ():void{

  $donor_id=$this->getDonor()->getId();

  $this->paymentmethod->processpayment($donor_id,$this->amount);
}

}