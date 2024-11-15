<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="/querykicks/public/scss/styles.css">
</head>
<body>
    <div class="auth-background">
        <div class="auth-container">
            <div class="auth-toggle-buttons">
                <button class="auth-toggle active" data-form="login-form">Login</button>
                <button class="auth-toggle" data-form="register-form">Register</button>
            </div>
            
            <!-- Login Form -->
            <div id="login-form" class="auth-form active">
                <form action="/querykicks/index.php?controller=UserController&action=login" method="post">
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <button type="submit">Login</button>
                </form>
            </div>
            
            <!-- Register Form -->
            <div id="register-form" class="auth-form">
                <form action="/querykicks/index.php?controller=UserController&action=register" method="post">
                    <input type="text" name="name" placeholder="Name" required>
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <button type="submit">Register</button>
                </form>
            </div>

            <!-- Error Message Display -->
            <?php if (isset($_GET['error'])): ?>
                <div class="error-message">
                    <?php
                    if ($_GET['error'] === 'email_exists') {
                        echo "Email already exists. Please use a different email.";
                    } elseif ($_GET['error'] === 'invalid_credentials') {
                        echo "Invalid email or password.";
                    }
                    ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="/querykicks/public/js/main.js" type="module"></script>
</body>
</html>
