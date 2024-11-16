<?php
class CartModel {
    private $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    public function addItemToCart($userId, $productId) {
        try {
            $this->db->beginTransaction();
            
            // First, get or create cart
            $stmt = $this->db->prepare("SELECT id FROM carts WHERE user_id = :user_id LIMIT 1");
            $stmt->execute(['user_id' => $userId]);
            $cart = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$cart) {
                // Create a new cart
                $stmt = $this->db->prepare("INSERT INTO carts (user_id) VALUES (:user_id)");
                $stmt->execute(['user_id' => $userId]);
                $cartId = $this->db->lastInsertId();
            } else {
                $cartId = $cart['id'];
            }

            // Check if product already exists in cart
            $stmt = $this->db->prepare("
                SELECT id, quantity 
                FROM cart_items 
                WHERE cart_id = :cart_id 
                AND product_id = :product_id 
                LIMIT 1
            ");
            $stmt->execute([
                'cart_id' => $cartId,
                'product_id' => $productId
            ]);
            $existingItem = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existingItem) {
                // Update existing item quantity
                $stmt = $this->db->prepare("
                    UPDATE cart_items 
                    SET quantity = quantity + 1 
                    WHERE id = :id
                ");
                $stmt->execute(['id' => $existingItem['id']]);
            } else {
                // Add new item
                $stmt = $this->db->prepare("
                    INSERT INTO cart_items (cart_id, product_id, quantity) 
                    VALUES (:cart_id, :product_id, 1)
                ");
                $stmt->execute([
                    'cart_id' => $cartId,
                    'product_id' => $productId
                ]);
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error in addItemToCart: " . $e->getMessage());
            throw $e;
        }
    }

    public function getCartItems($userId) {
        $stmt = $this->db->prepare("
            SELECT 
                ci.id AS cart_item_id,
                p.image_url,
                p.name,
                p.price,
                ci.quantity,
                p.id AS product_id
            FROM cart_items ci
            INNER JOIN carts c ON ci.cart_id = c.id
            INNER JOIN products p ON ci.product_id = p.id
            WHERE c.user_id = :user_id
        ");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function removeItemFromCart($cartItemId) {
        try {
            $stmt = $this->db->prepare("DELETE FROM cart_items WHERE id = :cart_item_id");
            $stmt->execute(['cart_item_id' => $cartItemId]);
            return true;
        } catch (Exception $e) {
            error_log("Error in removeItemFromCart: " . $e->getMessage());
            return false;
        }
    }
}