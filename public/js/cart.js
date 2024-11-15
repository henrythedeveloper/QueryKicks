document.addEventListener('DOMContentLoaded', () => {
    const addButtons = document.querySelectorAll('.add-to-cart-button');

    addButtons.forEach(button => {
        button.addEventListener('click', function () {
            const productId = this.dataset.productId;

            // Send the request via Fetch API
            fetch('/querykicks/index.php?controller=CartController&action=addToCart', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `product_id=${productId}`
            })            
                .then(response => {
                    // Check if the response is OK and JSON
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.text(); // Use text to avoid parsing errors
                })
                .then(text => {
                    let data;
                    try {
                        data = JSON.parse(text);
                    } catch (error) {
                        console.error('Invalid JSON:', text);
                        alert('An unexpected error occurred. Please try again.');
                        return;
                    }

                    // Handle response data
                    if (data.success) {
                        alert('Product added to cart!');
                        updateCartTab(data.cartHtml); // Update the cart tab content
                    } else if (data.error) {
                        alert(`Error: ${data.error}`);
                    } else {
                        alert('Failed to add product to cart.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An unexpected error occurred. Please try again.');
                });
        });
    });

    const removeButtons = document.querySelectorAll('.remove-from-cart');

    removeButtons.forEach(button => {
        button.addEventListener('click', function () {
            const cartItemId = this.dataset.id;

            fetch('/querykicks/index.php?controller=CartController&action=removeFromCart', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `cart_item_id=${cartItemId}`
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.text(); // Use text to avoid parsing errors
                })
                .then(text => {
                    let data;
                    try {
                        data = JSON.parse(text);
                    } catch (error) {
                        console.error('Invalid JSON:', text);
                        alert('An unexpected error occurred. Please try again.');
                        return;
                    }

                    if (data.success) {
                        alert('Item removed from cart!');
                        updateCartTab(data.cartHtml); // Dynamically update cart content
                    } else if (data.error) {
                        alert(`Error: ${data.error}`);
                    } else {
                        alert('Failed to remove item from cart.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An unexpected error occurred. Please try again.');
                });
        });
    });

    function updateCartTab(cartHtml) {
        const cartTab = document.getElementById('cart');
        if (cartTab) {
            cartTab.innerHTML = cartHtml; // Replace cart tab content with the updated cart
        }
    }
});
