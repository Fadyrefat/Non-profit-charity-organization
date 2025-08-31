<?php

class BeneficiaryController {

    private mysqli $conn;
    private RequestManager $requestManager;

    public function __construct() {
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

        // Include Report classes
        require_once 'models/Beneficiary/ReportContext.php';
        // Include RequestManager
        require_once 'models/Beneficiary/RequestManager.php';
        require_once 'models/Beneficiary/observers/ImpactTrackerObserver.php';

        $this->requestManager = new RequestManager($this->conn);
        $this->requestManager->attach(new ImpactTrackerObserver($this->conn));

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
    public function addForm() {
        require_once 'views/Beneficiary/addBeneficiary.html';
    }

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
    public function addRequestForm() {
        $beneficiaries = Beneficiary::getBeneficiaries();
        require_once 'views/Beneficiary/addRequest.html';
    }

    public function addRequest($data) {
        $type          = $data['request_type'] ?? '';
        $number        = isset($data['number']) ? (int)$data['number'] : 0;
        $reason        = $data['reason'] ?? '';
        $beneficiaryId = isset($data['beneficiary_id']) ? (int)$data['beneficiary_id'] : 0;

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

    public function showRequests() {
        $stateFilter = $_GET['state'] ?? 'All';

        $sql = "SELECT r.id, b.name AS beneficiary_name, r.request_type, r.number, r.reason, r.state, r.created_at
                FROM requests r
                JOIN beneficiaries b ON r.beneficiary_id = b.id";

        if ($stateFilter !== 'All') {
            $sql .= " WHERE r.state = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $stateFilter);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $this->conn->query($sql);
        }

        $requests = [];
        while ($row = $result->fetch_assoc()) {
            $requests[] = $row;
        }

        require_once 'views/Beneficiary/showRequests.html';
    }


    // ----------------------------
    // Approve / Reject Requests
    // ----------------------------
    public function approveRequest($requestId) {
        $requestId = (int)$requestId;
        if ($requestId <= 0) throw new Exception("Invalid request ID: $requestId");

        $this->requestManager->approveRequest($requestId);

        header("Location: index.php?action=showRequests");
        exit;
    }

    public function rejectRequest($requestId) {
        $requestId = (int)$requestId;
        if ($requestId <= 0) throw new Exception("Invalid request ID: $requestId");

        $this->requestManager->rejectRequest($requestId);

        header("Location: index.php?action=showRequests");
        exit;
    }

        // ----------------------------
    // Complete Request
    // ----------------------------
    public function completeRequest($requestId) {
        $message = $this->requestManager->completeRequest((int)$requestId);

        // Send alert and redirect
        echo "<script>alert('". addslashes($message) ."'); window.location='index.php?action=showRequests';</script>";
        exit;
    }

    public function showDistributions() {
        require_once 'models/Beneficiary/Distribution.php';
        $distributions = Distribution::getDistributions(); // static method to fetch all distributions
        require 'views/Beneficiary/showDistributions.html';
    }

    public function generateReport($reportType) {
        $conn = Database::getInstance()->getConnection();
        // Pass type to ReportContext (let it decide)
        $context = new ReportContext($reportType);
        // Generate report
        $reportData = $context->generate($conn);
        // Pass report data to a view
        require 'views/Beneficiary/reportView.html';
    }







    // ================= SHOW FEEDBACK FORM =================
        public function addFeedbackForm(int $requestId, int $beneficiaryId)
        {
            require_once 'views/Beneficiary/addFeedback.html';
        }

        // ================= INSERT FEEDBACK =================
        public function addFeedback(array $data)
        {
            $conn = Database::getInstance()->getConnection();

            $requestId      = isset($data['request_id']) ? (int)$data['request_id'] : 0;
            $beneficiaryId  = isset($data['beneficiary_id']) ? (int)$data['beneficiary_id'] : 0;
            $rating         = isset($data['satisfaction_rating']) ? (int)$data['satisfaction_rating'] : null;
            $notes          = $data['outcome_notes'] ?? null;

            $stmt = $conn->prepare("
                INSERT INTO beneficiary_feedback (request_id, beneficiary_id, satisfaction_rating, outcome_notes)
                VALUES (?, ?, ?, ?)
            ");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }

            $stmt->bind_param("iiis", $requestId, $beneficiaryId, $rating, $notes);

            if ($stmt->execute()) {
                header("Location: index.php?action=showDistributions&beneficiary_id=" . $beneficiaryId);
                exit;
            } else {
                echo "❌ Failed to submit feedback: " . $stmt->error;
            }
        }

        // ================= SHOW ALL FEEDBACK FOR A BENEFICIARY =================
        public function showFeedbacks()
        {
            $conn = Database::getInstance()->getConnection();

            $stmt = $conn->prepare("
                SELECT f.id, f.satisfaction_rating, f.outcome_notes, f.reported_at,
                    r.request_type, b.name AS beneficiary_name
                FROM beneficiary_feedback f
                JOIN requests r ON f.request_id = r.id
                JOIN beneficiaries b ON f.beneficiary_id = b.id
                ORDER BY f.reported_at DESC
            ");
            $stmt->execute();
            $result = $stmt->get_result();

            $feedbacks = [];
            while ($row = $result->fetch_assoc()) {
                $feedbacks[] = $row;
            }

            // Pass data to the view
            require 'views/Beneficiary/showFeedbacks.html';
        }

















}
