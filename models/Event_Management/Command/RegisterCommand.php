<?php
require_once __DIR__ . '/EventCommandInterface.php';
require_once __DIR__ . '/../Attendee.php';
require_once __DIR__ . '/../EventLog.php';

class RegisterCommand implements EventCommandInterface {
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
        $attendeeModel = new Attendee();

        // Parse selected_user format: donor:5, volunteer:2, etc.
        $parts = explode(':', $this->data['selected_user']);
        if (count($parts) !== 2) {
            throw new Exception("Invalid user selection format");
        }
        $userType = $parts[0];      // donor | volunteer | beneficiary
        $userId   = (int)$parts[1]; // actual user id

        // Normalize ticket type
        $ticketType = in_array($this->data['ticket_type'] ?? '', ['General','VIP','VIP+'])
            ? $this->data['ticket_type'] 
            : 'General';

        // Reminder methods: array → string
        $remMethods = null;
        if (!empty($this->data['reminder_methods'])) {
            $remMethods = implode(',', $this->data['reminder_methods']);
        }

        // Save attendee (new signature)
        $attendeeModel->register(
            $this->eventId,
            $userType,
            $userId,
            $ticketType,
            $remMethods
        );

        // Log it
        $log = new EventLog();
        $log->addLog($this->eventId, 'RegisterAttendee', json_encode([
            'user_type'        => $userType,
            'user_id'          => $userId,
            'ticket_type'      => $ticketType,
            'reminder_methods' => $remMethods
        ]));
    }
}
