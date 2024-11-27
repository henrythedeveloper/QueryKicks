<div class="product-container">
    <h1>Available Shoes</h1>
    <div class="product-wrapper">
        <?php foreach ($products as $product): ?>
            <?php
            // Construct the correct image path
            $imagePath = '/querykicks/' . htmlspecialchars($product['image_url']);
            ?>
            <div class="product-card">
                <img src="<?= $imagePath ?>" alt="<?= htmlspecialchars($product['name']) ?>" loading="lazy">
                <div class="product-info">
                    <h3><?= htmlspecialchars($product['name']) ?></h3>
                    <p class="price">$<?= number_format($product['price'], 2) ?></p>
                    <p class="stock">In Stock: <?= htmlspecialchars($product['stock']) ?></p>
                    <?php if ($product['stock'] > 0): ?>
                        <input type="number" class="quantity-input" min="1" max="<?= htmlspecialchars($product['stock']) ?>" value="1">
                        <!-- Add to Cart Button with data attributes -->
                        <button class="add-to-cart-button"
                                data-product-id="<?= htmlspecialchars($product['id']) ?>"
                                data-price="<?= htmlspecialchars($product['price']) ?>"
                                data-stock="<?= htmlspecialchars($product['stock']) ?>">
                            Add to Cart
                        </button>
                    <?php else: ?>
                        <button class="out-of-stock-button" disabled>Out of Stock</button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
