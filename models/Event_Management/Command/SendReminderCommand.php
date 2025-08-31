<?php
require_once __DIR__ . '/EventCommandInterface.php';
require_once __DIR__ . '/../Attendee.php';
require_once __DIR__ . '/../EventLog.php';
require_once __DIR__ . '/../Event.php';

class SendReminderCommand implements EventCommandInterface {
    private $receiver;
    private $event;
    private $eventId;

    public function __construct($receiver, $event, $eventId) {
        $this->receiver = $receiver;
        $this->event    = $event;
        $this->eventId  = $eventId;
    }

    public function execute() {
        $db = Database::getInstance()->getConnection();
        $attendeeModel = new Attendee();
        $eventData = $this->event->find($this->eventId);

        $attendees = $attendeeModel->getByEvent($this->eventId);
        $message   = "Reminder for event: " . $eventData['title'];

        foreach ($attendees as $att) {
            // Which reminder methods?
            $methods = !empty($att['reminder_methods'])
                ? explode(',', $att['reminder_methods'])
                : [];

            foreach ($methods as $method) {
                $recipient = null;

                if ($method === 'email') {
                    $recipient = $att['email'];
                } elseif (in_array($method, ['sms','whatsapp'])) {
                    $recipient = $att['phone'];
                }

                if (!$recipient) continue;

                // Insert into notifications table
                $stmt = $db->prepare("
                    INSERT INTO notifications (event_id, recipient, channel, message)
                    VALUES (?, ?, ?, ?)
                ");
                $stmt->bind_param("isss", $this->eventId, $recipient, $method, $message);
                $stmt->execute();

                // Log it
                $log = new EventLog();
                $log->addLog($this->eventId, 'SendReminder', json_encode([
                    'user_type' => $att['user_type'],
                    'user_id'   => $att['user_id'],
                    'channel'   => $method,
                    'recipient' => $recipient,
                    'message'   => $message
                ]));
            }
        }
    }
}
