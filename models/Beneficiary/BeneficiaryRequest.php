<?php

require_once "RequestState.php";
require_once "PendingState.php";
require_once "Beneficiary.php";

abstract class BeneficiaryRequest {
    protected $requestState;
    protected $requestType;
    protected $beneficiary;

    // Common attributes for all requests
    protected $number;
    protected $reason;

    public function __construct(Beneficiary $beneficiary, $number, $reason) {
        $this->beneficiary = $beneficiary;
        $this->number = $number;
        $this->reason = $reason;
        $this->requestState = new PendingState(); // default state
        $this->setRequestType();
    }

    // ========== State Handling ==========
    public function setState(RequestState $state) {
        $this->requestState = $state;
    }

    public function getStatus(): string {
        return $this->requestState->getStateName();
    }

    public function getType(): string {
        return $this->requestType;
    }

    // ========== Beneficiary Association ==========
    public function getBeneficiary(): Beneficiary {
        return $this->beneficiary;
    }

    // ========== Common Attributes ==========
    public function getNumber() {
        return $this->number;
    }

    public function setNumber($number) {
        $this->number = $number;
    }

    public function getReason() {
        return $this->reason;
    }

    public function setReason($reason) {
        $this->reason = $reason;
    }

    // ========== Abstract ==========
    abstract protected function setRequestType();
}
?>
