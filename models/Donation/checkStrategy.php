<?php 
require_once 'DonationStrategy.php';
class checkStrategy implements DonationStrategy{

    public function process($donorId,$amount):void{

    $conn = Database::getInstance()->getConnection();
    $sql = "INSERT INTO donations (donor_id, amount, strategy) VALUES ('$donorId', '$amount', 'checkpayment')";
    mysqli_query($conn, $sql);
    

    }

}