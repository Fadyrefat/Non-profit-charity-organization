<?php 
require_once 'DonationStrategy.php';
require_once __DIR__ . '/../../Inventory.php';
class VisaStrategy implements DonationStrategy{
   
    public function processpayment($donorId,$amount):void{

    $conn = Database::getInstance()->getConnection();
    $sql = "INSERT INTO moneydonations (paymentmethod, amount, donor_id) VALUES ('Visa', '$amount', '$donorId')";
    mysqli_query($conn, $sql);
    Inventory::addDonation(0,$amount,0);

    }
}