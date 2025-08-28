<?php
require_once 'config/Database.php';

// 🔄 Run table setup if not already created
$conn = Database::getInstance()->getConnection();
$tableCheck = mysqli_query($conn, "SHOW TABLES LIKE 'donors'");
if (mysqli_num_rows($tableCheck) == 0) {
    require_once __DIR__ . '/config/init_db.php';
}

// 🌐 Detect request method
$requestMethod = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? $_POST['action'] ?? 'home';

// 📌 Handle GET requests
if ($requestMethod === 'GET') {
    switch ($action) {
        case 'home':
            require_once 'views/home.html';
            break;

        case 'DonationDepartment':
            require_once 'controllers/DonationController.php';
            $controller = new DonationController();
            $controller->Index();
            break;

        case 'addDoner':
            require_once 'controllers/DonationController.php';
            $controller = new DonationController();
            $controller->DonerForm();
            break;

        case 'addDonation' :
            require_once 'controllers/DonationController.php';
            $controller = new DonationController();
            $controller->DonationForm();
            break;

        default:
            echo "404 - Page not found (GET)";
    }
}

// 📌 Handle POST requests
elseif ($requestMethod === 'POST') {
    switch ($action) {
        case 'addDoner':
            require_once 'controllers/DonationController.php';
            $controller = new DonationController();
            $controller->addDoner($_POST);
            break;
        
        case 'addDonation':
            require_once 'controllers/DonationController.php';
            $controller = new DonationController();
            $controller->addDonation($_POST);
            break;

        default:
            echo "404 - Page not found (POST)";
    }
}

