<?php
require_once 'config/Database.php';

// 🔄 Run table setup if not already created
$conn = Database::getInstance()->getConnection();
require_once __DIR__ . '/config/init_db.php';

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

        case 'addDonation':
            require_once 'controllers/DonationController.php';
            $controller = new DonationController();
            $controller->DonationForm();
            break;

        case 'VolunteerDepartment':
            require_once 'controllers/VolunteerController.php';
            $controller = new VolunteerController();
            $controller->Index();
            break;

        case 'addVolunteer':
            require_once 'controllers/VolunteerController.php';
            $controller = new VolunteerController();
            $controller->VolunteerForm();
            break;


        case 'showVolunteers':
            require_once 'controllers/VolunteerController.php';
            $controller = new VolunteerController();
            $controller->showVolunteers();
            break;

        case 'editVolunteer':
            require_once 'controllers/VolunteerController.php';
            $controller = new VolunteerController();
            $controller->editVolunteer($_GET['id']);
            break;

        case 'deleteVolunteer':
            require_once 'controllers/VolunteerController.php';
            $controller = new VolunteerController();
            $controller->deleteVolunteer($_GET['id']);
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

        case 'addVolunteer':
            require_once 'controllers/VolunteerController.php';
            $controller = new VolunteerController();
            $controller->addVolunteer($_POST);
            break;


        case 'updateVolunteer':
            require_once 'controllers/VolunteerController.php';
            $controller = new VolunteerController();
            $controller->updateVolunteer($_POST);
            break;

        default:
            echo "404 - Page not found (POST)";
    }
}

