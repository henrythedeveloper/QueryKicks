<?php
session_start();

// Destroy all session data
session_unset();
session_destroy();

// Redirect the user to the login page (or any other page)
header("Location: /querykicks/views/auth/auth.php");
exit();
