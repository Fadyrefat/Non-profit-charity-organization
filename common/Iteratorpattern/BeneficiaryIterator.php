<?php 
/*require_once 'Iterator';

class BeneficiaryIterator implements Iterator{

private array $beneficiaries = [];
private $position =0;

public function __construct (array $beneficiaries){
    $this->beneficiaries=$beneficiaries;
}

public function hasnext():bool{
return $this->position < count($this->beneficiaries);
}
public function next(){
return $this->beneficiaries[$this->position++];
}

}