<?php
require_once 'models/UserModel.php';

class UserController {
    public function login() {
        session_start();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = htmlspecialchars($_POST['email']);
            $password = $_POST['password'];
    
            $userModel = new UserModel();
            $user = $userModel->getUserByEmail($email);
    
            if ($user && password_verify($password, $user['password'])) {
                // Successful login, set session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
    
                // Redirect to the store page after login
                header("Location: /querykicks/index.php?controller=StoreController&action=index");
                exit();
            } else {
                // Redirect back to the login page with an error parameter
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
    
            $userModel = new UserModel();
            $isRegistered = $userModel->createUser($name, $email, $password);
    
            if ($isRegistered) {
                // Redirect to the login page after successful registration
                header("Location: /querykicks/index.php?controller=UserController&action=login");
                exit();
            } else {
                // Redirect back to the registration page with an error message
                header("Location: /querykicks/views/auth/auth.php?error=email_exists");
                exit();
            }
        } else {
            require 'views/auth/auth.php';
        }
    }
    
    
}
