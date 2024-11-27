
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QueryKicks - Authentication</title>
    <link rel="stylesheet" href="/querykicks/assets/css/auth.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-buttons">
            <button onclick="showForm('login')">Login</button>
            <button onclick="showForm('register')">Register</button>
            <button onclick="showForm('forgot')">Forgot</button>
        </div>

        <!-- Login Form -->
        <form id="login-form" class="auth-form">
            <div class="form-group">
                <label for="login-email">Email</label>
                <input type="email" id="login-email" name="email" required>
            </div>
            <div class="form-group">
                <label for="login-password">Password</label>
                <input type="password" id="login-password" name="password" required>
            </div>
            <button type="submit" class="submit-btn">Login</button>
        </form>

        <!-- Register Form -->
        <form id="register-form" class="auth-form" style="display: none;">
            <div class="form-group">
                <label for="register-name">Name</label>
                <input type="text" id="register-name" name="name" required>
            </div>
            <div class="form-group">
                <label for="register-email">Email</label>
                <input type="email" id="register-email" name="email" required>
            </div>
            <div class="form-group">
                <label for="register-password">Password</label>
                <input type="password" id="register-password" name="password" required>
            </div>
            <button type="submit" class="submit-btn">Register</button>
        </form>

        <!-- Forgot Password Form -->
        <form id="forgot-form" class="auth-form" style="display: none;">
            <div class="form-group">
                <label for="forgot-email">Email</label>
                <input type="email" id="forgot-email" name="email" required>
            </div>
            <div class="form-group" id="new-password-group" style="display: none;">
                <label for="new-password">New Password</label>
                <input type="password" id="new-password" name="new-password">
            </div>
            <button type="submit" class="submit-btn">Reset Password</button>
        </form>

        <div id="error-message" class="error-message"></div>
    </div>

    <script src="/querykicks/assets/js/auth.js"></script>
</body>
</html>