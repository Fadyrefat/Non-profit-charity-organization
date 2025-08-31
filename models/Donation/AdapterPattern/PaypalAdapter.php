<?php
require_once __DIR__ . '/../PaymentMethods/DonationStrategy.php';
require_once 'PaypalService.php';
class PaypalAdapter implements DonationStrategy{

private PaypalService $paypalService;

public function __construct (PaypalService $paypalService){
    $this->paypalService=$paypalService;
}

public function processpayment($donorId,$amount):void{

   $userCode=$donorId;
   $money=$amount;
   $this->paypalService->sendPayment($userCode,$money);
    }

}