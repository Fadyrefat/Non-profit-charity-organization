<?php 

interface DonationStrategy{

    public function processpayment($donorId,$amount):void;
}