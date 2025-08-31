<?php
require_once __DIR__ . "/Observer.php";

class ImpactTrackerObserver implements Observer
{
    private mysqli $conn;

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }

    // ===================== Triggered when a request is completed =====================
    public function update(int $requestId, string $type, int $number): void
    {
        // Fetch beneficiary_id for this request
        $stmt = $this->conn->prepare("SELECT beneficiary_id FROM requests WHERE id = ?");
        $stmt->bind_param("i", $requestId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $beneficiaryId = $row['beneficiary_id'] ?? null;
        $stmt->close();

        if (!$beneficiaryId) {
            error_log("ImpactTracker: No beneficiary found for request $requestId");
            return;
        }

        // Insert distribution record
        $stmt = $this->conn->prepare("
            INSERT INTO distributions (request_id, beneficiary_id, resource_type, quantity, distributed_at)
            VALUES (?, ?, ?, ?, NOW())
        ");

        if ($stmt) {
            $stmt->bind_param("iisi", $requestId, $beneficiaryId, $type, $number);
            $stmt->execute();
            $stmt->close();
        } else {
            error_log("ImpactTracker: Failed to prepare statement - " . $this->conn->error);
        }
    }
}
