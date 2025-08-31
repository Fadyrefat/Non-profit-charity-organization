<?php 
require_once 'Collection.php';
require_once __DIR__ . '/../../models/Beneficiary/Beneficiary.php';
class BeneficiaryCollection implements Collection{

private array  $beneficiaries=[];

public function __construct(){
    $beneficiaries = [];
$conn = Database::getInstance()->getConnection();
$sql="SELECT * From beneficiaries";
$result=mysqli_query($conn,$sql);
    while ($row = mysqli_fetch_assoc($result)) {
        $beneficiaries[] = new Beneficiary($row['name'], $row['address'] ?? '',$row['id']);
    }
$this->beneficiaries=$beneficiaries;
}


public function createIterator():IteratorInterface{
    return new BeneficiaryIterator($this->beneficiaries);
}
}


?>