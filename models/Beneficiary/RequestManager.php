<?php


class RequestManager {
    private $requests = [];

    public function addRequest(BeneficiaryRequest $request) {
        $this->requests[] = $request;
        echo "Added new " . $request->getType() . " request for " . $request->getName() . ".\n";
    }

    public function approveRequest($index) {
        if (isset($this->requests[$index])) {
            $this->requests[$index]->approve();
        } else {
            echo "Request not found.\n";
        }
    }

    public function rejectRequest($index) {
        if (isset($this->requests[$index])) {
            $this->requests[$index]->reject();
        } else {
            echo "Request not found.\n";
        }
    }

    public function completeRequest($index) {
        if (isset($this->requests[$index])) {
            $this->requests[$index]->complete();
        } else {
            echo "Request not found.\n";
        }
    }

    public function listRequests() {
        echo "\n=== CURRENT REQUESTS ===\n";
        if (empty($this->requests)) {
            echo "No requests found.\n";
            return;
        }

        foreach ($this->requests as $index => $request) {
            echo "[$index] " . $request->getType() . " request for " . $request->getName() . 
                 " (Status: " . $request->getStatus() . ")\n";
        }
    }
}