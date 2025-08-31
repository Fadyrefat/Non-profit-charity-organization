<?php
require_once __DIR__ . '/../Donation.php';
require_once __DIR__ . '/../../Inventory.php';
class FoodDonation extends Donation{

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
$sql = "INSERT INTO fooddonations (description, amount, donor_id) 
             VALUES ('{$this->description}', {$this->amount}, {$donor_id})";

mysqli_query($conn, $sql);
Inventory::addDonation($this->amount,0.0,0);
}

public function getAmount(): int {
        return $this->amount;
    }

public function getDescription(): string {
        return $this->description;
    }

}

