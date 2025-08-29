<?php

class ApprovedState implements RequestState {
    public function approve(BeneficiaryRequest $request) {
        echo "Request is already approved.\n";
    }

    public function reject(BeneficiaryRequest $request) {
        echo "Cannot reject an approved request.\n";
    }

    public function complete(BeneficiaryRequest $request) {
        $request->setState(new CompletedState());
        echo "Request marked as completed.\n";
    }

    public function getStateName() {
        return "Approved";
    }
}
