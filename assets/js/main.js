document.addEventListener('DOMContentLoaded', () => {
    // Initialize all functionality
    initializeTabs();
    initializeCart();
    initializeMoneyModal();
    setupLogout();
});

// Tab Navigation
function initializeTabs() {
    const tabButtons = document.querySelectorAll('.tab-button');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            const view = button.getAttribute('data-view');
            openTab(button, view);
        });
    });

    // Set default tab
    const defaultTab = document.querySelector('.tab-button.active');
    if (defaultTab) {
        const view = defaultTab.getAttribute('data-view');
        openTab(defaultTab, view);
    }
}

function openTab(button, tabName) {
    // Remove active class from all buttons and content
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('active');
    });

    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.remove('active');
    });

    // Add active class to clicked button and corresponding content
    button.classList.add('active');
    const tabContent = document.getElementById(`${tabName}`);
    if (tabContent) {
        tabContent.classList.add('active');
    }

    // Update clerk message based on tab
    updateClerkMessage(tabName);
}

// Cart Functions
function initializeCart() {
    const addToCartButtons = document.querySelectorAll('.add-to-cart-button');
    const removeFromCartButtons = document.querySelectorAll('.remove-from-cart');
    const checkoutBtn = document.querySelector('.checkout-btn');

    addToCartButtons.forEach(button => {
        button.addEventListener('click', handleAddToCart);
    });

    removeFromCartButtons.forEach(button => {
        button.addEventListener('click', handleRemoveFromCart);
    });

    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', handleCheckout);
    }
}

async function handleAddToCart(e) {
    const productId = e.target.getAttribute('data-product-id');
    const price = parseFloat(e.target.getAttribute('data-price'));

    try {
        const response = await fetch('/querykicks/controllers/StoreController.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'addToCart',
                product_id: productId
            })
        });

        const data = await response.json();
        if (data.success) {
            updateCartDisplay();
            updateClerkMessage('addToCart');
        }
    } catch (error) {
        console.error('Error adding to cart:', error);
    }
}

// Money Modal Functions
function initializeMoneyModal() {
    const addMoneyBtn = document.querySelector('.add-money-btn');
    const modal = document.getElementById('add-money-modal');
    const closeBtn = modal.querySelector('.close-modal');
    const form = document.getElementById('add-money-form');

    addMoneyBtn?.addEventListener('click', () => {
        modal.style.display = 'block';
    });

    closeBtn?.addEventListener('click', () => {
        modal.style.display = 'none';
    });

    form?.addEventListener('submit', handleAddMoney);
}

async function handleAddMoney(e) {
    e.preventDefault();
    const amount = document.getElementById('amount').value;

    try {
        const response = await fetch('/querykicks/controllers/StoreController.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'addMoney',
                amount: amount
            })
        });

        const data = await response.json();
        if (data.success) {
            updateBalance(data.newBalance);
            document.getElementById('add-money-modal').style.display = 'none';
        }
    } catch (error) {
        console.error('Error adding money:', error);
    }
}

// Logout Function
function setupLogout() {
    const logoutBtn = document.querySelector('.logout-button');
    
    logoutBtn?.addEventListener('click', async () => {
        try {
            const response = await fetch('/querykicks/controllers/AuthController.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    action: 'logout'
                })
            });

            const data = await response.json();
            if (data.success) {
                window.location.href = '/querykicks/views/auth.php';
            }
        } catch (error) {
            console.error('Logout error:', error);
        }
    });
}

// Helper Functions
function updateClerkMessage(type) {
    const clerkMessage = document.querySelector('.clerk-speech p');
    if (clerkMessage) {
        // You can expand this with more message types
        const messages = {
            shoes: "Check out our awesome kicks!",
            cart: "Ready to check out?",
            about: "Want to learn more about us?",
            contact: "Need help? I'm here!",
            faq: "Got questions? We've got answers!"
        };
        clerkMessage.textContent = messages[type] || "How can I help you today?";
    }
}

function updateBalance(newBalance) {
    const balanceElement = document.querySelector('.balance');
    if (balanceElement) {
        balanceElement.textContent = `$${parseFloat(newBalance).toFixed(2)}`;
    }
}

function updateCartDisplay() {
    // Refresh cart contents
    const cartContent = document.getElementById('cart');
    if (cartContent) {
        location.reload(); // Simple refresh for now
    }
}