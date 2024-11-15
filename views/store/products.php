<div class="product-wrapper">
    <?php foreach ($products as $product): ?>
        <div class="product">
            <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
            <h3><?= htmlspecialchars($product['name']) ?></h3>
            <p>$<?= htmlspecialchars($product['price']) ?></p>
            <button>Add to Cart</button>
        </div>
    <?php endforeach; ?>
</div>
