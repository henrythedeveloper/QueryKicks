<?php
// Debug file paths
$productPath = __DIR__ . '/../models/Product.php';
$dbPath = __DIR__ . '/../config/database.php';
$cart = __DIR__ . '/../models/Cart.php';

error_log("Product.php path: " . $productPath);
error_log("Database.php path: " . $dbPath);

require_once $dbPath;
require_once $productPath;
require_once $cart;
class StoreController {
    private $db;
    private $product;
    private $cart;
    private $clerkResponses;

    public function __construct() {
        $this->db = (new Database())->getConnection();
        $this->product = new Product($this->db);
        $this->cart = new Cart($this->db);
        $this->loadClerkResponses();
    }

    private function loadClerkResponses() {
        $jsonPath = __DIR__ . '/../data/clerk_responses.json';
        if (file_exists($jsonPath)) {
            $json = file_get_contents($jsonPath);
            $this->clerkResponses = json_decode($json, true) ?? [];
        } else {
            $this->clerkResponses = [];
        }
    }

    public function handleRequest() {
        error_log("StoreController::handleRequest called."); // Log entry into the method

        // Check authentication
        if (!isset($_SESSION['user_id'])) {
            error_log("User not authenticated, redirecting to auth."); // Log authentication failure
            header('Location: /querykicks/views/auth.php');
            exit();
        }

        // Handle AJAX requests
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            error_log('Received POST data: ' . print_r($input, true)); // Debug log
            $action = $_POST['action'] ?? $input['action'] ?? null;

            if ($action) {
                $this->handleAction($action);
                return;
            }
        }

