<?php

class Distribution
{
    public int $id;
    public int $requestId;
    public int $beneficiaryId;
    public int $quantity;
    public string $distributedAt;
    public string $requestType;
    public string $beneficiaryName;

    public function __construct(
        int $id,
        int $requestId,
        int $beneficiaryId,
        int $quantity,
        string $distributedAt,
        string $requestType,
        string $beneficiaryName
    ) {
        $this->id             = $id;
        $this->requestId      = $requestId;
        $this->beneficiaryId  = $beneficiaryId;
        $this->quantity       = $quantity;
        $this->distributedAt  = $distributedAt;
        $this->requestType    = $requestType;
        $this->beneficiaryName = $beneficiaryName;
    }

    // ===================== Getters =====================
    public function getId(): int { return $this->id; }
    public function getRequestId(): int { return $this->requestId; }
    public function getBeneficiaryId(): int { return $this->beneficiaryId; }
    public function getQuantity(): int { return $this->quantity; }
    public function getDistributedAt(): string { return $this->distributedAt; }
    public function getRequestType(): string { return $this->requestType; }
    public function getBeneficiaryName(): string { return $this->beneficiaryName; }

    // ===================== Get All Distributions =====================
    public static function getAll(): array
    {
        $conn = Database::getInstance()->getConnection();

        $sql = "
            SELECT 
                d.id,
                d.request_id,
                d.beneficiary_id,
                d.quantity,
                d.distributed_at,
                r.request_type,
                b.name AS beneficiary_name
            FROM distributions d
            JOIN requests r ON d.request_id = r.id
            JOIN beneficiaries b ON d.beneficiary_id = b.id
            ORDER BY d.distributed_at DESC
        ";

        $result = $conn->query($sql);
        $items  = [];

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $items[] = new self(
                    (int)$row['id'],
                    (int)$row['request_id'],
                    (int)$row['beneficiary_id'],
                    (int)$row['quantity'],
                    $row['distributed_at'],
                    $row['request_type'] ?? '',
                    $row['beneficiary_name'] ?? ''
                );
            }
        }

        return $items;
    }
}
?>
