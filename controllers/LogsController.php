<?php
require_once __DIR__ . '/../config/Database.php';

class LogsController
{
    private $connection;

    public function __construct()
    {
        $this->connection = Database::getInstance()->getConnection();
    }

    public function showLogs()
    {
        $sql = "SELECT * FROM action_logs ORDER BY timestamp DESC LIMIT 100";
        $result = mysqli_query($this->connection, $sql);

        require_once __DIR__ . '/../views/logs.html';
    }
}
