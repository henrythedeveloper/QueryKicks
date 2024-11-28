<?php
/** 
 * index.php: This file serves as the entry point and routing logic for the Query Kicks application. 
 * It checks the user's session status and redirects to the appropriate controller based on their role 
 * or authentication status.
 *
 * The routing logic handles:
 *  - Root paths ('/', '/querykicks', '/querykicks/') to redirect users to specific controllers 
 *    based on session and role information.
 *  - Logout functionality that clears the session and redirects to the authentication page.
 *  - A default route that redirects to the home page or handles 404 errors.
 *
 * Features:
 *  - Starts a user session and determines the appropriate controller for the request.
 *  - Handles user logout by clearing session variables, destroying cookies, and redirecting.
 *  - Serves as a centralized router for managing user flow in the application.
 *
 * Authors: Henry Le and Brody Sprouse
 * Version: 20241203
 */
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