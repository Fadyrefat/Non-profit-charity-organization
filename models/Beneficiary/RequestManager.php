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

    public function __construct(mysqli $conn) {
        $this->conn = $conn;
    }

    // ========== Add a Request ==========
    public function addRequest(BeneficiaryRequest $request) {
        // ✅ Always set state to Pending
        $request->setState(new PendingState());

        // Insert request into DB
        $this->insertRequestToDB($request);
    }


    // ========== Insert Request into Database ==========
// ========== Insert Request into Database ==========
    private function insertRequestToDB(BeneficiaryRequest $request) {
        $beneficiaryId = $request->getBeneficiary()->getId();
        $type = $request->getType();
        $number = $request->getNumber();
        $reason = $request->getReason();
        $state = (new PendingState())->getName(); // ✅ Force Pending state

        // Check if beneficiary exists
        $result = $this->conn->query("SELECT id FROM beneficiaries WHERE id = $beneficiaryId");
        if (!$result || $result->num_rows === 0) {
            echo "<script>alert('Cannot insert request: Beneficiary ID $beneficiaryId does not exist.');</script>";
            return;
        }

        $stmt = $this->conn->prepare("
            INSERT INTO requests (beneficiary_id, request_type, number, reason, state)
            VALUES (?, ?, ?, ?, ?)
        ");
        if (!$stmt) {
            echo "<script>alert('Prepare failed: " . $this->conn->error . "');</script>";
            return;
        }

        $stmt->bind_param("isiss", $beneficiaryId, $type, $number, $reason, $state);

        if (!$stmt->execute()) {
            echo "<script>alert('Execute failed: " . $stmt->error . "');</script>";
            return;
        }

        return $stmt->insert_id;
    }

    public static function getAll(string $state = 'All'): array
    {
        $conn = Database::getInstance()->getConnection();
        $sql = "
            SELECT r.id, b.name AS beneficiary_name, r.request_type, r.number, r.reason, r.state, r.created_at
            FROM requests r
            JOIN beneficiaries b ON r.beneficiary_id = b.id
        ";

        if ($state !== 'All') {
            $stmt = $conn->prepare($sql . " WHERE r.state = ?");
            $stmt->bind_param("s", $state);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $conn->query($sql);
        }

        $items = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        return $items;
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

    // ----------------------------
    // Complete a Request
    // ----------------------------
    private array $observers = []; // ✅ Observers list

    // ✅ Attach an observer
    public function attach(Observer $observer) {
        $this->observers[] = $observer;
    }

    // ✅ Notify all observers
    private function notifyObservers(int $requestId, string $type, int $number) {
        foreach ($this->observers as $observer) {
            $observer->update($requestId, $type, $number);
        }
    }
    public function completeRequest(int $requestId): string {
        if ($requestId <= 0) {
            return "Invalid request ID: $requestId";
        }

        // Fetch request info
        $stmt = $this->conn->prepare("
            SELECT id, request_type, number, state
            FROM requests
            WHERE id = ?
            LIMIT 1
        ");
        $stmt->bind_param("i", $requestId);
        $stmt->execute();
        $result = $stmt->get_result();
        $requestData = $result->fetch_assoc();
        $stmt->close();

        if (!$requestData) {
            return "Request not found with ID $requestId";
        }

        if ($requestData['state'] !== 'Approved') {
            return "Only approved requests can be completed. Current state: {$requestData['state']}";
        }

        $type = $requestData['request_type'];
        $number = (int)$requestData['number'];

        // Check inventory
        if (!$this->canFulfill($type, $number)) {
            return "Cannot complete request: insufficient inventory for $type.";
        }

        // Deduct inventory
        $this->deductInventory($type, $number);

        // Update request state to Completed
        $updateStmt = $this->conn->prepare("UPDATE requests SET state = ? WHERE id = ?");
        $state = (new CompletedState())->getName();
        $updateStmt->bind_param("si", $state, $requestId);
        $updateStmt->execute();
        $updateStmt->close();

        // ✅ Notify ImpactTracker
        $this->notifyObservers($requestId, $type, $number);

        return "Request ID $requestId has been completed successfully.";
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



}
