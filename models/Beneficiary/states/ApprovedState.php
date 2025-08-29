<?php

class ApprovedState implements RequestState {
    public function approve(BeneficiaryRequest $request): void {}
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