<?php
session_start();


// Base configuration
define('ROOT_PATH', __DIR__);
define('BASE_URL', '/querykicks');

// Autoload classes
spl_autoload_register(function ($class) {
    $paths = [
        ROOT_PATH . '/controllers/' . $class . '.php',
        ROOT_PATH . '/models/' . $class . '.php'
    ];
    
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

// Basic routing
$request = $_SERVER['REQUEST_URI'];
$request = str_replace('/querykicks/', '', $request);

// Route to appropriate page
if (empty($request) || $request === 'index.php') {
    if (!$isLoggedIn) {
        header('Location: ' . BASE_URL . '/views/auth.php');
    } else if ($isAdmin) {
        header('Location: ' . BASE_URL . '/views/admin.php');
    } else {
        header('Location: ' . BASE_URL . '/controllers/StoreController.php');
    }
    exit();
}

// Handle 404
http_response_code(404);
include ROOT_PATH . '/views/404.php';