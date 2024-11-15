<?php
class CartModel {
    private $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    public function addItemToCart($userId, $productId) {
        // Check if a cart exists for the user
        $stmt = $this->db->prepare("SELECT id FROM carts WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $userId]);
        $cart = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$cart) {
            // Create a new cart for the user
            $stmt = $this->db->prepare("INSERT INTO carts (user_id) VALUES (:user_id)");
            $stmt->execute(['user_id' => $userId]);
            $cartId = $this->db->lastInsertId();
        } else {
            $cartId = $cart['id'];
        }

        // Check if the product is already in the cart
        $stmt = $this->db->prepare("SELECT id FROM cart_items WHERE cart_id = :cart_id AND product_id = :product_id");
        $stmt->execute(['cart_id' => $cartId, 'product_id' => $productId]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($item) {
            // Update the quantity if the product already exists
            $stmt = $this->db->prepare("UPDATE cart_items SET quantity = quantity + 1 WHERE id = :id");
            $stmt->execute(['id' => $item['id']]);
        } else {
            // Add the product to the cart
            $stmt = $this->db->prepare("INSERT INTO cart_items (cart_id, product_id, quantity) VALUES (:cart_id, :product_id, 1)");
            $stmt->execute(['cart_id' => $cartId, 'product_id' => $productId]);
        }
    }
    
    public function getCartItems($userId) {
        $stmt = $this->db->prepare("
            SELECT 
                ci.id AS cart_item_id, 
                p.image_url, 
                p.name, 
                p.price, 
                ci.quantity 
            FROM 
                cart_items ci
            INNER JOIN 
                carts c ON ci.cart_id = c.id
            INNER JOIN 
                products p ON ci.product_id = p.id
            WHERE 
                c.user_id = :user_id
        ");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    

    public function removeItemFromCart($cartItemId) {
        $stmt = $this->db->prepare("DELETE FROM cart_items WHERE id = :cart_item_id");
        $stmt->bindParam(':cart_item_id', $cartItemId, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
}
