document.addEventListener('DOMContentLoaded', () => {
    // Initialize all functionality
    console.log('DOM loaded');
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
    console.log('Initializing cart');
    
    // Add to cart buttons
    const addToCartButtons = document.querySelectorAll('.add-to-cart-button');
    addToCartButtons.forEach(button => {
        button.addEventListener('click', handleAddToCart);
    });
    
    // Quantity buttons
    const quantityButtons = document.querySelectorAll('.quantity-btn');
    quantityButtons.forEach(button => {
        button.addEventListener('click', handleQuantityUpdate);
    });
    
    // Remove buttons
    const removeButtons = document.querySelectorAll('.remove-from-cart');
    removeButtons.forEach(button => {
        button.addEventListener('click', handleRemoveFromCart);
    });
    
    // Checkout button
    const checkoutBtn = document.querySelector('.checkout-btn');
    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', handleCheckout);
    }
}

async function handleAddToCart(e) {
    e.preventDefault();
    console.log('Add to cart clicked');
    
    const productId = e.target.getAttribute('data-product-id');
    console.log('Product ID:', productId);

    try {
        const response = await fetch('/querykicks/controllers/StoreController.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'addToCart',
                product_id: productId,
                quantity: 1
            })
        });

        console.log('Response received');
        const data = await response.json();
        console.log('Response data:', data);

        if (data.success) {
            console.log('Successfully added to cart');
            // Update the cart display
            const cartTab = document.getElementById('cart');
            if (cartTab) {
                location.reload(); // Temporary solution to refresh the cart
            }
            // Update clerk message
            const clerkMessage = document.querySelector('.clerk-speech p');
            if (clerkMessage && data.clerkMessage) {
                clerkMessage.textContent = data.clerkMessage;
            }
        } else {
            console.log('Failed to add to cart:', data.message);
            alert(data.message || 'Failed to add item to cart');
        }
    } catch (error) {
        console.error('Error adding to cart:', error);
        alert('Error adding item to cart. Please try again.');
    }
}

async function handleRemoveFromCart(e) {
    const itemId = e.target.getAttribute('data-item-id');
    
    try {
        const response = await fetch('/querykicks/controllers/StoreController.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'removeFromCart',
                item_id: itemId
            })
        });

        const data = await response.json();
        if (data.success) {
            location.reload(); // Refresh to show updated cart
        } else {
            alert(data.message || 'Failed to remove item');
        }
    } catch (error) {
        console.error('Error removing item:', error);
        alert('Error removing item. Please try again.');
    }
}

async function handleQuantityUpdate(e) {
    const itemId = e.target.getAttribute('data-item-id');
    const isIncrease = e.target.classList.contains('increase');
    const change = isIncrease ? 1 : -1;
    
    try {
        const response = await fetch('/querykicks/controllers/StoreController.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'updateQuantity',
                item_id: itemId,
                change: change
            })
        });

        const data = await response.json();
        if (data.success) {
            location.reload(); // Refresh to show updated cart
        } else {
            alert(data.message || 'Failed to update quantity');
        }
    } catch (error) {
        console.error('Error updating quantity:', error);
        alert('Error updating quantity. Please try again.');
    }
}

async function handleCheckout(e) {
    const total = e.target.getAttribute('data-total');
    
    try {
        const response = await fetch('/querykicks/controllers/StoreController.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'checkout',
                total: total
            })
        });

        const data = await response.json();
        if (data.success) {
            alert('Checkout successful!');
            location.reload(); // Refresh the page to show empty cart
        } else {
            alert(data.message || 'Checkout failed');
        }
    } catch (error) {
        console.error('Error during checkout:', error);
        alert('Error during checkout. Please try again.');
    }
}


// Money Modal Functions
function initializeMoneyModal() {
    console.log('Initializing money modal');

    // Get modal elements
    const addMoneyBtn = document.querySelector('.add-money-btn');
    const modal = document.getElementById('add-money-modal');
    const closeBtn = document.querySelector('.close-modal');
    const form = document.getElementById('add-money-form');

    if (addMoneyBtn) {
        console.log('Add money button found');
        addMoneyBtn.addEventListener('click', () => {
            console.log('Add money button clicked');
            modal.style.display = 'block';
        });
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', () => {
            modal.style.display = 'none';
        });
    }

    // Close modal when clicking outside
    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });

    if (form) {
        form.addEventListener('submit', handleAddMoney);
    }
}

async function handleAddMoney(e) {
    e.preventDefault();
    console.log('Handling add money submission');

    const amount = document.getElementById('amount').value;
    if (!amount || amount <= 0) {
        alert('Please enter a valid amount');
        return;
    }

    try {
        const response = await fetch('/querykicks/controllers/StoreController.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'addMoney',
                amount: parseFloat(amount)
            })
        });

        const data = await response.json();
        console.log('Add money response:', data);

        if (data.success) {
            // Update the balance display
            const balanceElement = document.querySelector('.balance');
            if (balanceElement) {
                balanceElement.textContent = `$${parseFloat(data.newBalance).toFixed(2)}`;
            }

            // Close the modal
            document.getElementById('add-money-modal').style.display = 'none';
            
            // Reset the form
            document.getElementById('add-money-form').reset();

            // Show success message
            alert('Money added successfully!');
        } else {
            alert(data.message || 'Failed to add money');
        }
    } catch (error) {
        console.error('Error adding money:', error);
        alert('Error adding money. Please try again.');
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
            faq: "Got questions? We've got answers!",
            addToCart: "Great choice! Item added to your cart!",
            removeFromCart: "Item removed from your cart.",
            checkout: "Thank you for your purchase!",
            error: "Oops! Something went wrong."
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
    const cartTab = document.getElementById('cart');
    if (cartTab) {
        // Fetch updated cart content
        fetch('/querykicks/controllers/StoreController.php?action=getCart')
            .then(response => response.text())
            .then(html => {
                cartTab.innerHTML = html;
                console.log('Cart display updated');
            })
            .catch(error => console.error('Error updating cart display:', error));
    }
}