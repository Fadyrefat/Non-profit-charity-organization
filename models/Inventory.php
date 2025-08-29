<?php
class Inventory {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get current inventory
    public function getInventory() {
        $sql = "SELECT * FROM inventory WHERE id = 1";
        $result = mysqli_query($this->conn, $sql);
        return mysqli_fetch_assoc($result);
    }

    // Add donation
    public function addDonation($food, $money, $clothes) {
        $food = (int)$food;
        $money = (float)$money;
        $clothes = (int)$clothes;

        $sql = "UPDATE inventory 
                SET food = food + $food, 
                    money = money + $money, 
                    clothes = clothes + $clothes 
                WHERE id = 1";
        return mysqli_query($this->conn, $sql);
    }

    // Deduct beneficiary request
    public function useItems($food, $money, $clothes) {
        $food = (int)$food;
        $money = (float)$money;
        $clothes = (int)$clothes;

        $sql = "UPDATE inventory 
                SET food = food - $food, 
                    money = money - $money, 
                    clothes = clothes - $clothes 
                WHERE id = 1";
        return mysqli_query($this->conn, $sql);
    }
}
?>
