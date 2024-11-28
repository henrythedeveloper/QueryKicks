/**
 * auth.js: This file defines the `AuthManager` class, which manages user authentication 
 * operations such as login, registration, and password reset for the Query Kicks application. 
 * It provides a seamless user experience by handling form submissions, interacting with the 
 * server via AJAX, and dynamically updating the UI based on server responses.
 *
 * The following functionalities are included:
 * 
 * 1. **Initialization**:
 *    - Sets up event listeners for authentication forms.
 *    - Manages dynamic form switching between login, registration, and password reset.
 * 
 * 2. **Authentication Operations**:
 *    - `handleLogin(e)`: Submits login credentials and redirects on success.
 *    - `handleRegister(e)`: Submits registration details and transitions to the login form on success.
 *    - `handleForgotPassword(e)`: Handles password reset operations in two stages:
 *        - Step 1: Checks if the provided email exists in the system.
 *        - Step 2: Resets the password if the email is valid and a new password is provided.
 * 
 * 3. **UI Management**:
 *    - `showForm(formType)`: Toggles between login, registration, and forgot password forms.
 *    - `showError(message)`: Displays error messages for the user.
 * 
 * 4. **Server Communication**:
 *    - `sendRequest(formData)`: Sends asynchronous requests to the server's authentication controller 
 *      using `fetch` and processes JSON responses.
 * 
 * 5. **Global Integration**:
 *    - Initializes an `AuthManager` instance when the DOM is fully loaded.
 *    - Exposes a global `showForm(formType)` function for seamless form toggling.
 * 
 * Features:
 *  - Modular structure for maintainability and scalability.
 *  - Encapsulation of authentication logic and UI updates.
 *  - Error handling for network or server issues during authentication.
 * 
 * Dependencies:
 *  - Server-side authentication endpoint: `/querykicks/controllers/AuthController.php`
 *  - HTML form elements: `login-form`, `register-form`, and `forgot-form`.
 * 
 * Authors: Henry Le and Brody Sprouse
 * Version: 20241203
 */

class AuthManager {
    constructor() {
        this.setupEventListeners();
        // Use absolute path from project root
        this.controllerPath = '/querykicks/controllers/AuthController.php';
        // Base URL for redirects
        this.baseUrl = '/querykicks';
    }

    setupEventListeners() {
        document.getElementById('login-form')?.addEventListener('submit', (e) => this.handleLogin(e));
        document.getElementById('register-form')?.addEventListener('submit', (e) => this.handleRegister(e));
        document.getElementById('forgot-form')?.addEventListener('submit', (e) => this.handleForgotPassword(e));
    }

    showForm(formType) {
        document.querySelectorAll('.auth-form').forEach(form => form.style.display = 'none');
        document.getElementById(`${formType}-form`).style.display = 'block';
        document.getElementById('error-message').textContent = '';
    }

    showError(message) {
        document.getElementById('error-message').textContent = message;
    }

    async handleLogin(e) {
        e.preventDefault();
        const formData = new FormData();
        formData.append('action', 'login');
        formData.append('email', document.getElementById('login-email').value);
        formData.append('password', document.getElementById('login-password').value);

        try {
            const response = await this.sendRequest(formData);
            if (response.success) {
                window.location.href = response.redirectUrl;
            } else {
                alert(response.message);
            }
        } catch (error) {
            alert('An error occurred: ' + error.message);
        }
    }

    async handleRegister(e) {
        e.preventDefault();
        const formData = new FormData();
        formData.append('action', 'register');
        formData.append('name', document.getElementById('register-name').value);
        formData.append('email', document.getElementById('register-email').value);
        formData.append('password', document.getElementById('register-password').value);

        try {
            const response = await this.sendRequest(formData);
            if (response.success) {
                this.showForm('login');
                alert(response.message);
            } else {
                alert(response.message);
            }
        } catch (error) {
            alert('An error occurred: ' + error.message);
        }
    }

    async handleForgotPassword(e) {
        e.preventDefault();
        const email = document.getElementById('forgot-email').value;
        const newPasswordGroup = document.getElementById('new-password-group');
        const newPassword = document.getElementById('new-password').value;

        if (newPasswordGroup.style.display === 'none') {
            const formData = new FormData();
            formData.append('action', 'check_email');
            formData.append('email', email);

            try {
                const response = await this.sendRequest(formData);
                if (response.success) {
                    newPasswordGroup.style.display = 'block';
                } else {
                    alert(response.message);
                }
            } catch (error) {
                alert('An error occurred: ' + error.message);
            }
        } else {
            const formData = new FormData();
            formData.append('action', 'reset_password');
            formData.append('email', email);
            formData.append('new_password', newPassword);

            try {
                const response = await this.sendRequest(formData);
                if (response.success) {
                    this.showForm('login');
                    alert(response.message);
                } else {
                    alert(response.message);
                }
            } catch (error) {
                alert('An error occurred: ' + error.message);
            }
        }
    }

    async sendRequest(formData) {
        try {
            const response = await fetch(this.controllerPath, {
                method: 'POST',
                body: formData
            });
            return await response.json();
        } catch (error) {
            alert('An error occurred: ' + error.message);
            throw error;
        }
    }
}

// Initialize auth manager when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.authManager = new AuthManager();
});

// Global function for form switching (used by buttons)
function showForm(formType) {
    window.authManager.showForm(formType);
}