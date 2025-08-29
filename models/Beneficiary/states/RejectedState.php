<?php
class RejectedState implements RequestState {
    public function approve(BeneficiaryRequest $request) {
        echo "Cannot approve a rejected request.\n";
    }

    public function reject(BeneficiaryRequest $request) {
        echo "Request is already rejected.\n";
    }

    public function complete(BeneficiaryRequest $request) {
        echo "Cannot complete a rejected request.\n";
    }

    public function getStateName() {
        return "Rejected";
    }
}