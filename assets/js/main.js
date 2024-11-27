document.addEventListener('DOMContentLoaded', () => {
    // Initialize all functionality
    initializeTabs();
    initializeCart();
    initializeMoneyModal();
    setupLogout();
    initializeFAQ();
    initializeIdleTimer(); 
});

let idleTime = 0;
let idleInterval;

function initializeIdleTimer() {
    // Increment the idle time counter every minute.
    idleInterval = setInterval(timerIncrement, 60000); // 1 minute

    // Zero the idle timer on mouse movement or key press.
    document.addEventListener('mousemove', resetIdleTimer);
    document.addEventListener('keypress', resetIdleTimer);
}

function timerIncrement() {
    idleTime++;
    if (idleTime >= 1) { // The idle time threshold 
        updateClerkMessage('idle');
        idleTime = 0; // Reset idle time after displaying the message
    }
}

function resetIdleTimer() {
    idleTime = 0;
}

function updateClerkMessage(type) {
    const clerkMessageElement = document.querySelector('.clerk-speech p');
    if (clerkMessageElement) {
        const messages = clerkMessages[type];
        if (messages && messages.length > 0) {
            const message = getRandomMessage(messages);
            const personalizedMessage = message.replace('{username}', getUsername());
            clerkMessageElement.textContent = personalizedMessage;
        } else {
            clerkMessageElement.textContent = "How can I help you today?";
        }
    }
}

function getRandomMessage(messagesArray) {
    return messagesArray[Math.floor(Math.random() * messagesArray.length)];
}

function getUsername() {
    return username || 'Shopper';
}

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

    // Map tab names to message types
    const tabMessageTypes = {
        'shoes': 'greetings',
        'cart': 'cart',
        'about': 'about',
        'contact': 'contact',
        'faq': 'faq'
    };

    const messageType = tabMessageTypes[tabName] || 'greetings';
    updateClerkMessage(messageType);
}

// Cart Functions
function initializeCart() {
    
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

    const button = e.target;
    const productId = button.getAttribute('data-product-id');
    const stock = parseInt(button.getAttribute('data-stock'), 10);

    // Get the quantity input associated with this product
    const productCard = button.closest('.product-card');
    const quantityInput = productCard.querySelector('.quantity-input');
    const quantity = parseInt(quantityInput.value, 10);

    // Validate quantity
    if (isNaN(quantity) || quantity < 1) {
        alert('Please enter a valid quantity.');
        return;
    }

    // Check if the quantity exceeds the stock
    if (quantity > stock) {
        alert('Cannot add more items than are in stock.');
        return;
    }

    try {
        const response = await fetch('/querykicks/controllers/StoreController.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'addToCart',
                product_id: productId,
                quantity: quantity
            })
        });

        const data = await response.json();

        if (data.success) {
            // Update the cart display
            updateCartDisplay();

            // Update clerk message
            updateClerkMessage('addToCart');
        } else {
            alert(data.message || 'Failed to add item to cart');
            updateClerkMessage('error');
        }
    } catch (error) {
        alert('Error adding item to cart. Please try again.');
        updateClerkMessage('error');
    }
}


async function handleRemoveFromCart(e) {
    e.preventDefault();

    const cartItemId = e.target.getAttribute('data-item-id');

    if (!cartItemId) {
        alert('Invalid cart item.');
        return;
    }

    try {
        const response = await fetch('/querykicks/controllers/StoreController.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'removeFromCart',
                cart_item_id: cartItemId
            })
        });

        const data = await response.json();

        if (data.success) {
            // Update the cart display
            updateCartDisplay();

            // Update clerk message
            updateClerkMessage('removeFromCart');
        } else {
            alert(data.message || 'Failed to remove item from cart');
            updateClerkMessage('error');
        }
    } catch (error) {
        alert('Error removing item from cart. Please try again.');
        updateClerkMessage('error');
    }
}


