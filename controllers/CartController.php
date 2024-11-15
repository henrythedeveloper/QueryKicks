<?php
require_once 'models/CartModel.php';

class CartController {

    public function addToCart() {
        header('Content-Type: application/json');
    
        try {
            if (!isset($_POST['product_id'])) {
                echo json_encode(['success' => false, 'error' => 'Product ID is missing.']);
                exit();
            }
    
            $productId = intval($_POST['product_id']);
            $userId = $_SESSION['user_id'] ?? null;
    
            if (!$userId) {
                echo json_encode(['success' => false, 'error' => 'User is not logged in.']);
                exit();
            }
    
            $db = Database::connect();
    
            // Check if the user's cart exists
            $stmt = $db->prepare('SELECT id FROM carts WHERE user_id = :user_id LIMIT 1');
            $stmt->execute([':user_id' => $userId]);
            $cart = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if (!$cart) {
                // Create a new cart for the user if it doesn't exist
                $stmt = $db->prepare('INSERT INTO carts (user_id) VALUES (:user_id)');
                $stmt->execute([':user_id' => $userId]);
                $cartId = $db->lastInsertId();
            } else {
                $cartId = $cart['id'];
            }
    
            // Add product to the cart_items table
            $stmt = $db->prepare('
                INSERT INTO cart_items (cart_id, product_id, quantity)
                VALUES (:cart_id, :product_id, 1)
                ON DUPLICATE KEY UPDATE quantity = quantity + 1
            ');
            $stmt->execute([
                ':cart_id' => $cartId,
                ':product_id' => $productId
            ]);
    
            echo json_encode(['success' => true, 'message' => 'Product added to cart.']);
        } catch (Exception $e) {
            error_log('Error in addToCart: ' . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Internal server error.']);
        }
        exit();
    }
    
    public function viewCart() {
        session_start();
    
        $userId = $_SESSION['user_id'] ?? null;
    
        if (!$userId) {
            echo "Error: User not logged in.";
            exit();
        }
    
        $db = Database::connect();
    
        try {
            // Fetch the user's cart ID
            $stmt = $db->prepare('SELECT id FROM carts WHERE user_id = :user_id LIMIT 1');
            $stmt->execute([':user_id' => $userId]);
            $cart = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if (!$cart) {
                // If no cart exists for the user
                $cartItems = [];
            } else {
                $cartId = $cart['id'];
    
                // Fetch cart items
                $stmt = $db->prepare('
                    SELECT 
                        ci.id AS cart_item_id, 
                        p.name, 
                        p.image_url, 
                        p.price, 
                        ci.quantity 
                    FROM cart_items ci
                    JOIN products p ON ci.product_id = p.id
                    WHERE ci.cart_id = :cart_id
                ');
                $stmt->execute([':cart_id' => $cartId]);
                $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
    
            // Pass cart items to the cart view
            require 'views/store/cart.php';
    
        } catch (Exception $e) {
            error_log('Error in viewCart: ' . $e->getMessage());
            echo "Error: Unable to fetch cart items.";
        }
    }
    
    

    public function removeFromCart() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart_item_id'])) {
            $cartItemId = intval($_POST['cart_item_id']);
    
            $cartModel = new CartModel();
            $success = $cartModel->removeItemFromCart($cartItemId);
    
            echo json_encode(['success' => $success]);
            exit();
        } else {
            echo json_encode(['success' => false]);
            exit();
        }
    }
    
}

