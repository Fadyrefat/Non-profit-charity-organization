<?php
require_once "RequestState.php";

class CompletedState implements RequestState {
    public function approve(BeneficiaryRequest $request): void {
        throw new Exception("Cannot approve a completed request.");
    }

    public function reject(BeneficiaryRequest $request): void {
        throw new Exception("Cannot reject a completed request.");
    }

    public function complete(BeneficiaryRequest $request): void {
        // already completed, do nothing
    }

    public function getName(): string {
        return "Completed";
    }
}
