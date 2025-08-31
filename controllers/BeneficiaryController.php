<?php

class BeneficiaryController 
{
    private mysqli $conn;
    private RequestManager $requestManager;

    public function __construct() 
    {
        $this->conn = Database::getInstance()->getConnection();

        // Load required models, request types, states, and observers
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

    // ---------------------------- Beneficiary CRUD ----------------------------
    public function addForm() 
    {
        require_once 'views/Beneficiary/addBeneficiary.html';
    }

    public function addBeneficiary(array $data) 
    {
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

    public function showAll() 
    {
        $beneficiaries = Beneficiary::getAll();
        require_once 'views/Beneficiary/showBeneficiaries.html';
    }

    // ---------------------------- Beneficiary Requests ----------------------------
    public function addRequestForm() 
    {
        $beneficiaries = Beneficiary::getAll();
        require_once 'views/Beneficiary/addRequest.html';
    }

    public function addRequest(array $data) 
    {
        $type          = $data['request_type'] ?? '';
        $number        = isset($data['number']) ? (int)$data['number'] : 0;
        $reason        = $data['reason'] ?? '';
        $beneficiaryId = isset($data['beneficiary_id']) ? (int)$data['beneficiary_id'] : 0;

        if ($beneficiaryId <= 0) {
            throw new Exception("Invalid beneficiary ID");
        }

        $beneficiary = Beneficiary::getById($beneficiaryId);
        if (!$beneficiary) {
            throw new Exception("Beneficiary not found");
        }

        $request = RequestFactory::createRequest($beneficiary, $type, $number, $reason, $this->conn);
        $this->requestManager->addRequest($request);

        header("Location: index.php?action=showRequests");
        exit;
    }

    public function showRequests() 
    {
        $stateFilter = $_GET['state'] ?? 'All';
        $requests = $this->requestManager->getAll($stateFilter);
        require_once 'views/Beneficiary/showRequests.html';
    }

    // ---------------------------- Approve / Reject / Complete ----------------------------
    public function approveRequest(int $requestId) 
    {
        $request = $this->requestManager->getRequestById($requestId);
        if ($request) {
            $request->setConnection($this->conn);
            $request->approve();
        }

        header("Location: index.php?action=showRequests");
        exit;
    }

    public function rejectRequest(int $requestId) 
    {
        $request = $this->requestManager->getRequestById($requestId);
        if ($request) {
            $request->setConnection($this->conn);
            $request->reject();
        }

        header("Location: index.php?action=showRequests");
        exit;
    }

    public function completeRequest(int $requestId) 
    {
        $request = $this->requestManager->getRequestById($requestId);
        if (!$request) return;

        $request->setConnection($this->conn);

        try {
            // Check inventory
            $result = mysqli_query($this->conn, "SELECT * FROM inventory LIMIT 1");
            $inventory = mysqli_fetch_assoc($result);
            $type = $request->getType();
            $number = $request->getNumber();

            $canFulfill = match($type) {
                "Food"      => $inventory["food"] >= $number,
                "Clothes"   => $inventory["clothes"] >= $number,
                "Financial" => $inventory["money"] >= $number,
                default     => false,
            };

            if (!$canFulfill) {
                throw new Exception("Cannot complete request: insufficient inventory.");
            }

            // Deduct inventory
            $field = match($type) {
                "Food"      => "food",
                "Clothes"   => "clothes",
                "Financial" => "money",
                default     => null,
            };

            if ($field) {
                mysqli_query($this->conn, "UPDATE inventory SET $field = $field - $number WHERE id = 1");
            }

            // Complete the request (updates state in DB)
            $request->complete();

            // Notify observers
            foreach ($this->requestManager->getObservers() as $observer) {
                $observer->update($request->getBeneficiary()->getId(), $type, $number);
            }

            echo "<script>alert('Request completed successfully'); window.location='index.php?action=showRequests';</script>";
        } catch (Exception $e) {
            echo "<script>alert('". addslashes($e->getMessage()) ."'); window.location='index.php?action=showRequests';</script>";
        }

        exit;
    }

    // ---------------------------- Distributions ----------------------------
    public function showDistributions() 
    {
        require_once 'models/Beneficiary/Distribution.php';
        require_once 'models/Beneficiary/BeneficiaryFeedback.php';

        $distributions = Distribution::getAll();
        $feedbacksList = BeneficiaryFeedback::getAll();

        $feedbacks = [];
        foreach ($feedbacksList as $f) {
            $feedbacks[$f->getRequestId()] = true;
        }

        require 'views/Beneficiary/showDistributions.html';
    }

    // ---------------------------- Reports ----------------------------
    public function generateReport(string $reportType) 
    {
        $context = new ReportContext($reportType);
        $reportData = $context->generate($this->conn);
        require 'views/Beneficiary/reportView.html';
    }

    // ---------------------------- Feedback ----------------------------
    public function addFeedbackForm(int $requestId, int $beneficiaryId) 
    {
        require_once 'views/Beneficiary/addFeedback.html';
    }

    public function addFeedback(array $data) 
    {
        $requestId     = $data['request_id'] ?? 0;
        $beneficiaryId = $data['beneficiary_id'] ?? 0;
        $rating        = $data['satisfaction_rating'] ?? null;
        $notes         = $data['outcome_notes'] ?? null;

        require_once 'models/Beneficiary/BeneficiaryFeedback.php';

        try {
            $feedback = new BeneficiaryFeedback($requestId, $beneficiaryId, $rating, $notes);
            $feedback->insert();
            header("Location: index.php?action=showFeedbacks");
            exit;
        } catch (Exception $e) {
            echo "❌ Failed to submit feedback: " . $e->getMessage();
        }
    }

    public function showFeedbacks() 
    {
        require_once 'models/Beneficiary/BeneficiaryFeedback.php';

        try {
            $feedbacks = BeneficiaryFeedback::getAll();
            require 'views/Beneficiary/showFeedbacks.html';
        } catch (Exception $e) {
            echo "❌ Failed to fetch feedbacks: " . $e->getMessage();
        }
    }
}
