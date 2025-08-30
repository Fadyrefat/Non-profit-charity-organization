<?php

class Beneficiary
{
    protected $id;
    protected $name;
    protected $address;

    public function __construct($name, $address = null, $id = null)
    {
        $this->id = $id;
        $this->name = $name;
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
        $stmt = $conn->prepare("INSERT INTO beneficiaries (name, address) VALUES (?, ?)");
        $stmt->bind_param("ss", $this->name, $this->address);

        if ($stmt->execute()) {
            $this->id = $stmt->insert_id; // get the inserted ID
            return true;
        } else {
            return false;
        }
    }

    
    // Static method to fetch all beneficiaries
    public static function getBeneficiaries() {
        $conn = Database::getInstance()->getConnection();
        $sql = "SELECT * FROM beneficiaries";
        $result = mysqli_query($conn, $sql);

        $beneficiaries = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $beneficiaries[] = new Beneficiary($row['id'], $row['name'], $row['address']);
        }
        return $beneficiaries;
    }
}
?>
