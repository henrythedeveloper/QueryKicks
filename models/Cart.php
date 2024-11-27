<?php
class Cart {
    private $db;
    private $table = 'cart_items';

    public function __construct($db) {
        $this->db = $db;
    }

    public function getCartItems($userId) {
        try {
            $query = "SELECT ci.*, p.name, p.price, p.image_url 
                    FROM cart_items ci 
                    JOIN carts c ON ci.cart_id = c.id 
                    JOIN products p ON ci.product_id = p.id 
                    WHERE c.user_id = ?";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error getting cart items: ' . $e->getMessage());
            return [];
        }
    }

    public function addItem($userId, $productId, $quantity = 1) {
        try {
            error_log("Adding item to cart - User ID: $userId, Product ID: $productId, Quantity: $quantity");
            
            $this->db->beginTransaction();
            
            // Get or create cart
            $cartId = $this->getOrCreateCart($userId);
            error_log("Cart ID: $cartId");
    
            // Check if item exists in cart
            $stmt = $this->db->prepare(
                "SELECT id, quantity FROM cart_items 
                WHERE cart_id = ? AND product_id = ?"
            );
            $stmt->execute([$cartId, $productId]);
            $existingItem = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($existingItem) {
                error_log("Updating existing item quantity");
                // Update quantity
                $stmt = $this->db->prepare(
                    "UPDATE cart_items 
                    SET quantity = quantity + ? 
                    WHERE id = ?"
                );
                $stmt->execute([$quantity, $existingItem['id']]);
            } else {
                error_log("Adding new item to cart");
                // Add new item
                $stmt = $this->db->prepare(
                    "INSERT INTO cart_items (cart_id, product_id, quantity) 
                    VALUES (?, ?, ?)"
                );
                $stmt->execute([$cartId, $productId, $quantity]);
            }
    
            $this->db->commit();
            error_log("Successfully added/updated cart item");
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log('Error adding item to cart: ' . $e->getMessage());
            return false;
        }
    }

    public function removeItem($userId, $itemId) {
        try {
            $stmt = $this->db->prepare(
                "DELETE ci FROM cart_items ci 
                JOIN carts c ON ci.cart_id = c.id 
                WHERE c.user_id = ? AND ci.id = ?"
            );
            return $stmt->execute([$userId, $itemId]);
        } catch (PDOException $e) {
            error_log('Error removing item from cart: ' . $e->getMessage());
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

    public function updateQuantity($userId, $itemId, $change) {
        try {
            $this->db->beginTransaction();
            
            // Verify the item belongs to the user's cart
            $stmt = $this->db->prepare("
                SELECT ci.id, ci.quantity 
                FROM cart_items ci
                JOIN carts c ON ci.cart_id = c.id
                WHERE c.user_id = ? AND ci.id = ?
            ");
            $stmt->execute([$userId, $itemId]);
            $item = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$item) {
                throw new Exception('Cart item not found');
            }
            
            $newQuantity = $item['quantity'] + $change;
            
            // Ensure quantity doesn't go below 1
            if ($newQuantity < 1) {
                throw new Exception('Quantity cannot be less than 1');
            }
            
            // Update quantity
            $stmt = $this->db->prepare("
                UPDATE cart_items 
                SET quantity = ? 
                WHERE id = ?
            ");
            $stmt->execute([$newQuantity, $itemId]);
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log('Error updating quantity: ' . $e->getMessage());
            return false;
        }
    }
}