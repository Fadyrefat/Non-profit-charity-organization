<?php

class BeneficiaryController {

    private mysqli $conn;
    private RequestManager $requestManager;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();

        require_once 'models/Beneficiary/Beneficiary.php';
        require_once 'models/Beneficiary/RequestFactory.php';
        require_once 'models/Beneficiary/requests/FoodRequest.php';
        require_once 'models/Beneficiary/requests/ClothesRequest.php';
        require_once 'models/Beneficiary/requests/FinancialRequest.php';
        require_once 'models/Beneficiary/states/PendingState.php';
        require_once 'models/Beneficiary/states/ApprovedState.php';
        require_once 'models/Beneficiary/states/RejectedState.php';
        require_once 'models/Beneficiary/states/CompletedState.php';
        require_once 'models/Beneficiary/ReportContext.php';
        require_once 'models/Beneficiary/RequestManager.php';
        require_once 'models/Beneficiary/observers/ImpactTrackerObserver.php';
        $this->requestManager = new RequestManager($this->conn);
        $this->requestManager->attach(new ImpactTrackerObserver($this->conn));
    }

    // ---------------------------- Beneficiary Department ----------------------------
    public function Index() {
        require_once 'views/Beneficiary/index.html';
    }

    // ---------------------------- Beneficiary CRUD ----------------------------
    public function addForm() {
        require_once 'views/Beneficiary/addBeneficiary.html';
    }

    public function addBeneficiary($data) {
        $name    = $data['name'] ?? null;
        $email   = $data['email'] ?? null;
        $phone   = $data['phone'] ?? null;
        $address = $data['address'] ?? null;

        if (!$name || !$email || !$phone || !$address) {
            echo "❌ All fields are required.";
            return;
        }

        $beneficiary = new Beneficiary($name, $email, $phone, $address, null);
        $beneficiary->insert($this->conn);

        header("Location: index.php?action=showBeneficiaries");
        exit;
    }

    public function showAll() {
        $result = $this->conn->query("SELECT * FROM beneficiaries ORDER BY id DESC");

        $beneficiaries = [];
        while ($row = $result->fetch_assoc()) {
            $beneficiaries[] = new Beneficiary(
                $row['name'],
                $row['email'],
                $row['phone'],
                $row['address'] ?? '',
                $row['id']
                
            );
        }

        require_once 'views/Beneficiary/showBeneficiaries.html';
    }

    // ---------------------------- Beneficiary Requests ----------------------------
    public function addRequestForm() {
        $beneficiaries = Beneficiary::getBeneficiaries();
        require_once 'views/Beneficiary/addRequest.html';
    }

    public function addRequest($data) {
        $type          = $data['request_type'] ?? '';
        $number        = isset($data['number']) ? (int)$data['number'] : 0;
        $reason        = $data['reason'] ?? '';
        $beneficiaryId = isset($data['beneficiary_id']) ? (int)$data['beneficiary_id'] : 0;

        if ($beneficiaryId <= 0) throw new Exception("Invalid beneficiary ID");

        $beneficiary = Beneficiary::getById($beneficiaryId);
        if (!$beneficiary) throw new Exception("Beneficiary not found");

        $request = RequestFactory::createRequest($beneficiary, $type, $number, $reason);
        $limit = 100;
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
        while ($row = $result->fetch_assoc()) $requests[] = $row;

        require_once 'views/Beneficiary/showRequests.html';
    }

    // ---------------------------- Approve / Reject / Complete ----------------------------
    public function approveRequest($requestId) {
        $this->requestManager->approveRequest((int)$requestId);
        header("Location: index.php?action=showRequests");
        exit;
    }

    public function rejectRequest($requestId) {
        $this->requestManager->rejectRequest((int)$requestId);
        header("Location: index.php?action=showRequests");
        exit;
    }

    public function completeRequest($requestId) {
        $message = $this->requestManager->completeRequest((int)$requestId);
        echo "<script>alert('". addslashes($message) ."'); window.location='index.php?action=showRequests';</script>";
        exit;
    }

    public function showDistributions() {
        require_once 'models/Beneficiary/Distribution.php';
        $distributions = Distribution::getDistributions();
        require 'views/Beneficiary/showDistributions.html';
    }

    // ---------------------------- Reports ----------------------------
    public function generateReport($reportType) {
        $conn = Database::getInstance()->getConnection();
        $context = new ReportContext($reportType);
        $reportData = $context->generate($conn);
        require 'views/Beneficiary/reportView.html';
    }

    // ---------------------------- Feedback ----------------------------
    public function addFeedbackForm(int $requestId, int $beneficiaryId) {
        require_once 'views/Beneficiary/addFeedback.html';
    }

    public function addFeedback(array $data) {
        $conn = Database::getInstance()->getConnection();
        $requestId     = isset($data['request_id']) ? (int)$data['request_id'] : 0;
        $beneficiaryId = isset($data['beneficiary_id']) ? (int)$data['beneficiary_id'] : 0;
        $rating        = isset($data['satisfaction_rating']) ? (int)$data['satisfaction_rating'] : null;
        $notes         = $data['outcome_notes'] ?? null;

        $stmt = $conn->prepare("
            INSERT INTO beneficiary_feedback (request_id, beneficiary_id, satisfaction_rating, outcome_notes)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param("iiis", $requestId, $beneficiaryId, $rating, $notes);

        if ($stmt->execute()) {
            header("Location: index.php?action=showFeedbacks");
            exit;
        } else {
            echo "❌ Failed to submit feedback: " . $stmt->error;
        }
    }

    public function showFeedbacks() {
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
        while ($row = $result->fetch_assoc()) $feedbacks[] = $row;

        require 'views/Beneficiary/showFeedbacks.html';
    }
}
