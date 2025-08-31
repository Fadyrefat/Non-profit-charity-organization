<?php
class EventInvoker {
    private $command;

    public function setCommand(EventCommandInterface $command) {
        $this->command = $command;
    }

    public function execute() {
        return $this->command->execute();
    }
}
