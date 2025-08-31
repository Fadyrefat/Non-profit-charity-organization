<?php
require_once 'Attendee.php';
require_once 'EventLog.php';

class EventReceiver
{
    private $attendee;
    private $log;

    public function __construct()
    {
        $this->attendee = new Attendee();
        $this->log = new EventLog();
    }

    public function registerAttendee($eventId, $attendeeData)
    {
        $this->attendee->register($attendeeData);
        $this->log->addLog($eventId, "register", json_encode($attendeeData));
        return $attendeeData;
    }

    public function bookTicket($eventId, $attendeeData)
    {
        $this->attendee->register($attendeeData);
        $this->log->addLog($eventId, "book_ticket", json_encode($attendeeData));
        return $attendeeData;
    }

    public function updateAttendance($attendeeId, $checkedIn)
    {
        $this->attendee->updateCheckIn($attendeeId, $checkedIn);
        $this->log->addLog(null, "update_attendance", "attendee $attendeeId checkin=$checkedIn");
    }

    public function sendReminder($eventId, $emails, $message)
    {
        $this->log->addLog($eventId, "reminder_sent", json_encode(['emails' => $emails, 'msg' => $message]));
        return ['emails' => $emails, 'msg' => $message];
    }
}
