<?php
require_once "BeneficiaryRequest.php";

// ===================== Food Request =====================
class FoodRequest extends BeneficiaryRequest
{
    protected function setRequestType(): void
    {
        $this->requestType = "Food";
    }
}
