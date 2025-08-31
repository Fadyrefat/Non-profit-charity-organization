<?php 
require_once __DIR__ . '/../../Inventory.php';
class PaypalService {

public function sendPayment($userCode,$money){

    $conn = Database::getInstance()->getConnection();
    $sql = "INSERT INTO moneydonations (paymentmethod, amount, donor_id) VALUES ('Paypal', '$money', '$userCode')";
    mysqli_query($conn, $sql);
    Inventory::addDonation(0,$money,0);

}

}