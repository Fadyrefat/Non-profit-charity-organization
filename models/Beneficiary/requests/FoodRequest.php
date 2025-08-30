<?php

require_once "BeneficiaryRequest.php";

class FoodRequest extends BeneficiaryRequest
{
    protected function setRequestType(): void
    {
        $this->requestType = "Food";
    }
}
?>
