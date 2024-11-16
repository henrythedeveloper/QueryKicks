<?php
require_once 'models/UserModel.php';

$userModel = new UserModel();
$userMoney = 0;

if (isset($_SESSION['user_id'])) {
    $userMoney = $userModel->getUserMoney($_SESSION['user_id']);
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
<div class="user-money">
    <span>Balance: $<?= number_format($userMoney, 2) ?></span>
    <button class="add-money-btn" onclick="openMoneyModal()">Add Money</button>
</div>

<!-- Money Modal -->
<div id="addMoneyModal" class="money-modal">
    <div class="money-modal-content">
        <span class="money-modal-close">&times;</span>
        <h2>Add Money</h2>
        <form id="addMoneyForm" novalidate>
            <div class="form-group">
                <label for="amount">Amount to Add ($):</label>
                <input type="number" id="amount" name="amount" min="1" step="0.01" required>
            </div>
            <button type="submit" class="submit-btn">Add Money</button>
        </form>
    </div>
</div>