// main.js

import { openTab } from './tabs.js';
import './modal.js';
import './navbar.js';
import './auth.js';

// Attach `openTab` to `window` to make it globally accessible
window.openTab = openTab;

// Additional initialization code
document.addEventListener('DOMContentLoaded', function() {
    openTab({ currentTarget: document.querySelector('.tab-button') }, 'shoes');
});
