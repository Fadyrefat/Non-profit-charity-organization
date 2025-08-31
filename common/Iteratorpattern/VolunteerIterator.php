<?php 
require_once 'IteratorInterface.php';

class VolunteerIterator implements IteratorInterface{

private SplDoublyLinkedList $volunteers;
private $position =0;

    public function __construct(SplDoublyLinkedList $volunteers) {
        $this->volunteers = $volunteers;
    }

public function hasnext():bool{
return $this->position < $this->volunteers->count();
}
public function next(){
return $this->volunteers->offsetGet($this->position++);
}

}