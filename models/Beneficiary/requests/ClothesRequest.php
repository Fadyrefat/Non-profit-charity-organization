<?php

require_once "../BeneficiaryRequest.php";

class ClothesRequest extends BeneficiaryRequest
{
    protected function setRequestType()
    {
        $this->requestType = "Clothes";
    }
}
?>
