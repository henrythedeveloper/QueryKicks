<?php
session_start();

require_once 'config/load_env.php';
require_once 'config/config.php';
require_once 'config/database.php';

$db = Database::connect();

// Redirect to login page if the user is not logged in and not accessing UserController
if (!isset($_SESSION['user_id']) && (!isset($_GET['controller']) || $_GET['controller'] !== 'UserController')) {
    header("Location: views/auth/auth.php");
    exit();
}


// Determine which controller and action to use
$controller = isset($_GET['controller']) ? $_GET['controller'] : 'StoreController';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

// Include and instantiate the controller
require_once "controllers/$controller.php";
$controller = new $controller;

// Execute the desired action
$controller->$action();
