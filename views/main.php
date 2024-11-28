<?php
/**
 * main.php: This file serves as the main store view for the Query Kicks application. 
 * It displays products, the shopping cart, and other store-related content dynamically 
 * based on user interaction.
 *
 * Features:
 *  - **Dynamic Tabs**: Includes tabs for Products, Cart, About, Contact, and FAQ, 
 *    allowing users to navigate between different views.
 *  - **Products View**: Displays the list of available products.
 *  - **Cart View**: Shows the items in the user's cart with options to modify or checkout.
 *  - **About, Contact, and FAQ Views**: Provide additional information about the store.
 *  - **Clerk Messages**: Dynamically displays greeting messages and other prompts 
 *    personalized with the user's name.
 *  - **Add Money Modal**: Allows users to add funds to their account balance.
 *  - **Header and Footer**: Includes reusable header and footer layouts.
 *
 * Linked Assets:
 *  - `/querykicks/assets/css/main.css`: Stylesheet for the main store page layout.
 *  - `/querykicks/assets/js/main.js`: Handles tab switching, modal functionality, 
 *    and interactions with clerk messages.
 *
 * Data Dependencies:
 *  - `$products`: An array of product data fetched by the controller.
 *  - `$greeting`: A personalized greeting message for the user.
 *  - `$clerkMessagesJson`: JSON-encoded data for dynamic clerk messages.
 *
 * Authors: Henry Le and Brody Sprouse
 * Version: 20241203
 */


if (!isset($products)) {
    // Redirect to storecontroller.php if $products is not set
    header('Location: /querykicks/controllers/storecontroller.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QueryKicks Store</title>
    <link rel="stylesheet" href="/querykicks/assets/css/main.css">
</head>
<body>
    <!-- Header Section -->
    <?php include __DIR__ . '/layouts/header.php'; ?>

    <!-- Main Content -->
    <div class="container store-background">
        <!-- Clerk Section -->
        <div class="header-section">
            <div class="clerk-speech">
                <p><?= htmlspecialchars($greeting) ?></p>
            </div>
        </div>

        <!-- Store Content -->
        <div class="main-content">
            <!-- Navigation Bar -->
            <div class="navbar">
                <button class="tab-button active" data-view="shoes">Products</button>
                <button class="tab-button" data-view="cart">Cart</button>
                <button class="tab-button" data-view="about">About</button>
                <button class="tab-button" data-view="contact">Contact</button>
                <button class="tab-button" data-view="faq">FAQ</button>
                <button class="logout-button" onclick="logout()">Leave Store</button>
            </div>

            <!-- Tab Content -->
            <div class="tab-wrapper">
                <!-- Products View -->
                <div id="shoes" class="tab-content active">
                    <?php include __DIR__ . '/store/products.php'; ?>
                </div>

                <!-- Cart View -->
                <div id="cart" class="tab-content">
                    <?php include __DIR__ . '/store/cart.php'; ?>
                </div>

                <!-- About View -->
                <div id="about" class="tab-content">
                    <?php include __DIR__ . '/store/about.php'; ?>
                </div>

                <!-- Contact View -->
                <div id="contact" class="tab-content">
                    <?php include __DIR__ . '/store/contact.php'; ?>
                </div>

                <!-- FAQ View -->
                <div id="faq" class="tab-content">
                    <?php include __DIR__ . '/store/faq.php'; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Money Modal -->
    <div id="add-money-modal" class="money-modal">
        <div class="money-modal-content">
            <span class="close-modal">&times;</span>
            <h2>Add Money to Balance</h2>
            <form id="add-money-form">
                <div class="form-group">
                    <label for="amount">Amount (<i class="currency-icon-modal"></i>)</label>
                    <input type="number" id="amount" name="amount" step="0.01" required>
                </div>
                <button type="submit" class="primary-btn">Add Money</button>
            </form>
        </div>
    </div>

    <?php include __DIR__ . '/layouts/footer.php'; ?>
    <script>
        const clerkMessages = <?= $clerkMessagesJson ?>;
        const username = <?= json_encode($_SESSION['name'] ?? 'Shopper') ?>;
    </script>
    <script src="/querykicks/assets/js/main.js"></script>
</body>
</html>