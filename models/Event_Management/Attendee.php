<?php
require_once __DIR__ . '/../../config/Database.php';

class Attendee
{
    private $conn;

    public function __construct()
    {
        $this->conn = Database::getInstance()->getConnection();
    }

    // Register attendee (link to donor/volunteer/beneficiary)
    public function register($eventId, $userType, $userId, $ticketType, $reminderMethods = null)
    {
        $stmt = $this->conn->prepare("
            INSERT INTO attendees (event_id, user_type, user_id, ticket_type, reminder_methods)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("isiss", $eventId, $userType, $userId, $ticketType, $reminderMethods);
        return $stmt->execute();
    }

    // Get all attendees for an event
    public function getByEvent($eventId)
    {
        $stmt = $this->conn->prepare("
            SELECT a.*, 
                   CASE a.user_type
                       WHEN 'donor' THEN (SELECT name FROM donors WHERE id = a.user_id)
                       WHEN 'volunteer' THEN (SELECT name FROM volunteers WHERE id = a.user_id)
                       WHEN 'beneficiary' THEN (SELECT name FROM beneficiaries WHERE id = a.user_id)
                   END AS name,
                   CASE a.user_type
                       WHEN 'donor' THEN (SELECT email FROM donors WHERE id = a.user_id)
                       WHEN 'volunteer' THEN (SELECT email FROM volunteers WHERE id = a.user_id)
                       WHEN 'beneficiary' THEN (SELECT email FROM beneficiaries WHERE id = a.user_id)
                   END AS email,
                   CASE a.user_type
                       WHEN 'donor' THEN (SELECT phone FROM donors WHERE id = a.user_id)
                       WHEN 'volunteer' THEN (SELECT phone FROM volunteers WHERE id = a.user_id)
                       WHEN 'beneficiary' THEN (SELECT phone FROM beneficiaries WHERE id = a.user_id)
                   END AS phone
            FROM attendees a
            WHERE a.event_id = ?
        ");
        $stmt->bind_param("i", $eventId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Update check-in
    public function updateAttendance($attendeeId, $checkedIn)
    {
        $stmt = $this->conn->prepare("
            UPDATE attendees SET checked_in = ? WHERE id = ?
        ");
        $stmt->bind_param("ii", $checkedIn, $attendeeId);
        return $stmt->execute();
    }

    // Get reminder methods for attendees of an event
    public function getReminderMethods($eventId)
    {
        $stmt = $this->conn->prepare("
            SELECT a.*, 
                   CASE a.user_type
                       WHEN 'donor' THEN (SELECT email FROM donors WHERE id = a.user_id)
                       WHEN 'volunteer' THEN (SELECT email FROM volunteers WHERE id = a.user_id)
                       WHEN 'beneficiary' THEN (SELECT email FROM beneficiaries WHERE id = a.user_id)
                   END AS email,
                   CASE a.user_type
                       WHEN 'donor' THEN (SELECT phone FROM donors WHERE id = a.user_id)
                       WHEN 'volunteer' THEN (SELECT phone FROM volunteers WHERE id = a.user_id)
                       WHEN 'beneficiary' THEN (SELECT phone FROM beneficiaries WHERE id = a.user_id)
                   END AS phone
            FROM attendees a
            WHERE a.event_id = ?
        ");
        $stmt->bind_param("i", $eventId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
