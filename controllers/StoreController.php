<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Cart.php';

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
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check authentication
        if (!isset($_SESSION['user_id'])) {
            header('Location: /querykicks/views/auth.php');
            exit();
        }

        // Handle AJAX requests
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
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
            case 'checkout':
                $this->processCheckout();
                break;
            case 'getClerkMessage':
                $this->getClerkMessage();
                break;
            default:
                $this->sendResponse(['success' => false, 'message' => 'Invalid action']);
        }
    }

    private function renderStore() {
        try {
            // Get products
            $products = $this->product->getAll();
            error_log('Products fetched: ' . print_r($products, true));

            // Get cart items
            $cartItems = $this->cart->getCartItems($_SESSION['user_id']);

            // Get greeting
            $greeting = $this->getRandomClerkMessage('greetings');

            // Get user data
            $userData = [
                'id' => $_SESSION['user_id'],
                'name' => $_SESSION['name'] ?? 'Shopper',
                'balance' => $_SESSION['money'] ?? 0,
                'role' => $_SESSION['role'] ?? 'user'
            ];

            // Include the view
            require_once __DIR__ . '/../views/store/main.php';
        } catch (Exception $e) {
            error_log('Error rendering store: ' . $e->getMessage());
            echo 'Error loading store';
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
            $productId = $_POST['product_id'] ?? null;
            $quantity = $_POST['quantity'] ?? 1;

            if (!$productId) {
                throw new Exception('Product ID is required');
            }

            $product = $this->product->getById($productId);
            if (!$product) {
                throw new Exception('Product not found');
            }

            // Check stock
            if ($product['stock'] < $quantity) {
                throw new Exception('Not enough stock available');
            }

            // Add to cart
            $success = $this->cart->addItem($_SESSION['user_id'], $productId, $quantity);

            if ($success) {
                $message = $this->getRandomClerkMessage('addToCart');
                $this->sendResponse([
                    'success' => true,
                    'message' => 'Product added to cart',
                    'clerkMessage' => $message
                ]);
            } else {
                throw new Exception('Failed to add to cart');
            }
        } catch (Exception $e) {
            $this->sendResponse([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    private function removeFromCart() {
        try {
            $itemId = $_POST['cart_item_id'] ?? null;
            if (!$itemId) {
                throw new Exception('Cart item ID is required');
            }

            $success = $this->cart->removeItem($_SESSION['user_id'], $itemId);
            
            if ($success) {
                $message = $this->getRandomClerkMessage('removeFromCart');
                $this->sendResponse([
                    'success' => true,
                    'message' => 'Item removed from cart',
                    'clerkMessage' => $message
                ]);
            } else {
                throw new Exception('Failed to remove item');
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
            $userId = $_SESSION['user_id'];
            $cartItems = $this->cart->getCartItems($userId);

            if (empty($cartItems)) {
                throw new Exception('Cart is empty');
            }

            // Calculate total
            $total = array_reduce($cartItems, function($sum, $item) {
                return $sum + ($item['price'] * $item['quantity']);
            }, 0);

            // Create order
            $orderId = $this->cart->createOrder($userId, $total);

            if ($orderId) {
                $message = $this->getRandomClerkMessage('checkout');
                $this->sendResponse([
                    'success' => true,
                    'message' => 'Order processed successfully',
                    'clerkMessage' => $message,
                    'orderId' => $orderId
                ]);
            } else {
                throw new Exception('Failed to process order');
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
}

// Handle request if accessed directly
if (basename($_SERVER['PHP_SELF']) == 'StoreController.php') {
    $controller = new StoreController();
    $controller->handleRequest();
}