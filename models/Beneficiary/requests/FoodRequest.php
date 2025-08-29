<?php

require_once "../BeneficiaryRequest.php";

class FoodRequest extends BeneficiaryRequest
{
    private $itemsNeeded;
    private $urgencyLevel;

    // Updated constructor: Now accepts a Beneficiary object
    public function __construct($beneficiary, $itemsNeeded, $urgencyLevel)
    {
        parent::__construct($beneficiary); // Pass the Beneficiary object to parent
        $this->itemsNeeded = $itemsNeeded;
        $this->urgencyLevel = $urgencyLevel;
    }

    protected function setRequestType()
    {
        $this->requestType = "Food";
    }

    // Getters and setters
    public function getItemsNeeded() {
        return $this->itemsNeeded;
    }

    public function setItemsNeeded($itemsNeeded) {
        $this->itemsNeeded = $itemsNeeded;
    }

    public function getUrgencyLevel() {
        return $this->urgencyLevel;
    }

    public function setUrgencyLevel($urgencyLevel) {
        $this->urgencyLevel = $urgencyLevel;
    }
}
?>