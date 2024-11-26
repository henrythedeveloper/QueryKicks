<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/User.php';

class AuthController {
    private $user;

    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();
        $this->user = new User($db);
    }

    public function login($email, $password) {
        $result = $this->user->login($email, $password);
        if($result) {
            session_start();
            $_SESSION['user_id'] = $result['id'];
            $_SESSION['name'] = $result['name'];
            $_SESSION['role'] = $result['role'];
            $_SESSION['money'] = $result['money'];
            return [
                'success' => true,
                'role' => $result['role'],
                'redirectUrl' => $result['role'] === 'admin' ? '/querykicks/views/admin.php' : '/querykicks/views/main.php'
            ];
        }
        return [
            'success' => false,
            'message' => 'Invalid email or password'
        ];
    }

    public function register($name, $email, $password) {
        if($this->user->emailExists($email)) {
            return [
                'success' => false, 
                'message' => 'Email already exists'
            ];
        }
        
        if($this->user->register($name, $email, $password)) {
            return [
                'success' => true, 
                'message' => 'Registration successful! Please login.'
            ];
        }
        return [
            'success' => false, 
            'message' => 'Registration failed'
        ];
    }

    public function checkEmail($email) {
        if($this->user->emailExists($email)) {
            return [
                'success' => true,
                'message' => 'Email found'
            ];
        }
        return [
            'success' => false,
            'message' => 'Email not found'
        ];
    }

    public function resetPassword($email, $newPassword) {
        if(!$this->user->emailExists($email)) {
            return [
                'success' => false, 
                'message' => 'Email not found'
            ];
        }
        
        if($this->user->updatePassword($email, $newPassword)) {
            return [
                'success' => true, 
                'message' => 'Password updated successfully! Please login.'
            ];
        }
        return [
            'success' => false, 
            'message' => 'Password update failed'
        ];
    }

    public function logout() {
        // Only start session if one doesn't exist
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Clear all session variables
        $_SESSION = array();
        
        // Destroy the session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time()-3600, '/');
        }
        
        // Destroy the session
        session_destroy();
        
        return [
            'success' => true,
            'message' => 'Logged out successfully',
            'redirectUrl' => '/querykicks/views/auth.php'
        ];
    }

    // Handle AJAX requests
    public function handleRequest() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid request method'
            ]);
            return;
        }
    
        // Get input data from either POST or JSON
        $input = json_decode(file_get_contents('php://input'), true);
        $action = $_POST['action'] ?? $input['action'] ?? '';
        
        // Debug
        error_log('POST data: ' . print_r($_POST, true));
        error_log('JSON data: ' . print_r($input, true));
        error_log('Action received: ' . $action);
    
        $response = [];
    
        switch($action) {
            case 'login':
                $email = $_POST['email'] ?? $input['email'] ?? '';
                $password = $_POST['password'] ?? $input['password'] ?? '';
                $response = $this->login($email, $password);
                break;
    
            case 'register':
                $name = $_POST['name'] ?? $input['name'] ?? '';
                $email = $_POST['email'] ?? $input['email'] ?? '';
                $password = $_POST['password'] ?? $input['password'] ?? '';
                $response = $this->register($name, $email, $password);
                break;
    
            case 'check_email':
                $email = $_POST['email'] ?? $input['email'] ?? '';
                $response = $this->checkEmail($email);
                break;
    
            case 'reset_password':
                $email = $_POST['email'] ?? $input['email'] ?? '';
                $newPassword = $_POST['new_password'] ?? $input['new_password'] ?? '';
                $response = $this->resetPassword($email, $newPassword);
                break;
    
            case 'logout':
                $response = $this->logout();
                break;
    
            default:
                $response = [
                    'success' => false,
                    'message' => 'Invalid action'
                ];
        }
    
        echo json_encode($response);
        exit();
    }
}

// Handle AJAX requests if this file is accessed directly
if (basename($_SERVER['PHP_SELF']) == 'AuthController.php') {
    $controller = new AuthController();
    $controller->handleRequest();
}