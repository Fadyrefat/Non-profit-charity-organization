<?php

require_once "BeneficiaryRequest.php";

class FinancialRequest extends BeneficiaryRequest
{
    protected function setRequestType()
    {
        $this->requestType = "Financial";
    }
}
?>
