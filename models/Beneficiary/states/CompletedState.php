<?php

class CompletedState implements RequestState {
    public function approve(BeneficiaryRequest $request): void {}
    public function reject(BeneficiaryRequest $request): void {}
    public function complete(BeneficiaryRequest $request): void {}
    public function getName(): string {
        return "Completed";
    }
}