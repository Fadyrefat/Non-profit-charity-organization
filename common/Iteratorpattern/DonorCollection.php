<?php 
require_once 'Collection.php';
require_once __DIR__ . '/../../models/Donation/Donor.php';
class DonorCollection implements Collection{

private array $donors = [];

public function __construct(){

$conn = Database::getInstance()->getConnection();
$sql="SELECT * From donors";
$result=mysqli_query($conn,$sql);
    while ($row = mysqli_fetch_assoc($result)) {
        $donors[] = new Donor($row['id'],$row['name'],$row['email'],$row['phone']);
    }
$this->donors=$donors;
}

public function createIterator():IteratorInterface{
    return new DonorIterator($this->donors);
}



}
?>