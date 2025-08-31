<?php 
require_once 'IteratorInterface.php';

class DonorIterator implements IteratorInterface{

private array $donors = [];
private $position =0;

public function __construct (array $donors){
    $this->donors=$donors;
}

public function hasnext():bool{
return $this->position < count($this->donors);
}
public function next(){
return $this->donors[$this->position++];
}

}