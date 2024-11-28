<?php

/** 
 * database.php: This file defines the Database class, which manages the connection 
 * to the Query Kicks application's MySQL database. It uses PDO for secure and efficient 
 * database interactions.
 *
 * The Database class:
 *  - Provides configuration details for connecting to the local MySQL database.
 *  - Implements a `getConnection()` method to establish and return a PDO connection.
 *  - Includes error handling to display connection issues during development.
 *
 * Features:
 *  - Uses PDO for database interactions, allowing prepared statements for security.
 *  - Configurable with database credentials for reuse across the application.
 *  - Sets PDO error mode to exception handling for easier debugging.
 *
 * Authors: Henry Le and Brody Sprouse
 * Version: 20241203
 */


class Database {
    private $host = "localhost";
    private $db_name = "query_kicks";
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            echo "Connection Error: " . $e->getMessage();
        }

        return $this->conn;
    }
}