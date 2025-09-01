<?php
require_once __DIR__ . '/EventCommandInterface.php';

class RegisterCommand implements EventCommandInterface {
    private $receiver;
    private $eventId;
    private $data;

    public function __construct($receiver, $eventId, $data) {
        $this->receiver = $receiver;
        $this->eventId = $eventId;
        $this->data = $data;
    }

    public function execute() {
        $parts = explode(':', $this->data['selected_user']);
        if (count($parts) !== 2) {
            throw new Exception("Invalid user selection format");
        }
        
        $userType = $parts[0];
        $userId = (int)$parts[1];
        $ticketType = in_array($this->data['ticket_type'] ?? '', ['General','VIP','VIP+'])
            ? $this->data['ticket_type']
            : 'General';
            
        $remMethods = !empty($this->data['reminder_methods'])
            ? implode(',', $this->data['reminder_methods'])
            : null;

        $this->receiver->registerAttendee(
            $this->eventId,
            $userType,
            $userId,
            $ticketType,
            $remMethods
        );
    }
}
