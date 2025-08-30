<?php
require_once 'IDonation.php';
class Donation implements IDonation {
    private $id;
    private $donorId;
    private $amount;
    private $method;


    public function __construct($donorId, $method, $amount) {
        $this->donorId = $donorId;
        $this->method = $method;   // should be an object like OnlineStrategy
        $this->amount = $amount;
    }

    // Getters
    public function getId() {
        return $this->id;
    }
    public function getDonorId() {
        return $this->donorId;
    }
    public function getAmount() {
        return $this->amount;
    }
    public function getMethod() {
        return $this->method;
    }
    public function getCreatedAt() {
        return $this->createdAt;
    }

    // Setters
    public function setDonorId($donorId) {
        $this->donorId = $donorId;
    }
    public function setAmount($amount) {
        $this->amount = $amount;
    }
    public function setMethod($method) {
        $this->method = $method;
    }

        public function donate() :void{
           $this->method->process($this->donorId,$this->amount);
    }
}
