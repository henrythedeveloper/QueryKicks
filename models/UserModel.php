<?php
class UserModel {
    private $db;

    public function __construct() {
        $this->db = Database::connect(); // Assumes you have a Database class for connections
    }

    public function getUserMoney($userId) {
        try {
            $stmt = $this->db->prepare("SELECT money FROM users WHERE id = :user_id");
            $stmt->execute(['user_id' => $userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? floatval($result['money']) : 0.00;
        } catch (PDOException $e) {
            error_log('Error getting user money: ' . $e->getMessage());
            return 0.00;
        }
    }

    public function updateUserMoney($userId, $newAmount) {
        $stmt = $this->db->prepare("UPDATE users SET money = :amount WHERE id = :user_id");
        return $stmt->execute([
            'amount' => $newAmount,
            'user_id' => $userId
        ]);
    }

    public function canAffordPurchase($userId, $amount) {
        $currentMoney = $this->getUserMoney($userId);
        return $currentMoney >= $amount;
    }

    public function getUserByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function processPurchase($userId, $amount) {
        try {
            $this->db->beginTransaction();
            
            // Get current balance
            $currentMoney = $this->getUserMoney($userId);
            if ($currentMoney < $amount) {
                $this->db->rollBack();
                return false;
            }
    
            // Update balance
            $newAmount = $currentMoney - $amount;
            $stmt = $this->db->prepare("UPDATE users SET money = :amount WHERE id = :user_id");
            $success = $stmt->execute([
                'amount' => $newAmount,
                'user_id' => $userId
            ]);
    
            if ($success) {
                $this->db->commit();
                return true;
            }
            
            $this->db->rollBack();
            return false;
            
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log('Error processing purchase: ' . $e->getMessage());
            return false;
        }
    }
    
    public function addMoney($userId, $amount) {
        try {
            $this->db->beginTransaction();
            
            // Get current balance
            $currentMoney = $this->getUserMoney($userId);
            $newAmount = $currentMoney + $amount;
            
            // Update balance
            $stmt = $this->db->prepare("UPDATE users SET money = :amount WHERE id = :user_id");
            $success = $stmt->execute([
                'amount' => $newAmount,
                'user_id' => $userId
            ]);

            if ($success) {
                $this->db->commit();
                return true;
            } else {
                $this->db->rollBack();
                return false;
            }
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log('Error adding money: ' . $e->getMessage());
            return false;
        }
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


