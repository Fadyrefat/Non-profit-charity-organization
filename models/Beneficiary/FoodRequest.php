<?php

require_once "BeneficiaryRequest.php";

class FoodRequest extends BeneficiaryRequest
{
    protected function setRequestType()
    {
        $this->requestType = "Food";
    }
}
?>
