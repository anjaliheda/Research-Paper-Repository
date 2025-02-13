<?php

class Database {
    private $host = 'localhost';
    private $dbname = 'research';
    private $username = 'root';
    private $password = '';
    private $connection;
    public function connect() {
        if ($this->connection === null) { 
            try {
                $this->connection = new PDO(
                    "mysql:host=$this->host;dbname=$this->dbname;charset=utf8mb4", 
                    $this->username,
                    $this->password
                );
                $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); 
            } catch (PDOException $e) {
                error_log("Database connection failed: " . $e->getMessage());
                throw new Exception("Database connection failed. Please check the logs.");
            }
        }
        return $this->connection;
    }
    public function __clone() {}
    public function __wakeup() {}
    public static function getInstance() {
        static $instance = null;
        if ($instance === null) {
            $instance = new self();
        }
        return $instance;
    }
}
?>