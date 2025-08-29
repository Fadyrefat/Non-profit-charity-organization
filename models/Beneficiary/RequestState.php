<?php

// RequestState Interface and Implementations
interface RequestState {
    public function approve(BeneficiaryRequest $request);
    public function reject(BeneficiaryRequest $request);
    public function complete(BeneficiaryRequest $request);
    public function getStateName();
}
