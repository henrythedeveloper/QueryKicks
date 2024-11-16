document.addEventListener('DOMContentLoaded', () => {
    // Add to Cart functionality
    const addButtons = document.querySelectorAll('.add-to-cart-button');

    addButtons.forEach(button => {
        button.addEventListener('click', () => {
            const productId = button.dataset.productId;

            sendRequest('index.php?controller=CartController&action=addToCart', { product_id: productId })
                .then(data => {
                    if (data.success) {
                        alert('Product added to cart!');
                        refreshCart();
                    } else {
                        alert(`Failed to add product: ${data.message || 'Unknown error'}`);
                    }
                })
                .catch(err => {
                    console.error('Add to cart error:', err);
                    alert('Error adding product to cart');
                });
        });
    });

    function refreshCart() {
        sendRequest('index.php?controller=CartController&action=viewCart', {})
            .then(data => {
                if (data.success) {
                    const cartTab = document.getElementById('cart');
                    if (cartTab) {
                        cartTab.innerHTML = data.cartHtml;
                        attachRemoveButtonListeners();
                    }
                } else {
                    console.error('Failed to refresh cart:', data.message);
                }
            })
            .catch(err => {
                console.error('Refresh cart error:', err);
            });
    }

    function attachRemoveButtonListeners() {
        const removeButtons = document.querySelectorAll('.remove-from-cart');
        removeButtons.forEach(button => {
            button.addEventListener('click', () => {
                const cartItemId = button.dataset.id;
                
                sendRequest('index.php?controller=CartController&action=removeFromCart', { cart_item_id: cartItemId })
                    .then(data => {
                        if (data.success) {
                            refreshCart();
                        } else {
                            alert(data.message || 'Failed to remove item from cart');
                        }
                    })
                    .catch(err => {
                        console.error('Remove from cart error:', err);
                        alert('Error removing item from cart');
                    });
            });
        });
    }

    // Attach remove button listeners on page load
    attachRemoveButtonListeners();

    async function sendRequest(url, data) {
        try {
            const params = new URLSearchParams(data).toString();
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: params,
            });

            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }

            const text = await response.text();
            try {
                return JSON.parse(text);
            } catch (error) {
                console.error('Invalid JSON response:', text);
                throw new Error('Invalid JSON response');
            }
        } catch (error) {
            console.error('Request error:', error);
            throw error;
        }
    }
});