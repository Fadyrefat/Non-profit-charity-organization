<?php
require_once __DIR__ . "/../states/PendingState.php";
require_once __DIR__ . "/../states/RequestState.php";
require_once __DIR__ . "/../Beneficiary.php";

// ===================== Abstract Beneficiary Request =====================
abstract class BeneficiaryRequest
{
    protected int $requestId;
    protected RequestState $requestState;
    protected string $requestType;
    protected Beneficiary $beneficiary;
    protected int $number;
    protected string $reason;
    protected mysqli $conn;

    public function __construct(Beneficiary $beneficiary, int $number = 1, string $reason = '', mysqli $conn = null, int $requestId = 0)
    {
        if (!$beneficiary->getId()) {
            throw new Exception("Beneficiary must have a valid ID before creating a request.");
        }

        $this->beneficiary = $beneficiary;
        $this->number = $number;
        $this->reason = $reason;
        $this->requestState = new PendingState();
        $this->setRequestType();
        $this->conn = $conn;
        $this->requestId = $requestId;
    }

    public function setConnection(mysqli $conn): void
    {
        $this->conn = $conn;
    }

    abstract protected function setRequestType(): void;

    // ---------------- State Transitions ----------------
    public function approve(): void
    {
        $this->requestState->approve($this);
        $this->updateStateInDB();
    }

    public function reject(): void
    {
        $this->requestState->reject($this);
        $this->updateStateInDB();
    }

    public function complete(): void
    {
        $this->requestState->complete($this);
        $this->updateStateInDB();
    }

    public function setState(RequestState $state): void
    {
        $this->requestState = $state;
    }

    public function getState(): RequestState
    {
        return $this->requestState;
    }

    public function getStatus(): string
    {
        return $this->requestState->getName();
    }

    public function getType(): string
    {
        return $this->requestType;
    }

    public function getBeneficiary(): Beneficiary
    {
        return $this->beneficiary;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function setNumber(int $number): void
    {
        $this->number = $number;
    }

    public function getReason(): string
    {
        return $this->reason;
    }

    public function setReason(string $reason): void
    {
        $this->reason = $reason;
    }

    public function setRequestId(int $id): void
    {
        $this->requestId = $id;
    }

    public function getRequestId(): int
    {
        return $this->requestId;
    }

    // ---------------- Update State in Database ----------------
    protected function updateStateInDB(): void
    {
        if (!$this->conn || $this->requestId <= 0) return;

        $stmt = $this->conn->prepare("UPDATE requests SET state = ? WHERE id = ?");
        $state = $this->getStatus();
        $stmt->bind_param("si", $state, $this->requestId);
        $stmt->execute();
        $stmt->close();
    }
}
