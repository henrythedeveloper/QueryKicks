<div class="product-container">
    <h1>Available Shoes</h1>
    <div class="product-wrapper">
        <?php foreach ($products as $product): ?>
            <div class="product-card">
                <img src="<?= htmlspecialchars($product['image_url']) ?>" 
                     alt="<?= htmlspecialchars($product['name']) ?>"
                     loading="lazy">
                <div class="product-info">
                    <h3><?= htmlspecialchars($product['name']) ?></h3>
                    <p class="price">$<?= number_format($product['price'], 2) ?></p>
                    <p class="stock">In Stock: <?= htmlspecialchars($product['stock']) ?></p>
                    <?php if ($product['stock'] > 0): ?>
                        <button class="add-to-cart-button" 
                                data-product-id="<?= htmlspecialchars($product['id']) ?>"
                                data-price="<?= htmlspecialchars($product['price']) ?>">
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