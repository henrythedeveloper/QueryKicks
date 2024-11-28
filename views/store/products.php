<?php
/**
 * products.php: This file serves as the product listing view for the Query Kicks application. 
 * It dynamically displays all available products in the store, including their details and 
 * options for adding items to the cart.
 *
 * Features:
 *  - **Product Display**: Displays product name, price, stock status, and image.
 *  - **Lazy Loading Images**: Uses `loading="lazy"` for product images to improve performance.
 *  - **Stock Status**: Dynamically adjusts the "Add to Cart" button based on product availability.
 *  - **Add to Cart Integration**: Provides buttons with data attributes for seamless AJAX cart updates.
 *
 * Data Dependencies:
 *  - `$products`: An array of products provided by the controller, each containing:
 *      - `id`: Unique identifier for the product.
 *      - `name`: Product name.
 *      - `price`: Product price.
 *      - `stock`: Available stock quantity.
 *      - `image_url`: Path to the product image.
 *
 * Authors: Henry Le and Brody Sprouse
 * Version: 20241203
 */
?>
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
                    <p class="price">
                    <span class="sparkle-wrapper">
                        <span class="sparkle"></span>
                        <span class="sparkle"></span>
                        <span class="sparkle"></span>
                        <span class="sparkle"></span>
                        <i class="currency-icon-small"></i>
                    </span>
                    <?= number_format($product['price'], 2) ?></p>
                    <p class="stock">In Stock: <?= htmlspecialchars($product['stock']) ?></p>
                    <?php if ($product['stock'] > 0): ?>
                        <input type="hidden" class="quantity-input" min="1" max="<?= htmlspecialchars($product['stock']) ?>" value="1">
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
