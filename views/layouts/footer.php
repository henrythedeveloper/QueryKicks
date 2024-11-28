<?php
/**
 * footer.php: This file serves as the footer component for the Query Kicks application. 
 * It provides copyright information and optionally displays the logged-in user's status.
 *
 * Features:
 *  - **Copyright Notice**: Displays the current year dynamically using PHP.
 *  - **User Status**: If a user is logged in, shows their name based on session data.
 *
 * Data Dependencies:
 *  - Session data:
 *      - `$_SESSION['user_id']`: Determines if a user is logged in.
 *      - `$_SESSION['name']`: Displays the logged-in user's name.
 *
 * Linked Assets:
 *  - Expected to use CSS for styling the `.main-footer` and its child elements.
 *
 * Authors: Henry Le and Brody Sprouse
 * Version: 20241203
 */
?>
<footer class="main-footer">
    <div class="footer-content">
        <p>&copy; <?= date('Y') ?> QueryKicks. All rights reserved.</p>
        <?php if (isset($_SESSION['user_id'])): ?>
            <p class="user-status">Logged in as: <?= htmlspecialchars($_SESSION['name']) ?></p>
        <?php endif; ?>
    </div>
</footer>