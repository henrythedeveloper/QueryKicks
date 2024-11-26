<footer class="main-footer">
    <div class="footer-content">
        <p>&copy; <?= date('Y') ?> QueryKicks. All rights reserved.</p>
        <?php if (isset($_SESSION['user_id'])): ?>
            <p class="user-status">Logged in as: <?= htmlspecialchars($_SESSION['name']) ?></p>
        <?php endif; ?>
    </div>
</footer>