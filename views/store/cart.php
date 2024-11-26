<div class="cart-wrapper" id="cart-container">
    <?php if (!empty($cartItems)): ?>
        <!-- Cart Items Section -->
        <div class="cart-content">
            <h2>Shopping Cart</h2>
            <div class="cart-items">
                <?php 
                $total = 0;
                foreach ($cartItems as $item):
                    $itemTotal = $item['price'] * $item['quantity'];
                    $total += $itemTotal;
                ?>
                    <div class="cart-item" data-item-id="<?= htmlspecialchars($item['cart_item_id']) ?>">
                        <div class="cart-item-image">
                            <img src="<?= htmlspecialchars($item['image_url']) ?>" 
                                 alt="<?= htmlspecialchars($item['name']) ?>"
                                 loading="lazy">
                        </div>
                        
                        <div class="cart-item-details">
                            <h3><?= htmlspecialchars($item['name']) ?></h3>
                            <div class="price-quantity">
                                <p class="price">$<?= number_format($item['price'], 2) ?></p>
                                <div class="quantity-controls">
                                    <button type="button" class="quantity-btn decrease" 
                                            data-item-id="<?= htmlspecialchars($item['cart_item_id']) ?>"
                                            <?= $item['quantity'] <= 1 ? 'disabled' : '' ?>>-</button>
                                    <span class="quantity"><?= htmlspecialchars($item['quantity']) ?></span>
                                    <button type="button" class="quantity-btn increase"
                                            data-item-id="<?= htmlspecialchars($item['cart_item_id']) ?>">+</button>
                                </div>
                            </div>
                            <p class="subtotal">Subtotal: $<?= number_format($itemTotal, 2) ?></p>
                            <button type="button" class="remove-from-cart"
                                    data-item-id="<?= htmlspecialchars($item['cart_item_id']) ?>">Remove</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Order Summary Section -->
        <div class="cart-summary">
            <h3>Order Summary</h3>
            <div class="receipt-preview">
                <p class="receipt-date">Date: <?= date('Y-m-d H:i:s') ?></p>
                <div class="receipt-items">
                    <?php foreach ($cartItems as $item): ?>
                        <div class="receipt-item">
                            <span><?= htmlspecialchars($item['name']) ?></span>
                            <span><?= $item['quantity'] ?> × $<?= number_format($item['price'], 2) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="receipt-total">
                    <strong>Total:</strong>
                    <span>$<?= number_format($total, 2) ?></span>
                </div>
            </div>
            <button type="button" class="checkout-btn" data-total="<?= htmlspecialchars($total) ?>">
                Checkout
            </button>
        </div>
    <?php else: ?>
        <div class="empty-cart">
            <h2>Your cart is empty</h2>
            <p>Looks like you haven't added any items yet!</p>
            <button class="continue-shopping" onclick="openTab(event, 'shoes')">Continue Shopping</button>
        </div>
    <?php endif; ?>
</div>