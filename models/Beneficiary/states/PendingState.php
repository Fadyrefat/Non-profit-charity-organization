<?php

class PendingState implements RequestState {
    public function approve(BeneficiaryRequest $request) {
        $request->setState(new ApprovedState());
        echo "Request approved successfully.\n";
    }

    public function reject(BeneficiaryRequest $request) {
        $request->setState(new RejectedState());
        echo "Request rejected.\n";
    }

    public function complete(BeneficiaryRequest $request) {
        echo "Cannot complete a pending request. Please approve it first.\n";
    }

    public function getStateName() {
        return "Pending";
    }
}