<?php
class Inventory {

    public function __construct() {
        
    }

    // Get current inventory
    public static function getInventory() {
        $conn=Database::getInstance()->getConnection();
        $sql = "SELECT * FROM inventory WHERE id = 1";
        $result = mysqli_query($conn, $sql);
        return mysqli_fetch_assoc($result);
    }

    // Add donation
    public static function addDonation($food, $money, $clothes) {
        $food = (int)$food;
        $money = (float)$money;
        $clothes = (int)$clothes;
$conn=Database::getInstance()->getConnection();
        $sql = "UPDATE inventory 
                SET food = food + $food, 
                    money = money + $money, 
                    clothes = clothes + $clothes 
                WHERE id = 1";
        return mysqli_query($conn, $sql);
    }

    // Deduct beneficiary request
    public function useItems($food, $money, $clothes) {
        $food = (int)$food;
        $money = (float)$money;
        $clothes = (int)$clothes;
$conn=Database::getInstance()->getConnection();
        $sql = "UPDATE inventory 
                SET food = food - $food, 
                    money = money - $money, 
                    clothes = clothes - $clothes 
                WHERE id = 1";
        return mysqli_query($conn, $sql);
    }
}
?>
