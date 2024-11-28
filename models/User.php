<?php
/**
 * User.php: This file defines the User model, which encapsulates the logic for 
 * managing user data and authentication in the Query Kicks application.
 *
 * The User class:
 *  - Handles user login, registration, and password updates.
 *  - Provides methods to check if an email already exists in the database.
 *  - Utilizes prepared statements with PDO for secure database operations.
 *
 * Features:
 *  - `login($email, $password)`: Authenticates a user using email and password.
 *  - `register($name, $email, $password)`: Registers a new user with hashed passwords.
 *  - `updatePassword($email, $newPassword)`: Updates the user's password securely.
 *  - `emailExists($email)`: Checks if a given email is already registered.
 *
 * Authors: Henry Le and Brody Sprouse
 * Version: 20241203
 */

class User {
    private $conn;
    private $table = "users";

    public $id;
    public $name;
    public $email;
    public $password;
    public $money;
    public $role;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function login($email, $password) {
        $query = "SELECT id, name, email, password, role, money FROM " . $this->table . " WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$email]);

        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if(password_verify($password, $row['password'])) {
                return $row;
            }
        }
        return false;
    }

    public function register($name, $email, $password) {
        $query = "INSERT INTO " . $this->table . " (name, email, password) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        try {
            $stmt->execute([$name, $email, $hashedPassword]);
            return true;
        } catch(PDOException $e) {
            return false;
        }
    }

    public function updatePassword($email, $newPassword) {
        $query = "UPDATE " . $this->table . " SET password = ? WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        try {
            $stmt->execute([$hashedPassword, $email]);
            return true;
        } catch(PDOException $e) {
            return false;
        }
    }

    public function emailExists($email) {
        $query = "SELECT id FROM " . $this->table . " WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$email]);
        
        return $stmt->rowCount() > 0;
    }
}