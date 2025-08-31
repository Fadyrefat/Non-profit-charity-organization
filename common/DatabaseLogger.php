<?php
require_once __DIR__ . '/../config/Database.php';

class DatabaseLogger
{
    private $connection;

    public function __construct()
    {
        $this->connection = Database::getInstance()->getConnection();
    }

    public function log($action, $method)
    {
        $sql = "INSERT INTO action_logs (action, method) VALUES (?, ?)";
        $stmt = mysqli_prepare($this->connection, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $action, $method);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}
