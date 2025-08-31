<?php
require_once("Database.php");

$conn = Database::getInstance()->getConnection();

// Create donors table
mysqli_query($conn, "
    CREATE TABLE IF NOT EXISTS donors (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        phone VARCHAR(20) NOT NULL
    )
");

// Create action_logs table for logging system actions
mysqli_query($conn, "
    CREATE TABLE IF NOT EXISTS action_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        action VARCHAR(255) NOT NULL,
        method VARCHAR(10) NOT NULL,
        timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
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
        email VARCHAR(100) UNIQUE NOT NULL,
        phone VARCHAR(20) NOT NULL,
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


// Event Management

mysqli_query($conn, "
    CREATE TABLE IF NOT EXISTS events (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        type ENUM('workshop','fundraiser','outreach') NOT NULL,
        start_datetime DATETIME NOT NULL,
        end_datetime DATETIME NULL,
        capacity INT DEFAULT 0,
        location VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
");


mysqli_query($conn, "
    CREATE TABLE IF NOT EXISTS attendees (
        id INT AUTO_INCREMENT PRIMARY KEY,
        event_id INT NOT NULL,
        user_type ENUM('donor','volunteer','beneficiary') NOT NULL,
        user_id INT NOT NULL,
        ticket_type ENUM('General','VIP','VIP+') DEFAULT 'General',
        reminder_methods SET('email','sms','whatsapp') DEFAULT NULL,
        checked_in TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
    )
");


mysqli_query($conn, "
    CREATE TABLE IF NOT EXISTS event_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        event_id INT NOT NULL,
        action VARCHAR(100),
        payload TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
    )
");


// Notifications table (for Observer simulation)
mysqli_query($conn, "
    CREATE TABLE IF NOT EXISTS notifications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        event_id INT NOT NULL,
        recipient VARCHAR(255),
        channel ENUM('email','sms','whatsapp') NOT NULL,
        message TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
    )
");

mysqli_query($conn, "
    CREATE TABLE IF NOT EXISTS beneficiaryFeedback (
        id INT AUTO_INCREMENT PRIMARY KEY,
        request_id INT NOT NULL,
        beneficiary_id INT NOT NULL,
        satisfaction_rating TINYINT,
        outcome_notes TEXT,
        reported_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (request_id) REFERENCES requests(id),
        FOREIGN KEY (beneficiary_id) REFERENCES beneficiaries(id),
        UNIQUE KEY unique_feedback_per_request (request_id, beneficiary_id)
    );
");



mysqli_query($conn, "
    CREATE TABLE IF NOT EXISTS distributions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        request_id INT NOT NULL,
        beneficiary_id INT NOT NULL,
        resource_type VARCHAR(50) NOT NULL, -- Food, Clothes, Financial
        quantity INT NOT NULL,
        distributed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        distributed_by VARCHAR(100), -- staff/admin name
        FOREIGN KEY (request_id) REFERENCES requests(id),
        FOREIGN KEY (beneficiary_id) REFERENCES beneficiaries(id)
    );

");


mysqli_query($conn, "
    CREATE TABLE IF NOT EXISTS receipts(
        id INT AUTO_INCREMENT PRIMARY KEY,
        doner_id INT NOT NULL,
        doner_name VARCHAR(100),
        money DECIMAL(10,2) NOT NULL,
        clothes INT NOT NULL,
        foods INT NOT NULL
    )
");

mysqli_query($conn, "
    CREATE TABLE IF NOT EXISTS acknowledgment(
        id INT AUTO_INCREMENT PRIMARY KEY,
        message VARCHAR(200) NOT NULL
    )
");


?>