function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
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
            // Update the quantity displayed
            const quantitySpan = e.target.closest('.quantity-controls').querySelector('.quantity');
            let currentQuantity = parseInt(quantitySpan.textContent);
            currentQuantity += change;
            quantitySpan.textContent = currentQuantity;

            // Update the subtotal for the item
            const priceElement = e.target.closest('.cart-item-details').querySelector('.price');
            const priceText = priceElement.textContent.replace('$', '').replace(/,/g, '');
            const price = parseFloat(priceText);
            const subtotalElement = e.target.closest('.cart-item-details').querySelector('.subtotal');
            const newSubtotal = (price * currentQuantity).toFixed(2);
            subtotalElement.textContent = `Subtotal: $${numberWithCommas(newSubtotal)}`;

            // Update the total in the order summary
            updateCartTotal();

            // Disable decrease button if quantity is 1
            const quantityControls = e.target.closest('.quantity-controls');
            const decreaseButton = quantityControls.querySelector('.quantity-btn.decrease');
            decreaseButton.disabled = currentQuantity <= 1;

            // Disable increase button if quantity reaches stock limit
            const stockLimit = parseInt(quantityControls.getAttribute('data-stock-limit'));
            const increaseButton = quantityControls.querySelector('.quantity-btn.increase');
            increaseButton.disabled = currentQuantity >= stockLimit;

        } else {
            alert(data.message || 'Failed to update quantity');
        }
    } catch (error) {
        alert('Error updating quantity. Please try again. ' + error.message);
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
            // Update the balance display
            updateBalance(data.newBalance);

            updateClerkMessage('checkout');
            location.reload(); // Refresh the page to show empty cart
        } else {
            alert(data.message || 'Checkout failed');
            updateClerkMessage('error');
        }
    } catch (error) {
        alert('Error during checkout. Please try again. ' + error.message);
        updateClerkMessage('error');
    }
}

// Money Modal Functions
function initializeMoneyModal() {

    // Get modal elements
    const addMoneyBtn = document.querySelector('.add-money-btn');
    const modal = document.getElementById('add-money-modal');
    const closeBtn = document.querySelector('.close-modal');
    const form = document.getElementById('add-money-form');

    if (addMoneyBtn) {
        addMoneyBtn.addEventListener('click', () => {
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

        if (data.success) {
            // Update the balance display immediately
            const balanceElement = document.querySelector('.balance');
            if (balanceElement) {
                balanceElement.textContent = `$${parseFloat(data.newBalance).toFixed(2)}`;
                balanceElement.setAttribute('data-balance', data.newBalance);
            }

            // Close the modal and reset form
            document.getElementById('add-money-modal').style.display = 'none';
            document.getElementById('add-money-form').reset();
            
            // Show success message
            alert('Money added successfully!');
            updateClerkMessage('addMoney');
        } else {
            alert(data.message || 'Failed to add money');
            updateClerkMessage('error');
        }
    } catch (error) {
        alert('Error adding money. Please try again. ' + error.message);
        updateClerkMessage('error');
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
            alert('Error logging out. Please try again. ' + error.message);
        }
    });
}

// Helper Functions
function updateBalance(newBalance) {
    const balanceElement = document.querySelector('.balance');
    if (balanceElement) {
        balanceElement.textContent = `$${parseFloat(newBalance).toFixed(2)}`;
        balanceElement.setAttribute('data-balance', newBalance);
    }
}

function updateCartDisplay() {
    const cartTab = document.getElementById('cart');
    if (cartTab) {
        // Fetch updated cart content
        fetch('/querykicks/controllers/StoreController.php?action=getCart')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    cartTab.innerHTML = data.html;

                    // Reinitialize event listeners for new cart content
                    initializeCart();
                } else {
                    alert(data.message || 'Failed to update cart display.');
                    updateClerkMessage('error');
                }
            })
            .catch(error => {
                alert('Sorry, we could not update your cart at this time. Please try again later.');
                updateClerkMessage('error');
                console.error('Error updating cart display:', error);
            });
    }
}



function updateCartTotal() {
    // Recalculate the total in the cart summary
    let total = 0;
    const cartItems = document.querySelectorAll('.cart-item');
    cartItems.forEach(item => {
        const quantity = parseInt(item.querySelector('.quantity').textContent);
        const priceText = item.querySelector('.price').textContent.replace('$', '').replace(/,/g, '');
        const price = parseFloat(priceText);
        total += quantity * price;
    });

    // Update the total in the receipt preview
    const totalElement = document.querySelector('.receipt-total span');
    if (totalElement) {
        totalElement.textContent = `$${numberWithCommas(total.toFixed(2))}`;
    }

    // Update the data-total attribute on the checkout button
    const checkoutBtn = document.querySelector('.checkout-btn');
    if (checkoutBtn) {
        checkoutBtn.setAttribute('data-total', total.toFixed(2));
    }
}

// FAQ functions
function initializeFAQ() {
    const faqItems = document.querySelectorAll('.faq-item');

    faqItems.forEach(item => {
        const question = item.querySelector('.faq-question');
        question.addEventListener('click', () => {
            item.classList.toggle('active');
            const toggleIcon = question.querySelector('.faq-toggle');
            toggleIcon.textContent = item.classList.contains('active') ? '-' : '+';

            // Update clerk message when a FAQ item is expanded
            if (item.classList.contains('active')) {
                updateClerkMessage('faq');
            }
        });
    });
}
