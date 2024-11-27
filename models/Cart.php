<?php
class Cart {
    private $db;
    private $table = 'cart_items';

    public function __construct($db) {
        $this->db = $db;
    }

    public function getCartItems($userId) {
        try {
            $query = "SELECT 
                        ci.id as cart_item_id, 
                        ci.cart_id,
                        ci.product_id,
                        ci.quantity,
                        p.name,
                        p.price,
                        p.image_url,
                        p.id as product_id,
                        p.stock
                    FROM cart_items ci 
                    JOIN carts c ON ci.cart_id = c.id 
                    JOIN products p ON ci.product_id = p.id 
                    WHERE c.user_id = ?";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([$userId]);
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $items;
        } catch (PDOException $e) {
            error_log('Error getting cart items: ' . $e->getMessage());
            return [];
        }
    }
    
    public function getCartItemByProductId($userId, $productId) {
        try {
            // Get the cart ID for the user
            $stmt = $this->db->prepare("SELECT id FROM carts WHERE user_id = ?");
            $stmt->execute([$userId]);
            $cartId = $stmt->fetchColumn();
    
            if (!$cartId) {
                // The user does not have a cart yet
                return null;
            }
    
            // Fetch the cart item
            $stmt = $this->db->prepare("SELECT * FROM cart_items WHERE cart_id = ? AND product_id = ?");
            $stmt->execute([$cartId, $productId]);
            $cartItem = $stmt->fetch(PDO::FETCH_ASSOC);
    
            return $cartItem ?: null;
        } catch (Exception $e) {
            error_log('Error fetching cart item: ' . $e->getMessage());
            return null;
        }
    }
    

    public function addItem($userId, $productId, $quantity) {
        try {
            // Get or create the user's cart
            $stmt = $this->db->prepare("SELECT id FROM carts WHERE user_id = ?");
            $stmt->execute([$userId]);
            $cartId = $stmt->fetchColumn();
    
            if (!$cartId) {
                // Create a new cart for the user
                $stmt = $this->db->prepare("INSERT INTO carts (user_id) VALUES (?)");
                $stmt->execute([$userId]);
                $cartId = $this->db->lastInsertId();
            }
    
            // Add the item to the cart
            $stmt = $this->db->prepare("INSERT INTO cart_items (cart_id, product_id, quantity) VALUES (?, ?, ?)");
            $stmt->execute([$cartId, $productId, $quantity]);
    
            return true;
        } catch (Exception $e) {
            error_log('Error adding item to cart: ' . $e->getMessage());
            return false;
        }
    }
    

    public function removeItem($userId, $cartItemId) {
        try {
            // Verify that the cart item belongs to the user's cart
            $stmt = $this->db->prepare("
                SELECT ci.id
                FROM cart_items ci
                JOIN carts c ON ci.cart_id = c.id
                WHERE ci.id = ? AND c.user_id = ?
            ");
            $stmt->execute([$cartItemId, $userId]);
            $itemExists = $stmt->fetchColumn();
    
            if (!$itemExists) {
                throw new Exception('Cart item not found.');
            }
    
            // Delete the cart item
            $stmt = $this->db->prepare("DELETE FROM cart_items WHERE id = ?");
            $stmt->execute([$cartItemId]);
    
            return true;
        } catch (Exception $e) {
            error_log('Error removing cart item: ' . $e->getMessage());
            return false;
        }
    }
    

    private function getOrCreateCart($userId) {
        try {
            // Check for existing cart
            $stmt = $this->db->prepare("SELECT id FROM carts WHERE user_id = ?");
            $stmt->execute([$userId]);
            $cart = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($cart) {
                return $cart['id'];
            }

            // Create new cart
            $stmt = $this->db->prepare("INSERT INTO carts (user_id) VALUES (?)");
            $stmt->execute([$userId]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log('Error with cart: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getCartTotal($userId) {
        try {
            $query = "SELECT SUM(ci.quantity * p.price) as total 
                    FROM cart_items ci 
                    JOIN carts c ON ci.cart_id = c.id 
                    JOIN products p ON ci.product_id = p.id 
                    WHERE c.user_id = ?";
        
            $stmt = $this->db->prepare($query);
            $stmt->execute([$userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log('Error calculating cart total: ' . $e->getMessage());
            return 0;
        }
    }

    public function updateQuantity($userId, $cartItemId, $newQuantity) {
        try {
            // Verify that the cart item belongs to the user's cart
            $stmt = $this->db->prepare("
                SELECT ci.id
                FROM cart_items ci
                JOIN carts c ON ci.cart_id = c.id
                WHERE ci.id = ? AND c.user_id = ?
            ");
            $stmt->execute([$cartItemId, $userId]);
            $itemExists = $stmt->fetchColumn();
    
            if (!$itemExists) {
                throw new Exception('Cart item not found.');
            }
    
            // Update the quantity
            $stmt = $this->db->prepare("UPDATE cart_items SET quantity = ? WHERE id = ?");
            $stmt->execute([$newQuantity, $cartItemId]);
    
            return true;
        } catch (Exception $e) {
            error_log('Error updating cart item quantity: ' . $e->getMessage());
            return false;
        }
    }
    
    
    
    
}