<?php
session_start();

// Base path configuration
define('BASE_PATH', __DIR__);

// Route handling
$request = $_SERVER['REQUEST_URI'];

// Basic routing logic
switch ($request) {
    case '/':
    case '/querykicks':
    case '/querykicks/':
        if (isset($_SESSION['user_id'])) {
            if ($_SESSION['role'] === 'admin') {
                header('Location: /querykicks/controllers/AdminController.php');
            } else {
                header('Location: /querykicks/controllers/StoreController.php');
            }
        } else {
            header('Location: /querykicks/controllers/AuthController.php');
        }
        exit();
        break;

    case '/querykicks/logout':
        // Clear all session variables
        $_SESSION = array();
        
        // Destroy the session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time()-3600, '/');
        }
        
        // Destroy the session
        session_destroy();
        
        // Redirect to auth page
        header('Location: /querykicks/controllers/AuthController.php');
        exit();
        break;

    // Add other routes as needed
    default:
        // Handle 404 or redirect to home
        header('Location: /querykicks/');
        exit();
}