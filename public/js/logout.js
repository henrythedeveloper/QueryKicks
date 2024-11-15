export function logout(event) {
    event.preventDefault(); // Prevent default action if needed
    window.location.href = '/querykicks/logout.php'; // Redirect to logout.php
}

// Attach the logout function to the button
document.addEventListener('DOMContentLoaded', function () {
    const logoutButton = document.querySelector('.logout-button');
    if (logoutButton) {
        logoutButton.addEventListener('click', logout);
    }
});
