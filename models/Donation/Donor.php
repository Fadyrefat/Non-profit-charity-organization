<?php
class Donor {
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
        $donors[] = $row;
    }
    return $donors;
}
} //fgsffrbf
