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
                window.location.href = response.role === 'admin' 
                    ? `${this.baseUrl}/views/admin.php` 
                    : `${this.baseUrl}/views/main.php`;
            } else {
                this.showError(response.message);
            }
        } catch (error) {
            console.error('Error:', error);
            this.showError('An error occurred');
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
                this.showError(response.message);
            } else {
                this.showError(response.message);
            }
        } catch (error) {
            console.error('Error:', error);
            this.showError('An error occurred');
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
                    this.showError(response.message);
                }
            } catch (error) {
                console.error('Error:', error);
                this.showError('An error occurred');
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
                    this.showError(response.message);
                } else {
                    this.showError(response.message);
                }
            } catch (error) {
                console.error('Error:', error);
                this.showError('An error occurred');
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
            console.error('Fetch Error:', error);
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