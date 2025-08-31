<?php
require_once 'config/Database.php';
require_once 'CharityFacade.php';
// 🔄 Setup database if needed
$conn = Database::getInstance()->getConnection();
require_once __DIR__ . '/config/init_db.php';


// 🌐 Detect request method and action
$requestMethod = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? $_POST['action'] ?? 'home';

$data = [];
if ($requestMethod === 'GET') {
    $data = $_GET;
} elseif ($requestMethod === 'POST') {
    $data = $_POST;
}
$facade = new CharityFacade();
$facade->handleRequest($action, $requestMethod, $data);