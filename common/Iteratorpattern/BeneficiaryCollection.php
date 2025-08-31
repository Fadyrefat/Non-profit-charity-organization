<?php 
/*require_once 'Colection';

class BeneficiaryCollection implements Collection{

private array  $beneficiaries=[];

public function __construct(){

$sql="SELECT * From beneficiaries";
$result=mysqli_query($conn,$sql);
    while ($row = mysqli_fetch_assoc($result)) {
        $donors[] = new Donor($row['id'],$row['name'],$row['email'],$row['phone']);
    }
$this->beneficiaries=$beneficiaries;
}


public function createIterator():Iterator{
    return BeneficiaryIterator($this->beneficiaries);
}
}


?>