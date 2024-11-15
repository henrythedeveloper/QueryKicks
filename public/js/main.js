// main.js

import { openTab } from './tabs.js';
import './modal.js';
import './navbar.js';
import './auth.js';
import { logout } from './logout.js';

// Attach `openTab` to `window` to make it globally accessible
window.openTab = openTab;

// Additional initialization code
document.addEventListener('DOMContentLoaded', function() {
    openTab({ currentTarget: document.querySelector('.tab-button') }, 'shoes');
});

// Attach the logout function to the "Leave" button
document.addEventListener('DOMContentLoaded', function () {
    const leaveButton = document.querySelector('.signout-button');
    if (leaveButton) {
        leaveButton.addEventListener('click', logout);
    }
});