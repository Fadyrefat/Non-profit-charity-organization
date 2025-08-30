<?php
require_once __DIR__ . "/PendingState.php";

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
        return $this->requestState->getName();
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

    // ========== Insert into DB ==========
    public function insert($conn) {
        // prepare
        $stmt = $conn->prepare("
            INSERT INTO requests (beneficiary_id, request_type, number, reason, state) 
            VALUES (?, ?, ?, ?, ?)
        ");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        $beneficiaryId = (int)$this->beneficiary->getId();
        if ($beneficiaryId <= 0) {
            throw new Exception("Invalid beneficiary id (null/0).");
        }

        $requestType = $this->getType();
        $number      = $this->number !== null ? (int)$this->number : 1; // default 1 if empty
        $reason      = $this->reason ?? '';
        $state       = $this->getStatus();

        // types: i = int, s = string, i for number
        $stmt->bind_param("isiss", $beneficiaryId, $requestType, $number, $reason, $state);

        if (!$stmt->execute()) {
            $err = $stmt->error;
            $stmt->close();
            throw new Exception("Database insert failed: " . $err);
        }

        $insertId = $conn->insert_id; // connection property
        $stmt->close();

        return $insertId;
    }


    // ========== Abstract ==========
    abstract protected function setRequestType();
}
