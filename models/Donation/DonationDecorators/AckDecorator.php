<?php 
require_once 'DonationDecorator.php';
require_once __DIR__ . '/../Donor.php';
class AckDecorator extends DonationDecorator{
    public function donate():void {
        parent::donate();
        $this->sendAcknowledgement();
    }

    private function sendAcknowledgement(){

        $data = $this->data;
        $doner_id=$data['donor_id'];
        $doner=Donor::getDonorByID($doner_id);
        $donorName=$doner->getName();
        $message="Thank you, {$donorName}, for your generous donation!";

        $conn=Database::getInstance()->getConnection();
        $sql="INSERT INTO acknowledgment (message) VALUES ('$message')";
        mysqli_query($conn, $sql);
    }

}