<?php

class BeneficiaryController {

    private $conn;

    public function __construct() {
        // ✅ Initialize DB connection once
        $this->conn = Database::getInstance()->getConnection();
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
        require_once 'models/Beneficiary/Beneficiary.php';

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
        require_once 'models/Beneficiary/Beneficiary.php';

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
    // BeneficiaryController.php
    public function addRequestForm() {
        // Include Beneficiary model
        require_once 'models/Beneficiary/Beneficiary.php';

        // Fetch all beneficiaries
        $beneficiaries = Beneficiary::getBeneficiaries(); // create this static method in Beneficiary model

        // Load the view
        require_once 'views/Beneficiary/addRequest.html';
    }

    public function addRequest($data) {
        require_once 'models/Beneficiary/Beneficiary.php';
        require_once 'models/Beneficiary/BeneficiaryRequest.php';
        require_once 'models/Beneficiary/ClothesRequest.php';
        require_once 'models/Beneficiary/FoodRequest.php';
        require_once 'models/Beneficiary/FinancialRequest.php';

        $beneficiaryId = $_POST['beneficiary_id'] ?? 0;
        $requestType   = strtolower(trim($_POST['request_type'] ?? ''));
        $number        = isset($_POST['number']) && $_POST['number'] !== '' ? (int)$_POST['number'] : null;
        $reason        = trim($_POST['reason'] ?? '');

        // Validate required fields
        if ($beneficiaryId <= 0 || !$requestType) {
            echo "❌ Beneficiary ID and Request Type are required.";
            return;
        }

        // Fetch Beneficiary object
        $beneficiary = Beneficiary::findById((int)$beneficiaryId);
        if (!$beneficiary) {
            echo "❌ Beneficiary not found.";
            return;
        }

        // Map request types to classes
        $map = [
            'clothes'   => 'ClothesRequest',
            'food'      => 'FoodRequest',
            'financial' => 'FinancialRequest'
        ];

        if (!isset($map[$requestType])) {
            echo "❌ Invalid request type.";
            return;
        }

        // Create request object
        $className = $map[$requestType];
        $request = new $className($beneficiary, $number, $reason);

        // Insert into DB
        try {
            $requestId = $request->insert($this->conn);
            header("Location: index.php?action=showRequests");
            exit;
        } catch (Exception $e) {
            echo "❌ Could not insert request: " . $e->getMessage();
        }
    }

    // Show All Requests
    public function showRequests() {
        require_once 'models/Beneficiary/BeneficiaryRequest.php';

        $result = $this->conn->query("
            SELECT r.id, b.name AS beneficiary_name, r.type, r.description, r.status
            FROM beneficiary_requests r
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
