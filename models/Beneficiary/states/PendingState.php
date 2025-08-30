<?php
require_once "RequestState.php";
require_once "ApprovedState.php";
require_once "RejectedState.php";

class PendingState implements RequestState {
    public function approve(BeneficiaryRequest $request): void {
        $request->setState(new ApprovedState());
    }

    public function reject(BeneficiaryRequest $request): void {
        $request->setState(new RejectedState());
    }

    public function complete(BeneficiaryRequest $request): void {
        throw new Exception("Cannot complete a request that is still pending.");
    }

    public function getName(): string {
        return "Pending";
    }
}
