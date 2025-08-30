<?php
require_once __DIR__ . "/../states/PendingState.php";
require_once __DIR__ . "/../states/RequestState.php";
require_once __DIR__ . "/../Beneficiary.php";

abstract class BeneficiaryRequest {
    protected RequestState $requestState;
    protected string $requestType;
    protected Beneficiary $beneficiary;

    protected int $number;
    protected string $reason;

    public function __construct(Beneficiary $beneficiary, int $number = 1, string $reason = '') {
        if (!$beneficiary->getId()) {
            throw new Exception("Beneficiary must have a valid ID before creating a request.");
        }

        $this->beneficiary = $beneficiary;
        $this->number = $number;
        $this->reason = $reason;
        $this->requestState = new PendingState(); // default state
        $this->setRequestType();
    }

    abstract protected function setRequestType(): void;

    // ========== State Handling ==========
    public function setState(RequestState $state): void {
        $this->requestState = $state;
    }

    public function getStatus(): string {
        return $this->requestState->getName();
    }

    public function getType(): string {
        return $this->requestType;
    }

    public function getBeneficiary(): Beneficiary {
        return $this->beneficiary;
    }

    public function getNumber(): int {
        return $this->number;
    }

    public function setNumber(int $number): void {
        $this->number = $number;
    }

    public function getReason(): string {
        return $this->reason;
    }

    public function setReason(string $reason): void {
        $this->reason = $reason;
    }

    // ========== Database Storage ==========
    public function insert(mysqli $conn): int {
        $beneficiaryId = $this->beneficiary->getId();

        // ✅ Check if beneficiary exists in the database
        $result = $conn->query("SELECT id FROM beneficiaries WHERE id = $beneficiaryId");
        if (!$result || $result->num_rows === 0) {
            throw new Exception("Cannot insert request: Beneficiary ID $beneficiaryId does not exist.");
        }

        $stmt = $conn->prepare("
            INSERT INTO requests (beneficiary_id, request_type, number, reason, state)
            VALUES (?, ?, ?, ?, ?)
        ");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        $type   = $this->getType();
        $number = $this->getNumber();
        $reason = $this->getReason();
        $state  = $this->getStatus();

        $stmt->bind_param("isiss", $beneficiaryId, $type, $number, $reason, $state);

        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        return $stmt->insert_id;
    }
}
