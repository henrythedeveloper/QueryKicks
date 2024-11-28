/**
 * main.js: This file serves as the central initialization and orchestration
 * of the Query Kicks application, connecting various modular classes and utilities
 * to create a seamless user experience. The main functionalities include tab navigation,
 * cart management, money handling, idle timer, and logout handling.
 * 
 * The following components are included:
 * 
 * 1. **Constants**:
 *    - `API_ENDPOINTS`: Defines reusable API endpoints for the application.
 * 
 * 2. **Utilities**:
 *    - `Utilities` class: Provides helper methods like currency formatting and
 *      creating currency icons for consistent rendering.
 * 
 * 3. **Services**:
 *    - `ApiService` class: A reusable service for making API requests with
 *      support for GET and POST methods.
 * 
 * 4. **Features**:
 *    - `IdleTimer`: Tracks user inactivity and updates the virtual store clerk's messages.
 *    - `TabManager`: Handles tab navigation and displays relevant content for each tab.
 *    - `CartManager`: Manages cart functionality, including adding/removing items,
 *      updating quantities, and handling checkout processes.
 *    - `MoneyManager`: Manages the modal for adding virtual currency and updates
 *      the user's balance in real time.
 *    - `MessageManager`: Updates the virtual store clerk's messages dynamically
 *      based on user actions or application state.
 * 
 * 5. **Application**:
 *    - `StoreApp` class: Initializes and orchestrates all features, ensuring a cohesive
 *      user experience. Handles logout functionality and the FAQ section.
 * 
 * Key Features:
 *  - Modular structure with reusable classes and services.
 *  - Encapsulated functionality for better readability and maintainability.
 *  - Support for AJAX-based updates to enhance user interactivity.
 *  - Dynamic message handling for the virtual store clerk.
 * 
 * Authors: Henry Le and Brody Sprouse
 * Version: 20241203
 */


// constants.js
const API_ENDPOINTS = {
    STORE: '/querykicks/controllers/StoreController.php',
    AUTH: '/querykicks/controllers/AuthController.php'
};

// utils.js
class Utilities {
    static formatCurrency(amount) {
        return amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    static createCurrencyIcon(size = 'small') {
        return `<i class="currency-icon-${size}"></i>`;
    }
}

// services/ApiService.js
class ApiService {
    static async request(endpoint, method = 'GET', data = null) {
        try {
            const options = {
                method,
                headers: {
                    'Content-Type': 'application/json'
                }
            };
            
            if (data) {
                options.body = JSON.stringify(data);
            }

            const response = await fetch(endpoint, options);
            return await response.json();
        } catch (error) {
            console.error('API Error:', error);
            throw error;
        }
    }
}

// features/IdleTimer.js
class IdleTimer {
    constructor(timeout = 60000) {
        this.idleTime = 0;
        this.timeout = timeout;
        this.interval = null;
    }

    initialize() {
        this.interval = setInterval(() => this.increment(), this.timeout);
        document.addEventListener('mousemove', () => this.reset());
        document.addEventListener('keypress', () => this.reset());
    }

    increment() {
        this.idleTime++;
        if (this.idleTime >= 1) {
            MessageManager.updateClerkMessage('idle');
            this.reset();
        }
    }

    reset() {
        this.idleTime = 0;
    }
}

// features/TabManager.js
class TabManager {
    constructor() {
        this.tabMessageTypes = {
            'shoes': 'greetings',
            'cart': 'cartMessages',
            'about': 'about',
            'contact': 'contact',
            'faq': 'faq'
        };
    }

    initialize() {
        const tabButtons = document.querySelectorAll('.tab-button');
        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                const view = button.getAttribute('data-view');
                this.openTab(button, view);
            });
        });

        const defaultTab = document.querySelector('.tab-button.active');
        if (defaultTab) {
            const view = defaultTab.getAttribute('data-view');
            this.openTab(defaultTab, view);
        }
    }

    openTab(button, tabName) {
        document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));

        button.classList.add('active');
        const tabContent = document.getElementById(tabName);
        if (tabContent) {
            tabContent.classList.add('active');
        }

        MessageManager.updateClerkMessage(this.tabMessageTypes[tabName] || 'idle');
    }
}

