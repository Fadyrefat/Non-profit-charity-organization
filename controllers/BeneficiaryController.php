<?php

class BeneficiaryController
{

    private mysqli $conn;
    private RequestManager $requestManager;

    public function __construct()
    {
        // ✅ Initialize DB connection once
        $this->conn = Database::getInstance()->getConnection();

        // Include Beneficiary model
        require_once 'models/Beneficiary/Beneficiary.php';

        // Include Request classes
        require_once 'models/Beneficiary/RequestFactory.php';
        require_once 'models/Beneficiary/requests/FoodRequest.php';
        require_once 'models/Beneficiary/requests/ClothesRequest.php';
        require_once 'models/Beneficiary/requests/FinancialRequest.php';

        // Include all states
        require_once 'models/Beneficiary/states/PendingState.php';
        require_once 'models/Beneficiary/states/ApprovedState.php';
        require_once 'models/Beneficiary/states/RejectedState.php';
        require_once 'models/Beneficiary/states/CompletedState.php';

        // Include RequestManager
        require_once 'models/Beneficiary/RequestManager.php';
        $this->requestManager = new RequestManager($this->conn);
    }

    // ----------------------------
    // Department index page
    // ----------------------------
    public function Index()
    {
        require_once 'views/Beneficiary/index.html';
    }

    // ----------------------------
    // Beneficiary CRUD
    // ----------------------------
    public function addForm()
    {
        require_once 'views/Beneficiary/addBeneficiary.html';
    }

    public function addBeneficiary($data)
    {
        $name = $data['name'] ?? null;
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

    public function showAll()
    {
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
    public function addRequestForm()
    {
        $beneficiaries = Beneficiary::getBeneficiaries();
        require_once 'views/Beneficiary/addRequest.html';
    }

    public function addRequest($data)
    {
        $type = $data['request_type'] ?? '';
        $number = isset($data['number']) ? (int) $data['number'] : 0;
        $reason = $data['reason'] ?? '';
        $beneficiaryId = isset($data['beneficiary_id']) ? (int) $data['beneficiary_id'] : 0;

        if ($beneficiaryId <= 0) {
            throw new Exception("Please select a valid beneficiary. ID: $beneficiaryId");
        }

        $beneficiary = Beneficiary::getById($beneficiaryId);
        if (!$beneficiary) {
            throw new Exception("Beneficiary not found with ID $beneficiaryId");
        }

        // ✅ Use RequestFactory to create request
        $request = RequestFactory::createRequest($beneficiary, $type, $number, $reason);

        // ✅ Use RequestManager to insert request and handle state
        $limit = 100; // Example: max number allowed
        $this->requestManager->addRequest($request, $limit);

        header("Location: index.php?action=showRequests");
        exit;
    }

    public function showRequests()
    {
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

    // ----------------------------
    // Approve / Reject Requests
    // ----------------------------
    public function approveRequest($requestId)
    {
        $requestId = (int) $requestId;
        if ($requestId <= 0)
            throw new Exception("Invalid request ID: $requestId");

        $this->requestManager->approveRequest($requestId);

        header("Location: index.php?action=showRequests");
        exit;
    }

    public function rejectRequest($requestId)
    {
        $requestId = (int) $requestId;
        if ($requestId <= 0)
            throw new Exception("Invalid request ID: $requestId");

        $this->requestManager->rejectRequest($requestId);

        header("Location: index.php?action=showRequests");
        exit;
    }

    public function completeRequest($requestId)
    {
        $requestId = (int) $requestId;
        if ($requestId <= 0)
            throw new Exception("Invalid request ID: $requestId");

        $this->requestManager->completeRequest($requestId);

        header("Location: index.php?action=showRequests");
        exit;
    }
}
