<?php
require_once 'models/CartModel.php';

class CartController {
    private $cartModel;

    public function __construct() {
        $this->cartModel = new CartModel();
    }

    public function addToCart() {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            echo json_encode(['success' => false, 'message' => 'User not logged in.']);
            return;
        }
    
        $productId = $_POST['product_id'] ?? null;
        if (!$productId) {
            echo json_encode(['success' => false, 'message' => 'Product ID missing.']);
            return;
        }
    
        try {
            $this->cartModel->addItemToCart($userId, $productId);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            error_log('Error in addToCart: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error adding product to cart.']);
        }
    }
    
    public function viewCart() {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            echo json_encode(['success' => false, 'error' => 'User not logged in']);
            return;
        }
    
        $cartItems = $this->cartModel->getCartItems($userId);
    
        // Render the cart items as HTML
        ob_start();
        require 'views/store/cart.php';
        $cartHtml = ob_get_clean();
    
        echo json_encode(['success' => true, 'cartHtml' => $cartHtml]);
    }

    public function removeFromCart() {
        if (!isset($_POST['cart_item_id'])) {
            echo json_encode(['success' => false, 'message' => 'Cart item ID missing']);
            return;
        }

        $cartItemId = intval($_POST['cart_item_id']);
        
        try {
            $success = $this->cartModel->removeItemFromCart($cartItemId);
            echo json_encode(['success' => $success]);
        } catch (Exception $e) {
            error_log('Error removing item from cart: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error removing item from cart']);
        }
    }
}