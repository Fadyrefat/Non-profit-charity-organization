<?php

class CompletedState implements RequestState {
    public function approve(BeneficiaryRequest $request) {
        echo "Cannot approve a completed request.\n";
    }

    public function reject(BeneficiaryRequest $request) {
        echo "Cannot reject a completed request.\n";
    }

    public function complete(BeneficiaryRequest $request) {
        echo "Request is already completed.\n";
    }

    public function getStateName() {
        return "Completed";
    }
}