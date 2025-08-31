<?php
require_once "RequestState.php";

// ===================== Rejected State =====================
class RejectedState implements RequestState
{
    public function approve(BeneficiaryRequest $request): void
    {
        throw new Exception("Cannot approve a rejected request.");
    }

    public function reject(BeneficiaryRequest $request): void
    {
        // Already rejected, no action needed
    }

    public function complete(BeneficiaryRequest $request): void
    {
        throw new Exception("Cannot complete a rejected request.");
    }

    public function getName(): string
    {
        return "Rejected";
    }
}
