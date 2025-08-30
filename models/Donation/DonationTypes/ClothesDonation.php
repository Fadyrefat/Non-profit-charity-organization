<?php
require_once __DIR__ . '/../Donation.php';

class ClothesDonation extends Donation{

private string $description;
private int $amount;

public function __construct($donor_id,string $description,int $amount) {
        parent::__construct($donor_id);
        $this->amount = $amount;
        $this->description=$description;
    }

public function donate ():void{

$conn = Database::getInstance()->getConnection();
$donor=parent::getDonor();
$donor_id=$donor->getId();
$sql = "INSERT INTO clothesdonations (description, amount, donor_id) 
             VALUES ('{$this->description}', {$this->amount}, {$donor_id})";

mysqli_query($conn, $sql);
}
public function getAmount(): float {
        return $this->amount;
    }

public function getDescription(): string {
        return $this->description;
    }

}