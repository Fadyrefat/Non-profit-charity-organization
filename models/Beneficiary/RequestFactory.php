<?php

require_once __DIR__ . "/requests/BeneficiaryRequest.php";
require_once __DIR__ . "/requests/FoodRequest.php";
require_once __DIR__ . "/requests/ClothesRequest.php";
require_once __DIR__ . "/requests/FinancialRequest.php";

class RequestFactory
{
    // ===================== Create Request Based on Type =====================
    public static function createRequest(
        Beneficiary $beneficiary,
        string $type,
        int $number,
        string $reason,
        mysqli $conn,
        int $requestId = 0
    ): BeneficiaryRequest {
        switch (strtolower($type)) {
            case 'food':
                return new FoodRequest($beneficiary, $number, $reason, $conn, $requestId);

            case 'clothes':
                return new ClothesRequest($beneficiary, $number, $reason, $conn, $requestId);

            case 'financial':
                return new FinancialRequest($beneficiary, $number, $reason, $conn, $requestId);

            default:
                throw new Exception("Unknown request type: $type");
        }
    }
}
?>
