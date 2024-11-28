/**
 * notification.js: This file defines the NotificationSystem class, which provides 
 * a dynamic, reusable system for displaying notifications in the Query Kicks application.
 *
 * Features:
 *  - **Notification Container**: Dynamically creates a container element for notifications.
 *  - **Custom Notifications**: Supports different types of notifications (e.g., success, error) 
 *    and configurable display durations.
 *  - **Auto-Hide**: Automatically hides notifications after a specified duration.
 *  - **Manual Dismissal**: Includes a close button for manually dismissing notifications.
 *  - **Integration with `alert`**: Overrides the default `alert` function to use the notification system.
 *
 * Methods:
 *  - `constructor()`: Initializes the notification system and creates the container.
 *  - `createContainer()`: Creates and appends the notification container to the DOM.
 *  - `show(message, type, duration)`: Displays a notification with a specified message, type, 
 *    and duration. Defaults to a "success" notification with a duration of 5000ms.
 *  - `hide(notification)`: Removes a notification from the DOM with a fade-out animation.
 *
 * Usage:
 *  - To display a notification:
 *      `notifications.show('This is a message', 'success', 3000);`
 *  - Types can include `success`, `error`, `warning`, etc., as defined in the CSS.
 *  - The overridden `window.alert` function automatically uses this system.
 *
 * Dependencies:
 *  - Requires CSS for styling `.notification`, `.notification-container`, and `.show`.
 *
 * Authors: Henry Le and Brody Sprouse
 * Version: 20241203
 */

class NotificationSystem {
    constructor() {
        this.container = this.createContainer();
    }

    createContainer() {
        const container = document.createElement('div');
        container.className = 'notification-container';
        document.body.appendChild(container);
        return container;
    }

    show(message, type = 'success', duration = 5000) {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        
        const messageSpan = document.createElement('span');
        messageSpan.textContent = message;
        
        const closeBtn = document.createElement('button');
        closeBtn.className = 'close-btn';
        closeBtn.textContent = '×';
        closeBtn.onclick = () => this.hide(notification);
        
        notification.appendChild(messageSpan);
        notification.appendChild(closeBtn);
        this.container.appendChild(notification);
        
        // Trigger reflow for animation
        notification.offsetHeight;
        
        // Show notification
        requestAnimationFrame(() => {
            notification.classList.add('show');
        });
        
        // Auto hide after duration
        if (duration) {
            setTimeout(() => {
                this.hide(notification);
            }, duration);
        }
    }

    hide(notification) {
        notification.classList.remove('show');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }
}

// Initialize notification system
const notifications = new NotificationSystem();

// Override default alert
window.alert = function(message) {
    notifications.show(message, 'success');
};