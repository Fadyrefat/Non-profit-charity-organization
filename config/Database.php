<?php
class Database {
    private static $instance = null;
    private $connection;

    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "nonprofit";

    private function __construct() {
        // Connect to MySQL without selecting DB
        $this->connection = mysqli_connect($this->host, $this->username, $this->password);
        if (!$this->connection) {
            die("❌ DB connection failed: " . mysqli_connect_error());
        }

        // Create database if not exists
        $createDB = "CREATE DATABASE IF NOT EXISTS $this->dbname";
        if (!mysqli_query($this->connection, $createDB)) {
            die("❌ Database creation failed: " . mysqli_error($this->connection));
        }

        // Select the database
        if (!mysqli_select_db($this->connection, $this->dbname)) {
            die("❌ Database selection failed: " . mysqli_error($this->connection));
        }
    }

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }
}
