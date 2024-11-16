<div class="cart-wrapper">
    <h2>Your Cart</h2>
    <?php if (!empty($cartItems)): ?>
        <div class="cart-items">
            <?php foreach ($cartItems as $item): ?>
                <div class="cart-item">
                    <div class="item-top-section">
                        <img src="<?= htmlspecialchars($item['image_url']) ?>" 
                             alt="<?= htmlspecialchars($item['name']) ?>">
                        <div class="cart-item-details">
                            <h3><?= htmlspecialchars($item['name']) ?></h3>
                            <p>Price: $<?= htmlspecialchars($item['price']) ?></p>
                            <p>Quantity: <?= htmlspecialchars($item['quantity']) ?></p>
                        </div>
                    </div>
                    <button class="remove-from-cart" 
                            data-id="<?= htmlspecialchars($item['cart_item_id']) ?>">
                        Remove
                    </button>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>Your cart is empty!</p>
    <?php endif; ?>
</div>