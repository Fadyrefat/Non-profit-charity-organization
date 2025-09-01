<?php
require_once __DIR__ . '/EventCommandInterface.php';

class BookTicketCommand implements EventCommandInterface {
    private $receiver;
    private $eventId;
    private $data;

    public function __construct($receiver, $eventId, $data) {
        $this->receiver = $receiver;
        $this->eventId  = $eventId;
        $this->data     = $data;
    }

    public function execute() {
        $attendeeId = (int)$this->data['attendee_id'];
        $ticketType = in_array($this->data['ticket_type'] ?? '', ['General','VIP','VIP+'])
            ? $this->data['ticket_type']
            : 'General';

        $this->receiver->bookTicket($this->eventId, $attendeeId, $ticketType);
    }
}
