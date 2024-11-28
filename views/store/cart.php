<?php
/**
 * cart.php: This file serves as the shopping cart view for the Query Kicks application. 
 * It dynamically displays the items in the user's cart, including their details, quantity controls, 
 * and order summary. It also handles empty cart states.
 *
 * Features:
 *  - **Cart Item Display**: Shows product name, price, quantity, subtotal, and stock availability.
 *  - **Quantity Controls**: Allows users to increase or decrease item quantities dynamically.
 *  - **Order Summary**: Displays a receipt-like preview of items, quantities, and the total cost.
 *  - **Checkout Integration**: Includes a "Checkout" button with the total price for processing orders.
 *  - **Empty Cart State**: Displays a friendly message and "Continue Shopping" button when the cart is empty.
 *
 * Data Dependencies:
 *  - `$cartItems`: An array of cart item data fetched from the controller, with each item containing:
 *      - `id` or `cart_item_id`: Unique identifier for the cart item.
 *      - `name`: Product name.
 *      - `price`: Product price per unit.
 *      - `quantity`: Number of units of the product in the cart.
 *      - `stock`: Available stock for the product.
 *      - `image_url`: Path to the product image.
 *
 * Linked Assets:
 *  - Expected to use CSS for styling cart items and the receipt preview.
 *  - JavaScript should handle quantity adjustments, item removal, and checkout actions.
 *
 * Authors: Henry Le and Brody Sprouse
 * Version: 20241203
 */

?>
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
                    $imagePath = '/querykicks/' . ltrim($item['image_url'], '/');
                ?>
                    <div class="cart-item">
                        <div class="cart-item-image">
                            <img src="<?= htmlspecialchars($imagePath) ?>"
                                alt="<?= htmlspecialchars($item['name']) ?>"
                                loading="lazy">
                        </div>
                        <div class="cart-item-details">
                            <h3><?= htmlspecialchars($item['name']) ?></h3>
                            <div class="price-quantity"
                                data-stock-limit="<?= htmlspecialchars($item['stock']) ?>">
                                <p class="price"><i class="currency-icon-modal"></i><?= number_format($item['price'], 2) ?></p>
                                <div class="quantity-controls">
                                    <button type="button" class="quantity-btn decrease"
                                            data-item-id="<?= htmlspecialchars($item['cart_item_id'] ?? $item['id']) ?>"
                                            <?= $item['quantity'] <= 1 ? 'disabled' : '' ?>>-</button>
                                    <span class="quantity"><?= htmlspecialchars($item['quantity']) ?></span>
                                    <button type="button" class="quantity-btn increase"
                                            data-item-id="<?= htmlspecialchars($item['cart_item_id'] ?? $item['id']) ?>">+</button>
                                </div>
                            </div>
                            <p class="stock-info">Available stock: <?= htmlspecialchars($item['stock']) ?></p>
                            <p class="subtotal">Subtotal: <i class="currency-icon-modal"></i><?= number_format($itemTotal, 2) ?></p>
                            <button type="button" class="remove-from-cart"
                                    data-item-id="<?= htmlspecialchars($item['cart_item_id'] ?? $item['id']) ?>">Remove</button>
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
                            <span><?= $item['quantity'] ?> × <i class="currency-icon-modal"></i><?= number_format($item['price'], 2) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="receipt-total">
                    <strong>Total:</strong>
                    <span><i class="currency-icon-modal"></i><?= number_format($total, 2) ?></span>
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
            <button class="continue-shopping" 
                    onclick="document.querySelector('[data-view=\'shoes\']').click()">Continue Shopping</button>
        </div>
    <?php endif; ?>
</div>