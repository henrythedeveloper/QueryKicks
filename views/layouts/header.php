<?php// At the top of protected pages
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /querykicks/views/auth/auth.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Query Kicks - 8-Bit E-Commerce Store</title>
    <link rel="stylesheet" href="/querykicks/public/scss/styles.css">
    <link href="https://fonts.googleapis.com/css?family=Press+Start+2P" rel="stylesheet">
</head>
<body>
