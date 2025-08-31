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
        case 'showDonors':
            require_once 'controllers/DonationController.php';
            $controller = new DonationController();
            $controller->showDonors();
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

        // ===== Approve / Reject / Complete Requests =====
        case 'approveRequest':
            if (isset($_GET['id'])) {
                require_once "controllers/BeneficiaryController.php";
                (new BeneficiaryController())->approveRequest((int) $_GET['id']);
            }
            break;

        case 'rejectRequest':
            if (isset($_GET['id'])) {
                require_once "controllers/BeneficiaryController.php";
                (new BeneficiaryController())->rejectRequest((int) $_GET['id']);
            }
            break;

        case 'completeRequest':
            if (isset($_GET['id'])) {
                require_once "controllers/BeneficiaryController.php";
                (new BeneficiaryController())->completeRequest((int) $_GET['id']);
            }
            break;

        case 'showDistributions':
            require_once "controllers/BeneficiaryController.php";
            (new BeneficiaryController())->showDistributions();
            break;

        // ===== Feedback =====
        case 'addFeedback': // show form
            if (isset($_GET['request_id']) && isset($_GET['beneficiary_id'])) {
                require_once "controllers/BeneficiaryController.php";
                (new BeneficiaryController())->addFeedbackForm(
                    (int) $_GET['request_id'],
                    (int) $_GET['beneficiary_id']
                );
            } else {
                echo "Missing request_id or beneficiary_id for feedback.";
            }
            break;

        case 'showFeedbacks':
            require_once "controllers/BeneficiaryController.php";
            (new BeneficiaryController())->showFeedbacks();
            break;

        // ===== Reports =====
        case 'generateReport':
            if (isset($_GET['reportType'])) {
                require_once "controllers/BeneficiaryController.php";
                (new BeneficiaryController())->generateReport($_GET['reportType']);
            } else {
                echo "Please select a report type.";
            }
            break;

        //Event Management
        case 'EventDepartment':
            require_once 'controllers/EventController.php';
            $controller = new EventController();
            $controller->index();
            break;

        case 'createEvent':
            require_once 'controllers/EventController.php';
            $controller = new EventController();
            $controller->create();
            break;

        case 'showEvent':
            require_once 'controllers/EventController.php';
            $controller = new EventController();
            $controller->show($_GET['id']);
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

        // Beneficiary + Requests
        case 'addBeneficiary':
            require_once 'controllers/BeneficiaryController.php';
            (new BeneficiaryController())->addBeneficiary($_POST);
            break;

        case 'addRequest':
            require_once 'controllers/BeneficiaryController.php';
            (new BeneficiaryController())->addRequest($_POST);
            break;

        case 'addFeedback': // 🔹 handle feedback form submission
            require_once 'controllers/BeneficiaryController.php';
            (new BeneficiaryController())->addFeedback($_POST);
            break;

        // Event Management
        case 'storeEvent':
            require_once 'controllers/EventController.php';
            $controller = new EventController();
            $controller->store($_POST);
            break;

        case 'registerAttendee':
            require_once 'controllers/EventController.php';
            $controller = new EventController();
            $controller->register($_POST);
            break;

        case 'bookTicket':
            require_once 'controllers/EventController.php';
            $controller = new EventController();
            $controller->bookTicket($_POST);
            break;

        case 'updateAttendance':
            require_once 'controllers/EventController.php';
            $controller = new EventController();
            $controller->updateAttendance($_POST);
            break;

        case 'sendReminder':
            require_once 'controllers/EventController.php';
            $controller = new EventController();
            $controller->sendReminder($_POST);
            break;

        default:
            echo "404 - Page not found (POST)";
    }
}