// features/CartManager.js
class CartManager {
    constructor() {
        this.isProcessing = false;
        this.pendingRequest = null;
    }

    initialize() {
        this.initializeButtons();
    }

    initializeButtons() {
        document.querySelectorAll('.add-to-cart-button').forEach(button => {
            button.addEventListener('click', this.handleAddToCart.bind(this));
        });

        document.querySelectorAll('.quantity-btn').forEach(button => {
            button.addEventListener('click', this.handleQuantityUpdate.bind(this));
        });

        document.querySelectorAll('.remove-from-cart').forEach(button => {
            button.addEventListener('click', this.handleRemoveFromCart.bind(this));
        });

        const checkoutBtn = document.querySelector('.checkout-btn');
        if (checkoutBtn) {
            checkoutBtn.addEventListener('click', this.handleCheckout.bind(this));
        }
    }

    async handleAddToCart(e) {
        e.preventDefault();
        if (this.isProcessing) return;

        const button = e.target;
        if (button.disabled) return;

        this.isProcessing = true;
        button.disabled = true;

        try {
            const productId = button.getAttribute('data-product-id');
            const stock = parseInt(button.getAttribute('data-stock'), 10);
            const quantityInput = button.parentElement.querySelector('.quantity-input');
            const requestedQuantity = parseInt(quantityInput.value, 10) || 1;

            if (requestedQuantity > stock) {
                throw new Error('Cannot add more items than are in stock.');
            }

            const response = await ApiService.request(API_ENDPOINTS.STORE, 'POST', {
                action: 'addToCart',
                product_id: productId,
                quantity: requestedQuantity
            });

            if (response.success) {
                quantityInput.value = 1;
                await this.updateCartDisplay();
                MessageManager.updateClerkMessage('addToCart');
            } else {
                throw new Error(response.message || 'Failed to add item to cart');
            }
        } catch (error) {
            alert(error.message);
            MessageManager.updateClerkMessage('error');
        } finally {
            this.isProcessing = false;
            button.disabled = false;
        }
    }

    async handleRemoveFromCart(e) {
        e.preventDefault();
        if (this.isProcessing) return;

        const button = e.target;
        if (button.disabled) return;

        this.isProcessing = true;
        button.disabled = true;

        try {
            const cartItemId = button.getAttribute('data-item-id');
            if (!cartItemId) throw new Error('Invalid cart item');

            const response = await ApiService.request(API_ENDPOINTS.STORE, 'POST', {
                action: 'removeFromCart',
                cart_item_id: cartItemId
            });

            if (response.success) {
                await this.updateCartDisplay();
                MessageManager.updateClerkMessage('removeFromCart');
            } else {
                throw new Error(response.message || 'Failed to remove item');
            }
        } catch (error) {
            alert(error.message);
            MessageManager.updateClerkMessage('error');
        } finally {
            this.isProcessing = false;
            button.disabled = false;
        }
    }

    async handleQuantityUpdate(e) {
        e.preventDefault();
        if (this.isProcessing) return;

        const button = e.target;
        if (button.disabled) return;

        this.isProcessing = true;

        try {
            const itemId = button.getAttribute('data-item-id');
            const isIncrease = button.classList.contains('increase');
            const quantitySpan = button.parentElement.querySelector('.quantity');
            const currentQuantity = parseInt(quantitySpan.textContent, 10);
            const newQuantity = isIncrease ? currentQuantity + 1 : currentQuantity - 1;

            const response = await ApiService.request(API_ENDPOINTS.STORE, 'POST', {
                action: 'updateQuantity',
                item_id: itemId,
                change: newQuantity
            });

            if (response.success) {
                quantitySpan.textContent = newQuantity;
                this.updateItemSubtotal(button, newQuantity);
                this.updateQuantityButtons(button.closest('.quantity-controls'), newQuantity);
                this.updateCartTotal();
                MessageManager.updateClerkMessage(isIncrease ? 'increaseQuantity' : 'decreaseQuantity');
            } else {
                throw new Error(response.message || 'Failed to update quantity');
            }
        } catch (error) {
            alert(error.message);
            MessageManager.updateClerkMessage('error');
        } finally {
            this.isProcessing = false;
        }
    }

