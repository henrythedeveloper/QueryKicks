<?php
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
                <?php if ($userData['role'] === 'admin'): ?>
                    <a href="/querykicks/views/admin.php" class="admin-link">Admin Panel</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>
