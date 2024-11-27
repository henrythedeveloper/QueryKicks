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