        // Default: render main store view
        $this->renderStore();
    }

    private function handleAction($action) {
        error_log('Handling action: ' . $action);
        switch($action) {
            case 'loadProducts':
                $this->getProducts();
                break;
            case 'addToCart':
                $this->addToCart();
                break;
            case 'removeFromCart':
                $this->removeFromCart();
                break;
            case 'updateQuantity':  
                $this->updateQuantity();
                break;
            case 'checkout':
                $this->processCheckout();
                break;
            case 'getClerkMessage':
                $this->getClerkMessage();
                break;
            case 'addMoney':
                $this->addMoney();
                break;
            default:
                $this->sendResponse(['success' => false, 'message' => 'Invalid action']);
        }
    }

    private function renderStore() {
        try {
            // Fetch products
            $products = $this->product->getAll();
            error_log('Products fetched in renderStore: ' . print_r($products, true));
    
            // Check if products are populated
            if (empty($products)) {
                error_log('No products fetched from the database.');
            }
    
            // Other data for the view
            $greeting = $this->getRandomClerkMessage('greetings');
            $cartItems = $this->cart->getCartItems($_SESSION['user_id']);
            error_log('Cart items: ' . print_r($cartItems, true));
    
            // Include the main view and pass the $products variable
            require_once __DIR__ . '/../views/main.php';
        } catch (Exception $e) {
            error_log('Error in renderStore: ' . $e->getMessage());
            echo 'Error loading store.';
        }
    }

    private function getProducts() {
        try {
            $products = $this->product->getAll();
            error_log('Products (AJAX): ' . print_r($products, true));
            $this->sendResponse([
                'success' => true,
                'products' => $products
            ]);
        } catch (Exception $e) {
            error_log('Error getting products: ' . $e->getMessage());
            $this->sendResponse([
                'success' => false,
                'message' => 'Error loading products'
            ]);
        }
    }

    private function addToCart() {
        try {
            // Get JSON input
            $input = json_decode(file_get_contents('php://input'), true);
            
            // Log the received data
            error_log('Received addToCart request: ' . print_r($input, true));
            
            $productId = $input['product_id'] ?? null;
            $quantity = $input['quantity'] ?? 1;

            if (!$productId) {
                throw new Exception('Product ID is required');
            }

            if (!isset($_SESSION['user_id'])) {
                throw new Exception('User must be logged in');
            }

            $userId = $_SESSION['user_id'];

            // Check if product exists and has stock
            $product = $this->product->getById($productId);
            if (!$product) {
                throw new Exception('Product not found');
            }

            if ($product['stock'] < $quantity) {
                throw new Exception('Not enough stock available');
            }

            // Add to cart
            $success = $this->cart->addItem($userId, $productId, $quantity);

            if ($success) {
                $message = $this->getRandomClerkMessage('addToCart');
                $this->sendResponse([
                    'success' => true,
                    'message' => 'Product added to cart successfully',
                    'clerkMessage' => $message
                ]);
            } else {
                throw new Exception('Failed to add item to cart');
            }
        } catch (Exception $e) {
            error_log('Error in addToCart: ' . $e->getMessage());
            $this->sendResponse([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    private function removeFromCart() {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            error_log('Remove cart data: ' . print_r($input, true)); // Debug log
            
            if (!isset($_SESSION['user_id'])) {
                throw new Exception('User must be logged in');
            }

            $itemId = $input['item_id'] ?? null;
            if (!$itemId) {
                throw new Exception('Cart Item ID is required');
            }

            $success = $this->cart->removeItem($_SESSION['user_id'], $itemId);

            if ($success) {
                $this->sendResponse([
                    'success' => true,
                    'message' => 'Item removed from cart'
                ]);
            } else {
                throw new Exception('Failed to remove item from cart');
            }
        } catch (Exception $e) {
            error_log('Error in removeFromCart: ' . $e->getMessage()); // Debug log
            $this->sendResponse([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    private function processCheckout() {
        try {
            if (!isset($_SESSION['user_id'])) {
                throw new Exception('User must be logged in');
            }
    
            $input = json_decode(file_get_contents('php://input'), true);
            $total = $input['total'] ?? 0;
    
            // Get user's current balance
            $stmt = $this->db->prepare("SELECT money FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $currentBalance = $stmt->fetchColumn();
    
            if ($currentBalance < $total) {
                throw new Exception('Insufficient funds');
            }
    
            // Start transaction
            $this->db->beginTransaction();
    
            try {
                // Update user balance
                $newBalance = $currentBalance - $total;
                $stmt = $this->db->prepare("UPDATE users SET money = ? WHERE id = ?");
                $stmt->execute([$newBalance, $_SESSION['user_id']]);
    
                // Clear the user's cart
                $stmt = $this->db->prepare("
                    DELETE ci FROM cart_items ci
                    JOIN carts c ON ci.cart_id = c.id
                    WHERE c.user_id = ?
                ");
                $stmt->execute([$_SESSION['user_id']]);
    
                $this->db->commit();
    
                $this->sendResponse([
                    'success' => true,
                    'message' => 'Checkout successful',
                    'newBalance' => $newBalance
                ]);
            } catch (Exception $e) {
                $this->db->rollBack();
                throw $e;
            }
        } catch (Exception $e) {
            $this->sendResponse([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    private function getClerkMessage() {
        try {
            $type = $_POST['type'] ?? 'greetings';
            $message = $this->getRandomClerkMessage($type);
            $this->sendResponse([
                'success' => true,
                'message' => $message
            ]);
        } catch (Exception $e) {
            $this->sendResponse([
                'success' => false,
                'message' => 'Error getting clerk message'
            ]);
        }
    }

    private function getRandomClerkMessage($type) {
        if (!isset($this->clerkResponses[$type])) {
            return '';
        }

        $messages = $this->clerkResponses[$type];
        $message = $messages[array_rand($messages)];
        return str_replace('{username}', $_SESSION['name'] ?? 'Shopper', $message);
    }

    private function sendResponse($data) {
        if (headers_sent()) {
            error_log('Headers already sent in StoreController');
        }
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }

    private function updateQuantity() {
        try {
            if (!isset($_SESSION['user_id'])) {
                throw new Exception('User must be logged in');
            }
    
            $input = json_decode(file_get_contents('php://input'), true);
            $itemId = $input['item_id'] ?? null;
            $change = $input['change'] ?? 0;
    
            if (!$itemId) {
                throw new Exception('Item ID is required');
            }
    
            // Add this method to your Cart class if not already present
            $success = $this->cart->updateQuantity($_SESSION['user_id'], $itemId, $change);
    
            if ($success) {
                $this->sendResponse([
                    'success' => true,
                    'message' => 'Quantity updated successfully'
                ]);
            } else {
                throw new Exception('Failed to update quantity');
            }
        } catch (Exception $e) {
            $this->sendResponse([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    private function addMoney() {
        try {
            if (!isset($_SESSION['user_id'])) {
                throw new Exception('User must be logged in');
            }
    
            $input = json_decode(file_get_contents('php://input'), true);
            $amount = $input['amount'] ?? 0;
    
            if (!is_numeric($amount) || $amount <= 0) {
                throw new Exception('Invalid amount');
            }
    
            // Start transaction
            $this->db->beginTransaction();
    
            try {
                // Get current money amount
                $stmt = $this->db->prepare("SELECT money FROM users WHERE id = ?");
                $stmt->execute([$_SESSION['user_id']]);
                $currentMoney = $stmt->fetchColumn();
    
                // Calculate new money amount
                $newMoney = $currentMoney + $amount;
    
                // Update user money
                $stmt = $this->db->prepare("UPDATE users SET money = ? WHERE id = ?");
                $stmt->execute([$newMoney, $_SESSION['user_id']]);
    
                $this->db->commit();
    
                // Update session with new money amount
                $_SESSION['money'] = $newMoney;
    
                $this->sendResponse([
                    'success' => true,
                    'message' => 'Money added successfully',
                    'newBalance' => $newMoney // keep this as newBalance for frontend compatibility
                ]);
            } catch (Exception $e) {
                $this->db->rollBack();
                throw $e;
            }
        } catch (Exception $e) {
            $this->sendResponse([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}

// Handle request if accessed directly
if (basename($_SERVER['PHP_SELF']) == 'StoreController.php') {
    $controller = new StoreController();
    $controller->handleRequest();
}