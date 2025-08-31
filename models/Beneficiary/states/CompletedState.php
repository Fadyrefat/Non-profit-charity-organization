<?php
require_once "RequestState.php";

// ===================== Completed State =====================
class CompletedState implements RequestState
{
    public function approve(BeneficiaryRequest $request): void
    {
        throw new Exception("Cannot approve a completed request.");
    }

    public function reject(BeneficiaryRequest $request): void
    {
        throw new Exception("Cannot reject a completed request.");
    }

    public function complete(BeneficiaryRequest $request): void
    {
        // Already completed, no action needed
    }

    public function getName(): string
    {
        return "Completed";
    }
}
