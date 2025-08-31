<?php

require_once __DIR__ . "/requests/BeneficiaryRequest.php";
require_once __DIR__ . "/requests/FoodRequest.php";
require_once __DIR__ . "/requests/ClothesRequest.php";
require_once __DIR__ . "/requests/FinancialRequest.php";

class RequestManager
{
    private mysqli $conn;
    private array $observers = [];

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }

    // ===================== Add a Request =====================
    public function addRequest(BeneficiaryRequest $request)
    {
        $request->setState(new PendingState());
        $request->setConnection($this->conn); // inject DB connection
        $requestId = $this->insertRequestToDB($request);
        $request->setRequestId($requestId);
    }

    // ===================== Insert Request into DB =====================
    private function insertRequestToDB(BeneficiaryRequest $request)
    {
        $beneficiaryId = $request->getBeneficiary()->getId();
        $type          = $request->getType();
        $number        = $request->getNumber();
        $reason        = $request->getReason();
        $state         = $request->getStatus();

        $stmt = $this->conn->prepare("
            INSERT INTO requests (beneficiary_id, request_type, number, reason, state)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("isiss", $beneficiaryId, $type, $number, $reason, $state);
        $stmt->execute();
        $id = $stmt->insert_id;
        $stmt->close();

        return $id;
    }

    // ===================== Fetch All Requests =====================
    public static function getAll(string $state = 'All'): array
    {
        $conn = Database::getInstance()->getConnection();

        $sql = "SELECT r.id, b.name AS beneficiary_name, r.request_type, r.number, r.reason, r.state, r.created_at
                FROM requests r
                JOIN beneficiaries b ON r.beneficiary_id = b.id";

        if ($state !== 'All') {
            $stmt = $conn->prepare($sql . " WHERE r.state = ?");
            $stmt->bind_param("s", $state);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $conn->query($sql);
        }

        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    // ===================== Get Request by ID =====================
    public function getRequestById(int $id): ?BeneficiaryRequest
    {
        $stmt = $this->conn->prepare("
            SELECT r.*, b.name, b.email, b.phone, b.address
            FROM requests r
            JOIN beneficiaries b ON r.beneficiary_id = b.id
            WHERE r.id = ?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$row) return null;

        // Create Beneficiary object
        $beneficiary = new Beneficiary(
            $row['name'],
            $row['email'],
            $row['phone'],
            $row['address'],
            (int)$row['beneficiary_id']
        );

        // Determine request type
        $request = match(strtolower($row['request_type'])) {
            'food' => new FoodRequest($beneficiary, (int)$row['number'], $row['reason'], $this->conn, (int)$row['id']),
            'clothes' => new ClothesRequest($beneficiary, (int)$row['number'], $row['reason'], $this->conn, (int)$row['id']),
            'financial' => new FinancialRequest($beneficiary, (int)$row['number'], $row['reason'], $this->conn, (int)$row['id']),
            default => null
        };

        if (!$request) return null;

        // Set correct state based on DB
        $state = match($row['state']) {
            'Pending' => new PendingState(),
            'Approved' => new ApprovedState(),
            'Rejected' => new RejectedState(),
            'Completed' => new CompletedState(),
            default => new PendingState()
        };

        $request->setState($state);

        return $request;
    }


    // ===================== Observer Handling =====================
    public function attach(Observer $observer)
    {
        $this->observers[] = $observer;
    }

    public function getObservers(): array
    {
        return $this->observers;
    }

    private function notifyObservers(int $beneficiaryId, string $type, int $number)
    {
        foreach ($this->observers as $observer) {
            $observer->update($beneficiaryId, $type, $number);
        }
    }
}
?>
