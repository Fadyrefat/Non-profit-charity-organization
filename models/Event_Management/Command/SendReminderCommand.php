<?php
require_once __DIR__ . '/EventCommandInterface.php';

class SendReminderCommand implements EventCommandInterface {
    private $receiver;
    private $eventId;
    private $message;

    public function __construct($receiver, $eventId, $message = 'Reminder about upcoming event') {
        $this->receiver = $receiver;
        $this->eventId = $eventId;
        $this->message = $message;
    }

    public function execute() {
        $this->receiver->sendReminder($this->eventId, $this->message);
    }
}
