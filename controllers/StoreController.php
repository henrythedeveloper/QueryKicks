<?php
session_start();
$productPath = __DIR__ . '/../models/Product.php';
$dbPath = __DIR__ . '/../config/database.php';
$cart = __DIR__ . '/../models/Cart.php';
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
        $jsonPath = __DIR__ . '/../data/clerk-messages.json';
        if (file_exists($jsonPath)) {
            $json = file_get_contents($jsonPath);
            $this->clerkResponses = json_decode($json, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                error_log('JSON Decode Error: ' . json_last_error_msg());
                $this->clerkResponses = [];
            }
        } else {
            error_log('Clerk messages file not found at: ' . $jsonPath);
            $this->clerkResponses = [];
        }
    }

    public function handleRequest() {
        // Check authentication
        if (!isset($_SESSION['user_id'])) {
            header('Location: /querykicks/controllers/AuthController.php');
            exit();
        }
    
        // Handle AJAX requests from POST data
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            $action = $_POST['action'] ?? $input['action'] ?? null;
    
            if ($action) {
                $this->handleAction($action);
                return;
            }
        }
    
        // Handle AJAX requests from GET data
        if (isset($_GET['action'])) {
            $action = $_GET['action'];
            $this->handleAction($action);
            return;
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
            case 'getCart':
                $this->getCart();
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
    
            // Other data for the view
            $greeting = $this->getRandomClerkMessage('greetings');
            $cartItems = $this->cart->getCartItems($_SESSION['user_id']);
            error_log('Clerk Responses: ' . print_r($this->clerkResponses, true));
            $clerkMessagesJson = json_encode($this->clerkResponses); // Encode messages as JSON
            error_log('Clerk Messages JSON: ' . $clerkMessagesJson);

    
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

    private function getCart() {
        try {
            if (!isset($_SESSION['user_id'])) {
                throw new Exception('User must be logged in');
            }
    
            // Fetch cart items
            $cartItems = $this->cart->getCartItems($_SESSION['user_id']);
    
            // Start output buffering
            ob_start();
    
            // Include the cart view template
            include __DIR__ . '/../views/store/cart.php';
    
            // Get the buffered content
            $html = ob_get_clean();
    
            $this->sendResponse([
                'success' => true,
                'html' => $html
            ]);
        } catch (Exception $e) {
            $this->sendResponse([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    

    private function addToCart() {
        try {
            if (!isset($_SESSION['user_id'])) {
                throw new Exception('User must be logged in');
            }
    
            $input = json_decode(file_get_contents('php://input'), true);
            $productId = $input['product_id'] ?? null;
            $quantity = $input['quantity'] ?? 1;
    
            if (!$productId || $quantity <= 0) {
                throw new Exception('Invalid product or quantity.');
            }
    
            // Fetch the product to get the current stock
            $product = $this->product->getProductById($productId);
            if (!$product) {
                throw new Exception('Product not found.');
            }
    
            // Check if the requested quantity exceeds the stock
            if ($quantity > $product['stock']) {
                throw new Exception('Requested quantity exceeds available stock.');
            }
    
            // Check if the cart already contains this product
            $existingCartItem = $this->cart->getCartItemByProductId($_SESSION['user_id'], $productId);
            $newQuantity = $quantity;
    
            if ($existingCartItem) {
                // Calculate the new quantity
                $newQuantity += $existingCartItem['quantity'];
    
                // Check if the new quantity exceeds the stock
                if ($newQuantity > $product['stock']) {
                    throw new Exception('Total quantity in cart exceeds available stock.');
                }
    
                // Update the quantity in the cart
                $this->cart->updateQuantity($_SESSION['user_id'], $existingCartItem['id'], $newQuantity);
            } else {
                // Add the product to the cart
                $this->cart->addItem($_SESSION['user_id'], $productId, $quantity);
            }
    
            $this->sendResponse([
                'success' => true,
                'message' => 'Item added to cart.'
            ]);
        } catch (Exception $e) {
            $this->sendResponse([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    
    private function removeFromCart() {
        try {
            if (!isset($_SESSION['user_id'])) {
                throw new Exception('User must be logged in');
            }
    
            $cartItemId = $_POST['cart_item_id'] ?? null;
    
            if (!$cartItemId) {
                throw new Exception('Cart Item ID is required');
            }
    
            $success = $this->cart->removeItem($_SESSION['user_id'], $cartItemId);
    
            if ($success) {
                $this->sendResponse([
                    'success' => true,
                    'message' => 'Item removed from cart'
                ]);
            } else {
                throw new Exception('Failed to remove item from cart');
            }
        } catch (Exception $e) {
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
    
                // Get cart items before clearing the cart
                $stmt = $this->db->prepare("
                    SELECT ci.product_id, ci.quantity
                    FROM cart_items ci
                    JOIN carts c ON ci.cart_id = c.id
                    WHERE c.user_id = ?
                ");
                $stmt->execute([$_SESSION['user_id']]);
                $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
                // Update product stock
                foreach ($cartItems as $item) {
                    $productId = $item['product_id'];
                    $quantityPurchased = $item['quantity'];
    
                    // Update the stock
                    $stmt = $this->db->prepare("
                        UPDATE products
                        SET stock = stock - ?
                        WHERE id = ?
                    ");
                    $stmt->execute([$quantityPurchased, $productId]);
                }
    
                // Clear the user's cart
                $stmt = $this->db->prepare("
                    DELETE ci FROM cart_items ci
                    JOIN carts c ON ci.cart_id = c.id
                    WHERE c.user_id = ?
                ");
                $stmt->execute([$_SESSION['user_id']]);
    
                // Commit the transaction
                $this->db->commit();
    
                // Update session with new balance
                $_SESSION['money'] = $newBalance;
    
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
    
            $this->cart->updateQuantity($_SESSION['user_id'], $itemId, $change);
    
            $this->sendResponse([
                'success' => true,
                'message' => 'Quantity updated successfully'
            ]);
    
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

// Handle request
$controller = new StoreController();
$controller->handleRequest();