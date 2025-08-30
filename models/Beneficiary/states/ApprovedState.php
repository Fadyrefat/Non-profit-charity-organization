<?php
require_once "RequestState.php";
require_once "RejectedState.php";
require_once "CompletedState.php";

class ApprovedState implements RequestState {
    public function approve(BeneficiaryRequest $request): void {
        // already approved, do nothing
    }

    public function reject(BeneficiaryRequest $request): void {
        $request->setState(new RejectedState());
    }

    public function complete(BeneficiaryRequest $request): void {
        $request->setState(new CompletedState());
    }

    public function getName(): string {
        return "Approved";
    }
}
