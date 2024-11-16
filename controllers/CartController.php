<?php
require_once 'models/CartModel.php';
require_once 'models/UserModel.php';

class CartController {
    private $cartModel;
    private $userModel;

    public function __construct() {
        $this->cartModel = new CartModel();
        $this->userModel = new UserModel();
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

    public function processCheckout() {
        // Ensure clean output
        ob_clean();
        header('Content-Type: application/json');

        try {
            // Check user login
            if (!isset($_SESSION['user_id'])) {
                echo json_encode(['success' => false, 'message' => 'User not logged in']);
                exit;
            }

            $userId = $_SESSION['user_id'];
            
            // Get cart items
            $cartItems = $this->cartModel->getCartItems($userId);
            if (empty($cartItems)) {
                echo json_encode(['success' => false, 'message' => 'Cart is empty']);
                exit;
            }

            // Calculate total
            $total = 0;
            foreach ($cartItems as $item) {
                $total += $item['price'] * $item['quantity'];
            }

            // Check if user can afford purchase
            if (!$this->userModel->canAffordPurchase($userId, $total)) {
                echo json_encode(['success' => false, 'message' => 'Insufficient funds']);
                exit;
            }

            // Process the purchase
            if (!$this->userModel->processPurchase($userId, $total)) {
                echo json_encode(['success' => false, 'message' => 'Failed to process payment']);
                exit;
            }

            // Generate receipt
            $receiptContent = $this->generateReceipt($cartItems, $total);
            $receiptFileName = 'receipt_' . time() . '.txt';
            $receiptPath = 'receipts/' . $receiptFileName;

            // Ensure receipts directory exists
            if (!is_dir('receipts')) {
                mkdir('receipts', 0777, true);
            }

            // Save receipt
            if (file_put_contents($receiptPath, $receiptContent)) {
                // Clear the cart
                $this->cartModel->clearCart($userId);
                
                // Get new balance
                $newBalance = $this->userModel->getUserMoney($userId);

                echo json_encode([
                    'success' => true,
                    'message' => 'Purchase successful',
                    'receiptUrl' => $receiptPath,
                    'newBalance' => $newBalance
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to generate receipt']);
            }

        } catch (Exception $e) {
            error_log('Checkout error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Server error occurred']);
        }
        exit;
    }

    private function generateReceipt($cartItems, $total) {
        $receipt = "=== QueryKicks Purchase Receipt ===\n\n";
        $receipt .= "Date: " . date('Y-m-d H:i:s') . "\n";
        $receipt .= "Transaction ID: " . uniqid() . "\n\n";
        $receipt .= "Items:\n";
        $receipt .= str_repeat("-", 40) . "\n";

        foreach ($cartItems as $item) {
            $itemTotal = $item['price'] * $item['quantity'];
            $receipt .= sprintf(
                "%-20s x%d $%8.2f\n",
                substr($item['name'], 0, 20),
                $item['quantity'],
                $itemTotal
            );
        }

        $receipt .= str_repeat("-", 40) . "\n";
        $receipt .= sprintf("Total: $%8.2f\n", $total);
        
        return $receipt;
    }
}
