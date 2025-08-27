<?php
require_once 'config/Database.php';

// 🔄 Run table setup if not already created
$conn = Database::getInstance()->getConnection();
$tableCheck = mysqli_query($conn, "SHOW TABLES LIKE 'donors'");
if (mysqli_num_rows($tableCheck) == 0) {
    require_once __DIR__ . '/config/init_db.php';
}

// 🌐 Routing
$action = $_GET['action'] ?? 'home';

switch ($action) {

    // ========== HOME ==========
case 'home':
    require_once 'views/home.html';
    break;

    // ========== DONOR MODULE ==========
    case 'DonationDepartment':
        require_once  'controllers/DonationController.php';
        $controller = new DonationController();
        $controller->Index();
        break;

}
