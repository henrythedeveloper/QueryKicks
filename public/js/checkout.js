function processCheckout() {
    if (!confirm('Are you sure you want to proceed with checkout?')) {
        return;
    }

    // Show loading state
    const checkoutBtn = document.querySelector('.checkout-btn');
    const originalText = checkoutBtn.textContent;
    checkoutBtn.textContent = 'Processing...';
    checkoutBtn.disabled = true;

    fetch('index.php?controller=CartController&action=processCheckout', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update user's balance display
            const balanceDisplay = document.querySelector('.user-money span');
            if (balanceDisplay) {
                balanceDisplay.textContent = `Balance: $${parseFloat(data.newBalance).toFixed(2)}`;
            }

            // Download receipt if provided
            if (data.receiptUrl) {
                window.location.href = data.receiptUrl;
            }

            // Clear cart display and show empty message
            const cartContent = document.querySelector('.cart-items');
            if (cartContent) {
                cartContent.innerHTML = '<p>Your cart is empty!</p>';
            }

            // Hide checkout button
            checkoutBtn.style.display = 'none';

            alert('Purchase successful! Your receipt will download automatically.');
        } else {
            alert(data.message || 'Error processing checkout');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error processing checkout. Please try again.');
    })
    .finally(() => {
        // Reset button state
        checkoutBtn.textContent = originalText;
        checkoutBtn.disabled = false;
    });
}