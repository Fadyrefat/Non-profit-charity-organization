<?php
require_once __DIR__ . "/requests/ClothesRequest.php";
require_once __DIR__ . "/requests/FoodRequest.php";
require_once __DIR__ . "/requests/FinancialRequest.php";
require_once __DIR__ . "/states/ApprovedState.php";
require_once __DIR__ . "/states/RejectedState.php";
require_once __DIR__ . "/states/CompletedState.php";
require_once __DIR__ . "/states/PendingState.php";

class RequestManager {
    private mysqli $conn;
    private array $pendingList = [];
    private array $approvedList = [];
    private array $rejectedList = [];
    private array $completedList = [];

    public function __construct(mysqli $conn) {
        $this->conn = $conn;
    }

    // ========== Add a Request ==========
    public function addRequest(BeneficiaryRequest $request, int $limit) {
        // Check if number <= limit
        if ($request->getNumber() <= $limit) {
            $request->setState(new ApprovedState());
            $this->approvedList[] = $request;
        } else {
            $request->setState(new RejectedState());
            $this->rejectedList[] = $request;
        }

        // Insert request into DB
        $this->insertRequestToDB($request);
    }

    // ========== Insert Request into Database ==========
    private function insertRequestToDB(BeneficiaryRequest $request) {
        $beneficiaryId = $request->getBeneficiary()->getId();
        $type = $request->getType();
        $number = $request->getNumber();
        $reason = $request->getReason();
        $state = $request->getStatus();

        // Check if beneficiary exists
        $result = $this->conn->query("SELECT id FROM beneficiaries WHERE id = $beneficiaryId");
        if (!$result || $result->num_rows === 0) {
            throw new Exception("Cannot insert request: Beneficiary ID $beneficiaryId does not exist.");
        }

        $stmt = $this->conn->prepare("
            INSERT INTO requests (beneficiary_id, request_type, number, reason, state)
            VALUES (?, ?, ?, ?, ?)
        ");
        if (!$stmt) throw new Exception("Prepare failed: " . $this->conn->error);

        $stmt->bind_param("isiss", $beneficiaryId, $type, $number, $reason, $state);

        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        return $stmt->insert_id;
    }




    // ========== Process Approved Requests ==========
    public function processRequests() {
        foreach ($this->approvedList as $key => $request) {
            $type = $request->getType();
            $number = $request->getNumber();

            if ($this->canFulfill($type, $number)) {
                $this->deductInventory($type, $number);
                $request->setState(new CompletedState());
                $this->completedList[] = $request;
                unset($this->approvedList[$key]);
            }
        }
    }

    // ----------------------------
    // Request State Updates
    // ----------------------------
    public function approveRequest($requestId) {
        $requestId = (int)$requestId;
        if ($requestId <= 0) {
            throw new Exception("Invalid request ID: $requestId");
        }

        // Update state in DB
        $stmt = $this->conn->prepare("UPDATE requests SET state = ? WHERE id = ?");
        $state = (new ApprovedState())->getName();
        $stmt->bind_param("si", $state, $requestId);
        $stmt->execute();
        $stmt->close();

        header("Location: index.php?action=showRequests");
        exit;
    }

    public function rejectRequest($requestId) {
        $requestId = (int)$requestId;
        if ($requestId <= 0) {
            throw new Exception("Invalid request ID: $requestId");
        }

        // Update state in DB
        $stmt = $this->conn->prepare("UPDATE requests SET state = ? WHERE id = ?");
        $state = (new RejectedState())->getName();
        $stmt->bind_param("si", $state, $requestId);
        $stmt->execute();
        $stmt->close();

        header("Location: index.php?action=showRequests");
        exit;
    }






    // ========== Check inventory ==========
    private function canFulfill(string $type, int $number): bool {
        $result = mysqli_query($this->conn, "SELECT * FROM inventory LIMIT 1");
        $inventory = mysqli_fetch_assoc($result);

        if ($type === "Food" && $inventory["food"] >= $number) return true;
        if ($type === "Clothes" && $inventory["clothes"] >= $number) return true;
        if ($type === "Financial" && $inventory["money"] >= $number) return true;

        return false;
    }

    // ========== Deduct inventory ==========
    private function deductInventory(string $type, int $number) {
        if ($type === "Food") {
            mysqli_query($this->conn, "UPDATE inventory SET food = food - $number WHERE id = 1");
        } elseif ($type === "Clothes") {
            mysqli_query($this->conn, "UPDATE inventory SET clothes = clothes - $number WHERE id = 1");
        } elseif ($type === "Financial") {
            mysqli_query($this->conn, "UPDATE inventory SET money = money - $number WHERE id = 1");
        }
    }

    // ========== Getters for lists ==========
    public function getPendingRequests(): array { return $this->pendingList; }
    public function getApprovedRequests(): array { return $this->approvedList; }
    public function getRejectedRequests(): array { return $this->rejectedList; }
    public function getCompletedRequests(): array { return $this->completedList; }
}
