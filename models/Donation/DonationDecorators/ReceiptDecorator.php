<?php 
require_once 'DonationDecorator.php';
require_once __DIR__ . '/../Donor.php';
class ReceiptDecorator extends DonationDecorator{
    public function donate():void{
        parent::donate();
        $this->generateReceipt();

    }
  
        private function generateReceipt(): void {

        $data = $this->data;
        
        $doner_id=$data['donor_id'];
        $doner=Donor::getDonorByID($doner_id);
        $donorName=$doner->getName();

if (!empty($data['money'])) {
    $money = $data['money_amount'];
} else {
    $money = 0.0;
}

if (!empty($data['food'])) {
    $foods = $data['food_amount'];
} else {
    $foods = 0;
}

if (!empty($data['clothes'])) {
    $clothes = $data['clothes_amount'];
} else {
    $clothes = 0;
}

        
        $conn=Database::getInstance()->getConnection();
        $sql="INSERT INTO receipts (doner_id,doner_name,money,clothes,foods) VALUES ('$doner_id','$donorName','$money','$foods','$clothes')";
        mysqli_query($conn, $sql);
    }
}