    async handleCheckout(e) {
        e.preventDefault();
        if (this.isProcessing) return;

        const button = e.target;
        if (button.disabled) return;

        this.isProcessing = true;
        button.disabled = true;

        try {
            const total = button.getAttribute('data-total');
            const response = await ApiService.request(API_ENDPOINTS.STORE, 'POST', {
                action: 'checkout',
                total: total
            });

            if (response.success) {
                this.updateBalance(response.newBalance);
                MessageManager.updateClerkMessage('checkout');
                location.reload();
            } else {
                throw new Error(response.message || 'Checkout failed');
            }
        } catch (error) {
            alert(error.message);
            MessageManager.updateClerkMessage('error');
        } finally {
            this.isProcessing = false;
            button.disabled = false;
        }
    }

    async updateCartDisplay() {
        try {
            const response = await ApiService.request(`${API_ENDPOINTS.STORE}?action=getCart`);
            const cartTab = document.getElementById('cart');
            
            if (response.success && cartTab) {
                cartTab.innerHTML = response.html;
                this.initializeButtons();
            } else {
                throw new Error(response.message || 'Failed to update cart display');
            }
        } catch (error) {
            console.error('Error updating cart display:', error);
            MessageManager.updateClerkMessage('error');
        }
    }

    updateItemSubtotal(button, quantity) {
        const itemDetails = button.closest('.cart-item-details');
        const priceElement = itemDetails.querySelector('.price');
        const priceText = priceElement.textContent.replace(/[^\d.-]/g, '');
        const price = parseFloat(priceText);
        const subtotalElement = itemDetails.querySelector('.subtotal');
        const newSubtotal = Utilities.formatCurrency((price * quantity).toFixed(2));
        subtotalElement.innerHTML = `Subtotal: ${Utilities.createCurrencyIcon('modal')}${newSubtotal}`;
    }

    updateQuantityButtons(controls, currentQuantity) {
        const decreaseButton = controls.querySelector('.quantity-btn.decrease');
        const increaseButton = controls.querySelector('.quantity-btn.increase');
        const stockLimit = parseInt(controls.getAttribute('data-stock-limit'));

        decreaseButton.disabled = currentQuantity <= 1;
        increaseButton.disabled = currentQuantity >= stockLimit;
    }

    updateCartTotal() {
        let total = 0;
        document.querySelectorAll('.cart-item').forEach(item => {
            const quantity = parseInt(item.querySelector('.quantity').textContent);
            const priceText = item.querySelector('.price').textContent.replace(/[^\d.-]/g, '');
            const price = parseFloat(priceText);
            total += quantity * price;
        });

        const totalElement = document.querySelector('.receipt-total span');
        if (totalElement) {
            totalElement.innerHTML = `${Utilities.createCurrencyIcon('modal')}${Utilities.formatCurrency(total.toFixed(2))}`;
        }

        const checkoutBtn = document.querySelector('.checkout-btn');
        if (checkoutBtn) {
            checkoutBtn.setAttribute('data-total', total.toFixed(2));
        }
    }

    updateBalance(newBalance) {
        const balanceElement = document.querySelector('.balance');
        if (balanceElement) {
            balanceElement.innerHTML = `${Utilities.createCurrencyIcon('small')}${parseFloat(newBalance).toFixed(2)}`;
            balanceElement.setAttribute('data-balance', newBalance);
        }
    }
}

