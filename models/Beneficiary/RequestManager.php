<?php

require_once "ClothesRequest.php";
require_once "FoodRequest.php";
require_once "FinancialRequest.php";
require_once "ApprovedState.php";
require_once "RejectedState.php";
require_once "CompletedState.php";
require_once "PendingState.php";

class RequestManager {
    private $conn;
    private $pendingList = [];
    private $approvedList = [];
    private $rejectedList = [];
    private $completedList = [];

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // ========== Add a Request ==========
    public function addRequest(BeneficiaryRequest $request, $limit) {
        // Check if number <= limit
        if ($request->getNumber() <= $limit) {
            $request->setState(new ApprovedState());
            $this->approvedList[] = $request;
        } else {
            $request->setState(new RejectedState());
            $this->rejectedList[] = $request;
        }
    }

    // ========== Try Completing Approved Requests ==========
    public function processRequests() {
        foreach ($this->approvedList as $key => $request) {
            $type = $request->getType();
            $number = $request->getNumber();

            if ($this->canFulfill($type, $number)) {
                // Deduct from inventory
                $this->deductInventory($type, $number);

                // Mark as completed
                $request->setState(new CompletedState());
                $this->completedList[] = $request;

                // Remove from approved list
                unset($this->approvedList[$key]);
            }
        }
    }

    // ========== Check if inventory can fulfill ==========
    private function canFulfill($type, $number) {
        $result = mysqli_query($this->conn, "SELECT * FROM inventory LIMIT 1");
        $inventory = mysqli_fetch_assoc($result);

        if ($type === "Food" && $inventory["food"] >= $number) return true;
        if ($type === "Clothes" && $inventory["clothes"] >= $number) return true;
        if ($type === "Financial" && $inventory["money"] >= $number) return true;

        return false;
    }

    // ========== Deduct inventory ==========
    private function deductInventory($type, $number) {
        if ($type === "Food") {
            mysqli_query($this->conn, "UPDATE inventory SET food = food - $number WHERE id = 1");
        } elseif ($type === "Clothes") {
            mysqli_query($this->conn, "UPDATE inventory SET clothes = clothes - $number WHERE id = 1");
        } elseif ($type === "Financial") {
            mysqli_query($this->conn, "UPDATE inventory SET money = money - $number WHERE id = 1");
        }
    }

    // ========== Getters for lists ==========
    public function getPendingRequests() { return $this->pendingList; }
    public function getApprovedRequests() { return $this->approvedList; }
    public function getRejectedRequests() { return $this->rejectedList; }
    public function getCompletedRequests() { return $this->completedList; }
}
?>
