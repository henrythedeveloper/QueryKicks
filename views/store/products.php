<div class="product-wrapper">
    <h2>Shoes</h2>
    <?php foreach ($products as $product): ?>
        <div class="product">
            <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
            <h3><?= htmlspecialchars($product['name']) ?></h3>
            <p>$<?= htmlspecialchars($product['price']) ?></p>
            <button class="add-to-cart-button" data-product-id="<?= htmlspecialchars($product['id']) ?>">Add to Cart</button>
        </div>
    <?php endforeach; ?>
</div>
