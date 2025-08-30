<?php
require_once __DIR__ . "/requests/BeneficiaryRequest.php";
require_once __DIR__ . "/requests/FoodRequest.php";
require_once __DIR__ . "/requests/ClothesRequest.php";
require_once __DIR__ . "/requests/FinancialRequest.php";

class RequestFactory {
    public static function createRequest(Beneficiary $beneficiary, string $type, int $number, string $reason): BeneficiaryRequest {
        switch (strtolower($type)) {
            case 'food':
                return new FoodRequest($beneficiary, $number, $reason);
            case 'clothes':
                return new ClothesRequest($beneficiary, $number, $reason);
            case 'financial':
                return new FinancialRequest($beneficiary, $number, $reason);
            default:
                throw new Exception("Unknown request type: $type");
        }
    }
}
