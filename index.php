<?php
require_once 'config/Database.php';

// 🔄 Setup database if needed
$conn = Database::getInstance()->getConnection();
require_once __DIR__ . '/config/init_db.php';

// 🌐 Detect request method and action
$requestMethod = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? $_POST['action'] ?? 'home';

// 📌 Handle GET requests
if ($requestMethod === 'GET') {
    switch ($action) {
        case 'home':
            require_once 'views/home.html';
            break;

        // ===== Donation Department =====
        case 'DonationDepartment':
            require_once 'controllers/DonationController.php';
            (new DonationController())->Index();
            break;

        case 'addDoner':
            require_once 'controllers/DonationController.php';
            (new DonationController())->DonerForm();
            break;

        case 'addDonation':
            require_once 'controllers/DonationController.php';
            (new DonationController())->DonationForm();
            break;

        // ===== Volunteer Department =====
        case 'VolunteerDepartment':
            require_once 'controllers/VolunteerController.php';
            (new VolunteerController())->Index();
            break;

        case 'addVolunteer':
            require_once 'controllers/VolunteerController.php';
            (new VolunteerController())->VolunteerForm();
            break;

        case 'showVolunteers':
            require_once 'controllers/VolunteerController.php';
            (new VolunteerController())->showVolunteers();
            break;

        case 'editVolunteer':
            require_once 'controllers/VolunteerController.php';
            (new VolunteerController())->editVolunteer($_GET['id']);
            break;

        case 'deleteVolunteer':
            require_once 'controllers/VolunteerController.php';
            (new VolunteerController())->deleteVolunteer($_GET['id']);
            break;

        // ===== Beneficiary Department =====
        case 'BeneficiaryDepartment':
            require_once "views/Beneficiary/index.html";
            break;

        case 'addBeneficiary':
            require_once "controllers/BeneficiaryController.php";
            (new BeneficiaryController())->addForm();
            break;

        case 'showBeneficiaries':
            require_once "controllers/BeneficiaryController.php";
            (new BeneficiaryController())->showAll();
            break;

        case 'addRequest':
            require_once "controllers/BeneficiaryController.php";
            (new BeneficiaryController())->addRequestForm();
            break;

        case 'showRequests':
            require_once "controllers/BeneficiaryController.php";
            (new BeneficiaryController())->showRequests();
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
            (new DonationController())->addDoner($_POST);
            break;

        case 'addDonation':
            require_once 'controllers/DonationController.php';
            (new DonationController())->addDonation($_POST);
            break;

        case 'addVolunteer':
            require_once 'controllers/VolunteerController.php';
            (new VolunteerController())->addVolunteer($_POST);
            break;

        case 'updateVolunteer':
            require_once 'controllers/VolunteerController.php';
            (new VolunteerController())->updateVolunteer($_POST);
            break;

        // You’ll likely want POST handling for Beneficiary + Requests here too
        case 'addBeneficiary':
            require_once 'controllers/BeneficiaryController.php';
            (new BeneficiaryController())->addBeneficiary($_POST);
            break;

        case 'addRequest':
            require_once 'controllers/BeneficiaryController.php';
            (new BeneficiaryController())->addRequest($_POST);
            break;

        default:
            echo "404 - Page not found (POST)";
    }
}
