<?php
require_once 'Attendee.php';
require_once 'EventLog.php';
require_once 'ObserverFactory.php';
require_once 'Event.php';

class EventReceiver {
    private $attendee;
    private $log;
    private $eventModel;
    private $observerFactory;

    public function __construct(Event $event) {
        $this->eventModel = $event; 
        $this->attendee = new Attendee();
        $this->log = new EventLog();
        $this->observerFactory = new ObserverFactory();
    }

    public function registerAttendee($eventId, $userType, $userId, $ticketType, $remMethods) {
        // Register attendee in database
        $this->attendee->register($eventId, $userType, $userId, $ticketType, $remMethods);
        
        try {
            $userObserver = $this->observerFactory->createObserver($userType, $userId);
            $this->eventModel->subscribe($userObserver);
            
        } catch (Exception $e) {
            // Log the error but don't break the registration process
            error_log("Failed to create observer for $userType $userId: " . $e->getMessage());
            // You might want to add this to your event log as well
            $this->log->addLog($eventId, 'ObserverCreationFailed', json_encode([
                'user_type' => $userType,
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]));
        }
        
        // Log the action
        $this->log->addLog($eventId, 'RegisterAttendee', json_encode([
            'user_type' => $userType,
            'user_id' => $userId,
            'ticket_type' => $ticketType,
            'reminder_methods' => $remMethods
        ]));
    }

    public function bookTicket($eventId, $attendeeData) {
        // Extract data from array
        $userId = $attendeeData['user_id'] ?? null;
        $userType = $attendeeData['user_type'] ?? null;
        $ticketType = $attendeeData['ticket_type'] ?? 'General';
        $remMethods = $attendeeData['reminder_methods'] ?? null;
        
        // Register the attendee
        $this->attendee->register($eventId, $userType, $userId, $ticketType, $remMethods);
        
        // Log the action
        $this->log->addLog($eventId, "book_ticket", json_encode($attendeeData));
        
        return $attendeeData;
    }

    public function updateAttendance($eventId, $attendeeId, $checkedIn) {
        $this->attendee->updateAttendance($attendeeId, $checkedIn);
        $this->log->addLog($eventId, "update_attendance", "attendee $attendeeId checkin=$checkedIn");
    }

public function sendReminder($eventId, $message) {
    $event = $this->eventModel->find($eventId);

    $start = new DateTime($event['start_datetime']);
    $end = new DateTime($event['end_datetime']);
    $duration = $end->getTimestamp() - $start->getTimestamp();
    $durationHours = (int) floor($duration / 3600);

    $payload = [
        'title' => $event['title'],
        'duration_hours' => $durationHours,
        'location' => $event['location'],
        'message' => $message
    ];

    // Notify observers
    $this->eventModel->notify($eventId, $payload);

    // Log
    $this->log->addLog($eventId, "reminder_sent", json_encode($payload));

    return ['event_id' => $eventId, 'message' => $message];
}


}
