<?php

require_once "ApprovedState.php";
require_once "RejectedState.php";

class AcceptState {
    public function approveRequest(BeneficiaryRequest $request) {
        if ($request->getStatus() === "Pending") {
            $request->setState(new ApprovedState());
            echo "Admin approved the request.\n";
        } else {
            echo "Only pending requests can be approved.\n";
        }
    }

    public function rejectRequest(BeneficiaryRequest $request) {
        if ($request->getStatus() === "Pending") {
            $request->setState(new RejectedState());
            echo "Admin rejected the request.\n";
        } else {
            echo "Only pending requests can be rejected.\n";
        }
    }
}
