<?php

require_once "RequestState.php";
require_once "PendingState.php";
require_once "Beneficiary.php"; // Include the Beneficiary class

abstract class BeneficiaryRequest {
    protected $requestState;
    protected $requestType;
    protected $beneficiary; // Association: Each request has one beneficiary

    public function __construct(Beneficiary $beneficiary) {
        $this->beneficiary = $beneficiary; // Store the beneficiary object
        $this->requestState = new PendingState();
        $this->setRequestType();
    }

    public function getState(): RequestState {
        return $this->requestState;
    }

    // State management methods
    public function approve() {
        $this->requestState->approve($this);
    }

    public function reject() {
        $this->requestState->reject($this);
    }

    public function complete() {
        $this->requestState->complete($this);
    }

    public function getStatus(): string {
        return $this->requestState->getStateName();
    }

    public function getType(): string {
        return $this->requestType;
    }

    // Getter for the associated beneficiary
    public function getBeneficiary(): Beneficiary {
        return $this->beneficiary;
    }

    // Optional: Setter if you need to change the beneficiary later
    public function setBeneficiary(Beneficiary $beneficiary) {
        $this->beneficiary = $beneficiary;
    }

    // Abstract method to be implemented by child classes
    abstract protected function setRequestType();
}
?>