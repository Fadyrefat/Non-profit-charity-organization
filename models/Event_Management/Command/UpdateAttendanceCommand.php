<?php
require_once __DIR__ . '/EventCommandInterface.php';

class UpdateAttendanceCommand implements EventCommandInterface {
    private $receiver;
    private $eventId;
    private $attendeeId;
    private $checkedIn;

    public function __construct($receiver, $eventId, $attendeeId, $checkedIn) {
        $this->receiver = $receiver;
        $this->eventId = $eventId;
        $this->attendeeId = $attendeeId;
        $this->checkedIn = $checkedIn;
    }

    public function execute() {
        $this->receiver->updateAttendance(
            $this->eventId,
            $this->attendeeId,
            $this->checkedIn
        );
    }
}
