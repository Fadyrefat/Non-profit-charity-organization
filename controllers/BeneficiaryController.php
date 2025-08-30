<?php

class BeneficiaryController {

    private $conn;

    public function __construct() {
        // ✅ Initialize DB connection once
        $this->conn = Database::getInstance()->getConnection();

        // Include Beneficiary model once
        require_once 'models/Beneficiary/Beneficiary.php';

        // Include Request classes once
        require_once 'models/Beneficiary/RequestFactory.php';
        require_once 'models/Beneficiary/requests/FoodRequest.php';
        require_once 'models/Beneficiary/requests/ClothesRequest.php';
        require_once 'models/Beneficiary/requests/FinancialRequest.php';

        // Include all states
        require_once 'models/Beneficiary/states/PendingState.php';
        require_once 'models/Beneficiary/states/ApprovedState.php';
        require_once 'models/Beneficiary/states/RejectedState.php';
        require_once 'models/Beneficiary/states/CompletedState.php';
    }

    // ----------------------------
    // Department index page
    // ----------------------------
    public function Index() {
        require_once 'views/Beneficiary/index.html';
    }

    // ----------------------------
    // Beneficiary CRUD
    // ----------------------------

    // Show Add Beneficiary Form
    public function addForm() {
        require_once 'views/Beneficiary/addBeneficiary.html';
    }

    // Handle Add Beneficiary (POST)
    public function addBeneficiary($data) {
        $name    = $data['name'] ?? null;
        $address = $data['address'] ?? null;

        if (!$name || !$address) {
            echo "❌ Beneficiary name and address are required.";
            return;
        }

        $beneficiary = new Beneficiary($name, $address);
        $beneficiary->insert($this->conn);

        header("Location: index.php?action=showBeneficiaries");
        exit;
    }

    // Show All Beneficiaries
    public function showAll() {
        $result = $this->conn->query("SELECT * FROM beneficiaries ORDER BY id DESC");

        $beneficiaries = [];
        while ($row = $result->fetch_assoc()) {
            $beneficiaries[] = $row;
        }

        require_once 'views/Beneficiary/showBeneficiaries.html';
    }

    // ----------------------------
    // Beneficiary Requests
    // ----------------------------

    // Show Add Request Form
    public function addRequestForm() {
        // Fetch all beneficiaries
        $beneficiaries = Beneficiary::getBeneficiaries();

        // Load the view
        require_once 'views/Beneficiary/addRequest.html';
    }
    
    // Handle Add Request (POST)
    public function addRequest($data) {
        $type        = $data['request_type'] ?? '';
        $number      = isset($data['number']) ? (int)$data['number'] : 0;
        $reason      = $data['reason'] ?? '';
        $beneficiaryId = isset($data['beneficiary_id']) ? (int)$data['beneficiary_id'] : 0;

        if ($beneficiaryId <= 0) {
            throw new Exception("Please select a valid beneficiary. ID: $beneficiaryId");
        }

        $beneficiary = Beneficiary::getById($beneficiaryId);
        if (!$beneficiary) {
            throw new Exception("Beneficiary not found with ID $beneficiaryId");
        }

        $request = RequestFactory::createRequest($beneficiary, $type, $number, $reason);
        $insertedId = $request->insert($this->conn);

        header("Location: index.php?action=showRequests");
        exit;
    }

    // Show All Requests
    public function showRequests() {
        $result = $this->conn->query("
            SELECT r.id, b.name AS beneficiary_name, r.request_type, r.number, r.reason, r.state, r.created_at
            FROM requests r
            JOIN beneficiaries b ON r.beneficiary_id = b.id
            ORDER BY r.id DESC
        ");

        $requests = [];
        while ($row = $result->fetch_assoc()) {
            $requests[] = $row;
        }

        require_once 'views/Beneficiary/showRequests.html';
    }
}
