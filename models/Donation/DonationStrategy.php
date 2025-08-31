<?php 

interface DonationStrategy{

    public function process($donorId,$amount):void;
}