<?php

require_once "../BeneficiaryRequest.php";

class FinancialRequest extends BeneficiaryRequest
{
    private $amount;
    private $purpose;

    // Updated constructor: Now accepts a Beneficiary object
    public function __construct($beneficiary, $amount, $purpose)
    {
        parent::__construct($beneficiary); // Pass the Beneficiary object to parent
        $this->amount = $amount;
        $this->purpose = $purpose;
    }

    protected function setRequestType()
    {
        $this->requestType = "Financial";
    }

    // Getters and setters
    public function getAmount() {
        return $this->amount;
    }

    public function setAmount($amount) {
        $this->amount = $amount;
    }

    public function getPurpose() {
        return $this->purpose;
    }

    public function setPurpose($purpose) {
        $this->purpose = $purpose;
    }
}
?>