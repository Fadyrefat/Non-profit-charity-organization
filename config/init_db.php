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
mysqli_query($conn, "
    CREATE TABLE IF NOT EXISTS donations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        donor_id INT NOT NULL,
        amount DECIMAL(10,2) NOT NULL,
        strategy VARCHAR(50) NOT NULL,  -- stores DonationStrategy (e.g., 'Cash', 'Online')
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (donor_id) REFERENCES donors(id) ON DELETE CASCADE
    )
");

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


