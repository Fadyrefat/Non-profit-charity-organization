<?php
require_once __DIR__ . '/EventCommandInterface.php';
require_once __DIR__ . '/../Attendee.php';
require_once __DIR__ . '/../EventLog.php';

class BookTicketCommand implements EventCommandInterface {
    private $receiver;
    private $event;
    private $eventId;
    private $data;

    public function __construct($receiver, $event, $eventId, $data) {
        $this->receiver = $receiver;
        $this->event    = $event;
        $this->eventId  = $eventId;
        $this->data     = $data;
    }

    public function execute() {
        $attendeeId = (int)$this->data['attendee_id'];
        $ticketType = in_array($this->data['ticket_type'] ?? '', ['General','VIP','VIP+'])
            ? $this->data['ticket_type']
            : 'General';

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("UPDATE attendees SET ticket_type = ? WHERE id = ?");
        $stmt->bind_param("si", $ticketType, $attendeeId);
        $stmt->execute();

        // Log with event_id context
        $log = new EventLog();
        $log->addLog($this->eventId, 'BookTicket', json_encode([
            'attendee_id' => $attendeeId,
            'ticket_type' => $ticketType
        ]));
    }
}
