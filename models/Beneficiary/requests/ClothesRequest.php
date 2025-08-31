<?php
require_once "BeneficiaryRequest.php";

// ===================== Clothes Request =====================
class ClothesRequest extends BeneficiaryRequest
{
    protected function setRequestType(): void
    {
        $this->requestType = "Clothes";
    }
}
