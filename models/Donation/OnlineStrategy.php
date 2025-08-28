<?php 
require_once 'DonationStrategy.php';
class OnlineStrategy implements DonationStrategy{
    
    public function process($donorId,$amount):void{

    $conn = Database::getInstance()->getConnection();
    $sql = "INSERT INTO donations (donor_id, amount, strategy) VALUES ('$donorId', '$amount', 'onlinepayment')";
    mysqli_query($conn, $sql);
    

    }

}