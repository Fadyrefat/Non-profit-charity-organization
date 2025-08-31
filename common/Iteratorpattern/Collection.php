<?php 

require_once 'IteratorInterface.php';

interface Collection{

    public function createIterator():IteratorInterface;
}
?>