<?php
require_once("Database.php");

$conn = Database::getInstance()->getConnection();

// Create donors table
mysqli_query($conn, "
    CREATE TABLE IF NOT EXISTS donors (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) UNIQUE,
        phone VARCHAR(20) NOT NULL
    )
");

// Create donations table
/*mysqli_query($conn, "
    CREATE TABLE IF NOT EXISTS donations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        donor_id INT NOT NULL,
        amount DECIMAL(10,2) NOT NULL,
        strategy VARCHAR(50) NOT NULL,  -- stores DonationStrategy (e.g., 'Cash', 'Online')
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (donor_id) REFERENCES donors(id) ON DELETE CASCADE
    )

");*/

// Create inventory table
mysqli_query($conn, "
    CREATE TABLE IF NOT EXISTS inventory (
        id INT AUTO_INCREMENT PRIMARY KEY,
        food INT DEFAULT 0,
        money DECIMAL(10,2) DEFAULT 0,
        clothes INT DEFAULT 0
    )
");

// Insert a default row if none exists
$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM inventory");
$row = mysqli_fetch_assoc($result);
if ($row['count'] == 0) {
    mysqli_query($conn, "INSERT INTO inventory (food, money, clothes) VALUES (0, 0, 0)");
}
// Create volunteers table
mysqli_query($conn, "
    CREATE TABLE IF NOT EXISTS volunteers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        phone VARCHAR(20) NOT NULL,
        hours INT DEFAULT 0,
        state VARCHAR(20) DEFAULT 'Helper'
    )
");



// ========== Beneficiaries Table ==========
mysqli_query($conn, "
    CREATE TABLE IF NOT EXISTS beneficiaries (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        address VARCHAR(255) NOT NULL
    )
");

mysqli_query($conn,"
CREATE TABLE IF NOT EXISTS FoodDonations(
        id INT AUTO_INCREMENT PRIMARY KEY,
        description VARCHAR(200),
        amount INT NOT NULL,
        donor_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (donor_id) REFERENCES donors(id) ON DELETE CASCADE
        )

");
mysqli_query($conn,"
CREATE TABLE IF NOT EXISTS ClothesDonations(
        id INT AUTO_INCREMENT PRIMARY KEY,
        description VARCHAR(200),
        amount INT NOT NULL,
        donor_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (donor_id) REFERENCES donors(id) ON DELETE CASCADE
        )

");
mysqli_query($conn,"
CREATE TABLE IF NOT EXISTS MoneyDonations(
        id INT AUTO_INCREMENT PRIMARY KEY,
        paymentmethod VARCHAR(50),
        amount DECIMAL(10,2) NOT NULL,
        donor_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (donor_id) REFERENCES donors(id) ON DELETE CASCADE
        )

");
=======
// ========== Requests Table ==========
mysqli_query($conn, "
    CREATE TABLE IF NOT EXISTS requests (
        id INT AUTO_INCREMENT PRIMARY KEY,
        beneficiary_id INT NOT NULL,
        request_type ENUM('Food','Clothes','Financial') NOT NULL,
        number INT NOT NULL,
        reason TEXT,
        state ENUM('Pending','Approved','Rejected','Completed') DEFAULT 'Pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (beneficiary_id) REFERENCES beneficiaries(id) ON DELETE CASCADE
    )
");
?>
