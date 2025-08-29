<?php

require_once "../BeneficiaryRequest.php";

class MedicalRequest extends BeneficiaryRequest
{
    private $diagnosis;
    private $doctorReport;

    // Updated constructor: Now accepts a Beneficiary object
    public function __construct($beneficiary, $diagnosis, $doctorReport)
    {
        parent::__construct($beneficiary); // Pass the Beneficiary object to parent
        $this->diagnosis = $diagnosis;
        $this->doctorReport = $doctorReport;
    }

    protected function setRequestType()
    {
        $this->requestType = "Medical";
    }

    // Getters and setters for medical-specific attributes
    public function getDiagnosis() {
        return $this->diagnosis;
    }

    public function setDiagnosis($diagnosis) {
        $this->diagnosis = $diagnosis;
    }

    public function getDoctorReport() {
        return $this->doctorReport;
    }

    public function setDoctorReport($doctorReport) {
        $this->doctorReport = $doctorReport;
    }
}
?>