<?php
require_once(__DIR__ . '/../../config/Database.php');

class EventLog
{
    private $conn;

    public function __construct()
    {
        $this->conn = Database::getInstance()->getConnection();
    }

    // Add a log entry
    public function addLog($eventId, $action, $payload = null)
    {
        $stmt = $this->conn->prepare("
            INSERT INTO event_logs (event_id, action, payload)
            VALUES (?, ?, ?)
        ");
        $stmt->bind_param("iss", $eventId, $action, $payload);
        return $stmt->execute();
    }

    // Get logs for an event
    public function getByEvent($eventId)
    {
        $stmt = $this->conn->prepare("SELECT * FROM event_logs WHERE event_id = ? ORDER BY created_at DESC");
        $stmt->bind_param("i", $eventId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
