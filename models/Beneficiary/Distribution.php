<?php

class Distribution {

    private mysqli $conn;

    public function __construct(mysqli $conn) {
        $this->conn = $conn;
    }

    // Insert a new distribution record
    public function createDistribution(int $requestId, int $beneficiaryId, string $resourceType, int $quantity, string $distributedBy): bool {
        $stmt = $this->conn->prepare("
            INSERT INTO distributions (request_id, beneficiary_id, resource_type, quantity, distributed_by)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("iisds", $requestId, $beneficiaryId, $resourceType, $quantity, $distributedBy);
        return $stmt->execute();
    }

    public static function getAll(): array
    {
        $conn = Database::getInstance()->getConnection();
        $sql = "
            SELECT 
                d.id,
                d.request_id,
                d.beneficiary_id,
                b.name AS beneficiary_name,
                r.request_type,
                d.quantity,
                d.distributed_at
            FROM distributions d
            JOIN beneficiaries b ON d.beneficiary_id = b.id
            JOIN requests r ON d.request_id = r.id
            ORDER BY d.distributed_at DESC
        ";
        $result = $conn->query($sql);

        $items = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        return $items;
    }
}