// features/MoneyManager.js
class MoneyManager {
    initialize() {
        this.initializeModal();
        this.initializeForm();
    }

    initializeModal() {
        const addMoneyBtn = document.querySelector('.add-money-btn');
        const modal = document.getElementById('add-money-modal');
        const closeBtn = document.querySelector('.close-modal');

        if (addMoneyBtn) {
            addMoneyBtn.addEventListener('click', () => modal.style.display = 'block');
        }

        if (closeBtn) {
            closeBtn.addEventListener('click', () => modal.style.display = 'none');
        }

        window.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        });
    }

    initializeForm() {
        const form = document.getElementById('add-money-form');
        if (form) {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();

                const amount = document.getElementById('amount').value;
                if (!amount || amount <= 0) {
                    alert('Please enter a valid amount');
                    return;
                }

                try {
                    const response = await ApiService.request(API_ENDPOINTS.STORE, 'POST', {
                        action: 'addMoney',
                        amount: parseFloat(amount)
                    });

                    if (response.success) {
                        const balanceElement = document.querySelector('.balance');
                        if (balanceElement) {
                            balanceElement.innerHTML = `${Utilities.createCurrencyIcon('small')}${parseFloat(response.newBalance).toFixed(2)}`;
                            balanceElement.setAttribute('data-balance', response.newBalance);
                        }

                        modal.style.display = 'none';
                        form.reset();
                        alert('Money added successfully!');
                        MessageManager.updateClerkMessage('addMoney');
                    } else {
                        throw new Error(response.message || 'Failed to add money');
                    }
                } catch (error) {
                    alert(error.message);
                    MessageManager.updateClerkMessage('error');
                }
            });
        }
    }
}
// features/MessageManager.js
class MessageManager {
    static updateClerkMessage(type) {
        const clerkMessageElement = document.querySelector('.clerk-speech p');
        if (clerkMessageElement) {
            const messages = clerkMessages[type];
            if (messages?.length) {
                const message = messages[Math.floor(Math.random() * messages.length)];
                clerkMessageElement.textContent = message.replace('{username}', this.getUsername());
            } else {
                clerkMessageElement.textContent = "How can I help you today";
            }
        }
    }

    static getUsername() {
        return username || 'Shopper';
    }
}

// app.js
class StoreApp {
    constructor() {
        this.idleTimer = new IdleTimer();
        this.tabManager = new TabManager();
        this.cartManager = new CartManager();
        this.moneyManager = new MoneyManager();
    }

    initialize() {
        document.addEventListener('DOMContentLoaded', () => {
            this.idleTimer.initialize();
            this.tabManager.initialize();
            this.cartManager.initialize();
            this.moneyManager.initialize();
            this.setupLogout();
            this.initializeFAQ();
        });
    }

    setupLogout() {
        const logoutBtn = document.querySelector('.logout-button');
        logoutBtn?.addEventListener('click', async () => {
            try {
                const response = await ApiService.request(API_ENDPOINTS.AUTH, 'POST', { action: 'logout' });
                if (response.success) {
                    window.location.href = '/querykicks/views/auth.php';
                }
            } catch (error) {
                alert('Error logging out. Please try again. ' + error.message);
            }
        });
    }

    initializeFAQ() {
        document.querySelectorAll('.faq-item').forEach(item => {
            const question = item.querySelector('.faq-question');
            question.addEventListener('click', () => {
                item.classList.toggle('active');
                const toggleIcon = question.querySelector('.faq-toggle');
                toggleIcon.textContent = item.classList.contains('active') ? '-' : '+';
                
                if (item.classList.contains('active')) {
                    MessageManager.updateClerkMessage('faq');
                }
            });
        });
    }
}

// Initialize the application
const app = new StoreApp();
app.initialize();