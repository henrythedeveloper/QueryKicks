<div class="cart-wrapper">
    <div class="cart-header">
        <h2>Your Cart</h2>
        <?php if (!empty($cartItems)): ?>
            <button class="checkout-btn" onclick="processCheckout()">Checkout</button>
        <?php endif; ?>
    </div>

    <?php if (!empty($cartItems)): ?>
        <div class="cart-items">
            <?php 
            $total = 0;
            foreach ($cartItems as $item): 
                $itemTotal = $item['price'] * $item['quantity'];
                $total += $itemTotal;
            ?>
                <div class="cart-item">
                    <img src="<?= htmlspecialchars($item['image_url']) ?>" 
                         alt="<?= htmlspecialchars($item['name']) ?>">
                    <div class="cart-item-details">
                        <h3><?= htmlspecialchars($item['name']) ?></h3>
                        <p>Price: $<?= htmlspecialchars($item['price']) ?></p>
                        <p>Quantity: <?= htmlspecialchars($item['quantity']) ?></p>
                        <button class="remove-from-cart" 
                                data-id="<?= htmlspecialchars($item['cart_item_id']) ?>">
                            Remove
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
            <div class="cart-total">
                <h3>Total: $<?= number_format($total, 2) ?></h3>
            </div>
        </div>
    <?php else: ?>
        <p>Your cart is empty!</p>
    <?php endif; ?>
</div>

<script>
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
            const cartWrapper = document.querySelector('.cart-wrapper');
            if (cartWrapper) {
                cartWrapper.innerHTML = '<div class="cart-header"><h2>Your Cart</h2></div><p>Your cart is empty!</p>';
            }

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
        if (checkoutBtn) {
            checkoutBtn.textContent = originalText;
            checkoutBtn.disabled = false;
        }
    });
}
</script>