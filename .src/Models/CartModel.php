```php
/**
 * Cart model handling shopping cart operations
 */
class CartModel extends BaseModel {
    protected string $table = 'carts';

    /**
     * Add product to cart
     */
    public function addItem(int $userId, int $productId, int $quantity = 1): int {
        // First check if item already exists in cart
        $existingItem = Database::query(
            "SELECT * FROM {$this->table} 
            WHERE user_id = :user_id AND product_id = :product_id",
            ['user_id' => $userId, 'product_id' => $productId]
        )->fetch();

        if ($existingItem) {
            // Update quantity of existing item
            return Database::query(
                "UPDATE {$this->table} 
                SET quantity = quantity + :quantity 
                WHERE user_id = :user_id AND product_id = :product_id",
                [
                    'quantity' => $quantity,
                    'user_id' => $userId,
                    'product_id' => $productId
                ]
            )->rowCount();
        }

        // Add new item
        return $this->create([
            'user_id' => $userId,
            'product_id' => $productId,
            'quantity' => $quantity
        ]);
    }

    /**
     * Get user's cart items with product details
     */
    public function getUserCart(int $userId): array {
        return Database::query(
            "SELECT c.*, p.name, p.price, (p.price * c.quantity) as subtotal 
            FROM {$this->table} c 
            JOIN products p ON c.product_id = p.id 
            WHERE c.user_id = :user_id",
            ['user_id' => $userId]
        )->fetchAll();
    }

    /**
     * Update item quantity
     */
    public function updateQuantity(int $userId, int $productId, int $quantity): bool {
        return (bool)Database::query(
            "UPDATE {$this->table} 
            SET quantity = :quantity 
            WHERE user_id = :user_id AND product_id = :product_id",
            [
                'quantity' => $quantity,
                'user_id' => $userId,
                'product_id' => $productId
            ]
        )->rowCount();
    }

    /**
     * Remove item from cart
     */
    public function removeItem(int $userId, int $productId): bool {
        return (bool)Database::query(
            "DELETE FROM {$this->table} 
            WHERE user_id = :user_id AND product_id = :product_id",
            ['user_id' => $userId, 'product_id' => $productId]
        )->rowCount();
    }

    /**
     * Clear user's cart
     */
    public function clearCart(int $userId): bool {
        return (bool)Database::query(
            "DELETE FROM {$this->table} WHERE user_id = :user_id",
            ['user_id' => $userId]
        )->rowCount();
    }

    /**
     * Get cart total
     */
    public function getCartTotal(int $userId): float {
        $result = Database::query(
            "SELECT SUM(p.price * c.quantity) as total 
            FROM {$this->table} c 
            JOIN products p ON c.product_id = p.id 
            WHERE c.user_id = :user_id",
            ['user_id' => $userId]
        )->fetch();

        return (float)($result['total'] ?? 0);
    }
}
```

Next layer?