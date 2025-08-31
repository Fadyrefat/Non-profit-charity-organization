<?php
require_once __DIR__ . '/EventCommandInterface.php';
require_once __DIR__ . '/../Attendee.php';
require_once __DIR__ . '/../EventLog.php';

class UpdateAttendanceCommand implements EventCommandInterface {
    private $receiver;
    private $attendeeId;
    private $checkedIn;

    public function __construct($receiver, $attendeeId, $checkedIn) {
        $this->receiver   = $receiver;
        $this->attendeeId = (int)$attendeeId;
        $this->checkedIn  = (int)$checkedIn;
    }

    public function execute() {
        $db = Database::getInstance()->getConnection();

        // Update attendance
        $stmt = $db->prepare("UPDATE attendees SET checked_in = ? WHERE id = ?");
        $stmt->bind_param("ii", $this->checkedIn, $this->attendeeId);
        $stmt->execute();

        // Fetch event_id to log properly
        $eventId = null;
        $check = $db->prepare("SELECT event_id FROM attendees WHERE id = ?");
        $check->bind_param("i", $this->attendeeId);
        $check->execute();
        $res = $check->get_result();
        if ($row = $res->fetch_assoc()) {
            $eventId = (int)$row['event_id'];
        }

        // Log with event_id if available
        $log = new EventLog();
        $log->addLog(
            $eventId ?? 0,
            'UpdateAttendance',
            json_encode([
                'attendee_id' => $this->attendeeId,
                'checked_in'  => $this->checkedIn
            ])
        );
    }
}
