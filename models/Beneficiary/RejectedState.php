<?php
require_once "RequestState.php";

class RejectedState implements RequestState {
    public function approve(BeneficiaryRequest $request): void {
        throw new Exception("Cannot approve a rejected request.");
    }

    public function reject(BeneficiaryRequest $request): void {
        // already rejected, do nothing
    }

    public function complete(BeneficiaryRequest $request): void {
        throw new Exception("Cannot complete a rejected request.");
    }

    public function getName(): string {
        return "Rejected";
    }
}
