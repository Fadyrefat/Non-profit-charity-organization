<?php
require_once __DIR__ . '/../Event_Management/Observer/ObserverInterface.php';
class Donor implements ObserverInterface{
    private $id;
    private $name;
    private $email;
    private $phone;

    public function __construct($id = null, $name = "", $email = "", $phone = "") {
        $this->id    = $id;
        $this->name  = $name;
        $this->email = $email;
        $this->phone = $phone;
    }

    // Getters
    public function getId() {
        return $this->id;
    }
    public function getName() {
        return $this->name;
    }
    public function getEmail() {
        return $this->email;
    }
    public function getPhone() {
        return $this->phone;
    }

    // Setters
    public function setName($name) {
        $this->name = $name;
    }
    public function setEmail($email) {
        $this->email = $email;
    }
    public function setPhone($phone) {
        $this->phone = $phone;
    }

    public static function create($name, $email, $phone) {
    $conn = Database::getInstance()->getConnection();
    $sql = "INSERT INTO donors (name, email, phone) VALUES ('$name', '$email', $phone)";
    return mysqli_query($conn, $sql);
    }

public static function getDonors() {
    $conn = Database::getInstance()->getConnection();
    $sql = "SELECT * FROM donors";
    $result = mysqli_query($conn, $sql);

    $donors = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $donors[] = new Donor($row['id'],$row['name'],$row['email'],$row['phone']);
    }
    return $donors;
}

    public static function getDonorByID($id): ?Donor {
        $conn = Database::getInstance()->getConnection();
        $sql = "SELECT * FROM donors WHERE id = $id LIMIT 1";
        $result = mysqli_query($conn, $sql);

        if ($row = mysqli_fetch_assoc($result)) {
            return new Donor($row['id'], $row['name'], $row['email'], $row['phone']);
        }

        return null; 
    }

public function update($eventId, $payload) {
    echo "Notification:\n";
    echo "Event ID: $eventId\n";
    echo "Payload: $payload\n\n";
}

} 
