<?php
class UserModel {
    private $db;

    public function __construct() {
        $this->db = Database::connect(); // Assumes you have a Database class for connections
    }

    public function getUserByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createUser($name, $email, $password) {
        // Check if the email already exists
        $existingUser = $this->getUserByEmail($email);
        if ($existingUser) {
            // Email already exists, return false or handle as needed
            return false;
        }

        // Insert the new user if email is unique
        $stmt = $this->db->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        return $stmt->execute();
    }
}
