<?php
require_once "BeneficiaryRequest.php";

// ===================== Financial Request =====================
class FinancialRequest extends BeneficiaryRequest
{
    protected function setRequestType(): void
    {
        $this->requestType = "Financial";
    }
}
