<?php

class Beneficiary
{
    protected $id;
    protected $name;
    protected $email;
    protected $phone;
    protected $address;

    public function __construct($name, $email, $phone, $address = null, $id = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->phone = $phone;
        $this->address = $address;
    }

    // ===== Getters and setters =====
    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id = $id;
    }

    public function getName()
    {
        return $this->name;
    }
    public function setName($name)
    {
        $this->name = $name;
    }

    public function getEmail()
    {
        return $this->email;
    }
    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getPhone()
    {
        return $this->phone;
    }
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    public function getAddress()
    {
        return $this->address;
    }
    public function setAddress($address)
    {
        $this->address = $address;
    }

    // ===== Insert beneficiary into DB =====
    public function insert($conn)
    {
        $stmt = $conn->prepare("
            INSERT INTO beneficiaries (name, email, phone, address) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param("ssss", $this->name, $this->email, $this->phone, $this->address);

        if ($stmt->execute()) {
            $this->id = $stmt->insert_id; // get the inserted ID
            return true;
        } else {
            return false;
        }
    }

    // ===== Get Beneficiary by ID =====
    public static function getById($id)
    {
        if ($id <= 0) return null;

        $conn = Database::getInstance()->getConnection();
        $stmt = $conn->prepare("
            SELECT id, name, email, phone, address 
            FROM beneficiaries 
            WHERE id = ?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if ($result) {
            return new Beneficiary(
                $result['name'],
                $result['email'],
                $result['phone'],
                $result['address'] ?? null,
                $result['id']
            );
        }
        return null;
    }

    // ===== Beneficiaries =====
    public static function getAll(): array
    {
        $conn = Database::getInstance()->getConnection();
        $sql = "SELECT * FROM beneficiaries ORDER BY id DESC";
        $result = $conn->query($sql);

        $items = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $items[] = new Beneficiary(
                    $row['name'],
                    $row['email'],
                    $row['phone'],
                    $row['address'] ?? '',
                    $row['id']
                );
            }
        }
        return $items;
    }

}
?>
