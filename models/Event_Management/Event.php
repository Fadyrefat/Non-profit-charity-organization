<?php
require_once(__DIR__ . '/../../config/Database.php');
require_once 'Observer/ObserverInterface.php';

class Event {
    private $conn;
    private $observers = [];
    private static $instance = null;


    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Event();
        }
        return self::$instance;
    }

    public function subscribe(ObserverInterface $observer) {

        $this->observers[] = $observer;

    }

    public function unsubscribe(ObserverInterface $observer) {
        $this->observers = array_filter($this->observers, function($o) use ($observer) {
            return $o !== $observer;
        });
    }

    public function clearObservers() {
        $this->observers = [];
    }

    public function notify($eventId, $payload) {
        foreach ($this->observers as $observer) {
            $observer->update($eventId, $payload);
        }
    }


    public function create($data) {
        $stmt = $this->conn->prepare("
            INSERT INTO events (title, description, type, start_datetime, end_datetime, capacity, location)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param(
            "sssssis",
            $data['title'],
            $data['description'],
            $data['type'],
            $data['start_datetime'],
            $data['end_datetime'],
            $data['capacity'],
            $data['location']
        );
        $stmt->execute();
        return mysqli_insert_id($this->conn);
    }

    public function getAll() {
        $result = mysqli_query($this->conn, "SELECT * FROM events ORDER BY start_datetime DESC");
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function find($id) {
        $stmt = $this->conn->prepare("SELECT * FROM events WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
}
