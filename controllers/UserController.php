<?php
require_once 'models/UserModel.php';

class UserController {
    private $userModel;

    public function __construct() {
        $this->userModel = new UserModel(); // Initialize userModel
    }

    public function login() {
        session_start();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = htmlspecialchars($_POST['email']);
            $password = $_POST['password'];
            $user = $this->userModel->getUserByEmail($email);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                header("Location: /querykicks/index.php?controller=StoreController&action=index");
                exit();
            } else {
                header("Location: /querykicks/views/auth/auth.php?error=invalid_credentials");
                exit();
            }
        } else {
            require 'views/auth/auth.php';
        }
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = htmlspecialchars($_POST['name']);
            $email = htmlspecialchars($_POST['email']);
            $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
            
            $isRegistered = $this->userModel->createUser($name, $email, $password);
            
            if ($isRegistered) {
                header("Location: /querykicks/index.php?controller=UserController&action=login");
                exit();
            } else {
                header("Location: /querykicks/views/auth/auth.php?error=email_exists");
                exit();
            }
        } else {
            require 'views/auth/auth.php';
        }
    }

    public function updateMoney() {
        ob_clean();
        header('Content-Type: application/json');

        try {
            if (!isset($_SESSION['user_id'])) {
                echo json_encode(['success' => false, 'message' => 'User not logged in']);
                return;
            }

            if (!isset($_POST['amount'])) {
                echo json_encode(['success' => false, 'message' => 'Amount is required']);
                return;
            }

            $amount = filter_var($_POST['amount'], FILTER_VALIDATE_FLOAT);
            if ($amount === false || $amount <= 0) {
                echo json_encode(['success' => false, 'message' => 'Invalid amount']);
                return;
            }

            $userId = $_SESSION['user_id'];
            if ($this->userModel->addMoney($userId, $amount)) {
                $newBalance = $this->userModel->getUserMoney($userId);
                echo json_encode([
                    'success' => true,
                    'message' => 'Money added successfully',
                    'newBalance' => $newBalance
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error updating balance']);
            }
        } catch (Exception $e) {
            error_log('Error in updateMoney: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Server error occurred']);
        }
    }
}

