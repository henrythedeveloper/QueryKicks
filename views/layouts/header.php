<?php
/**
 * header.php: This file serves as the header component for the Query Kicks application. 
 * It displays the site logo, user information, and navigation links dynamically based on user data.
 *
 * Features:
 *  - **Site Title and Logo**: Displays the Query Kicks logo as part of the site branding.
 *  - **User Section**:
 *      - Shows the user's name and current balance.
 *      - Includes a dynamic "Add Money" button for topping up the virtual balance.
 *  - **Dynamic User Data**: Retrieves user information (e.g., name, balance, role) from session data or a fallback array.
 *
 * Data Dependencies:
 *  - `$userData`: An associative array containing:
 *      - `id`: User ID from the session.
 *      - `name`: User's name, defaults to "Guest" if not logged in.
 *      - `balance`: User's virtual balance, defaults to 0.
 *      - `role`: User's role, defaults to "user".
 *
 * Linked Assets:
 *  - `/querykicks/assets/images/logo.webp`: Path to the Query Kicks logo.
 *  - CSS for styling elements like `.main-header`, `.user-section`, and `.sparkle-wrapper`.
 *  - Optional icons for currency representation (`.currency-icon-small`).
 *
 * Authors: Henry Le and Brody Sprouse
 * Version: 20241203
 */

if (!isset($userData)) {
    $userData = [
        'id' => $_SESSION['user_id'] ?? null,
        'name' => $_SESSION['name'] ?? 'Guest',
        'balance' => $_SESSION['money'] ?? 0,
        'role' => $_SESSION['role'] ?? 'user'
    ];
}
?>
<header class="main-header">
    <div class="nav-container">
        <div class="nav-left">
            <h1 class="site-title"><img class="logo" src="/querykicks/assets/images/logo.webp" alt="QueryKicks Logo"></h1>
        </div>
        <div class="nav-right">
            <div class="user-section">
                <div class="money-section">
                    <div class="user-money">
                        <div class="user-info">
                            <span class="username"><?= htmlspecialchars($userData['name']) ?></span>
                            <span class="balance" data-balance="<?= $userData['balance'] ?>">
                            <span class="sparkle-wrapper">
                                <span class="sparkle"></span>
                                <span class="sparkle"></span>
                                <span class="sparkle"></span>
                                <span class="sparkle"></span>
                                <i class="currency-icon-small"></i>
                            </span>
                                <?= number_format($userData['balance'], 2) ?>
                            </span>
                        </div>
                        <button type="button" class="add-money-btn">Add Money</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
