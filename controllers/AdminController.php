<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Debug file paths
$productPath = __DIR__ . '/../models/Product.php';
$dbPath = __DIR__ . '/../config/database.php';

error_log("Product.php path: " . $productPath);
error_log("Database.php path: " . $dbPath);
error_log("Product.php exists: " . (file_exists($productPath) ? 'yes' : 'no'));
error_log("Database.php exists: " . (file_exists($dbPath) ? 'yes' : 'no'));

require_once $dbPath;
require_once $productPath;

class AdminController {
    private $db;
    private $product;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->product = new Product($this->db);
    }

    public function handleRequest() {

        // Debug session info
        error_log('Session data: ' . print_r($_SESSION, true));
        error_log('POST data: ' . print_r($_POST, true));


        // Check if user is admin
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            $this->sendResponse(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }

        // Get request data
        $input = json_decode(file_get_contents('php://input'), true);
        $action = $_POST['action'] ?? $input['action'] ?? '';

        try {
            switch ($action) {
                case 'getDashboardData':
                    $this->getDashboardData();
                    break;
                case 'getProducts':
                    $this->getProducts();
                    break;
                case 'getProduct':
                    $id = isset($_POST['id']) ? $_POST['id'] : (isset($input['id']) ? $input['id'] : null);
                    if ($id) {
                        $this->getProduct($id);
                    } else {
                        $this->sendResponse(['success' => false, 'message' => 'Product ID required']);
                    }
                    break;
                case 'addProduct':
                    $this->addProduct();
                    break;
                case 'updateProduct':
                    $this->updateProduct();
                    break;
                case 'deleteProduct':
                    $id = isset($_POST['id']) ? $_POST['id'] : (isset($input['id']) ? $input['id'] : null);
                    if ($id) {
                        $this->deleteProduct($id);
                    } else {
                        $this->sendResponse(['success' => false, 'message' => 'Product ID required']);
                    }
                    break;
                case 'getOrders':
                    $this->getOrders();
                    break;
                case 'getOrderDetails':
                    $this->getOrderDetails($_POST['orderId']);
                    break;
                case 'updateOrderStatus':
                    $this->updateOrderStatus();
                    break;
                case 'getUsers':
                    $this->getUsers();
                    break;
                case 'addUserMoney':
                    $this->addUserMoney();
                    break;
                default:
                    $this->sendResponse([
                        'success' => false,
                        'message' => 'Invalid action',
                        'received_action' => $action
                    ]);
            }
        } catch (Exception $e) {
            error_log('Error in handleRequest: ' . $e->getMessage());
            $this->sendResponse([
                'success' => false,
                'message' => 'Server error',
                'error' => $e->getMessage()
            ]);
        }
    }

    private function getDashboardData() {
        try {
            // Get total products
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM products");
            $totalProducts = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            // Get total orders
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM orders");
            $totalOrders = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            // Get total users
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM users WHERE role = 'user'");
            $totalUsers = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            $responseData = [
                'success' => true,
                'totalProducts' => $totalProducts,
                'totalOrders' => $totalOrders,
                'totalUsers' => $totalUsers
            ];

            error_log('Dashboard response: ' . print_r($responseData, true));
            $this->sendResponse($responseData);

        } catch (PDOException $e) {
            error_log('Database error in getDashboardData: ' . $e->getMessage());
            $this->sendResponse([
                'success' => false,
                'message' => 'Database error',
                'error' => $e->getMessage()
            ]);
        }
    }

    private function getProducts() {
        try {
            $stmt = $this->db->query("SELECT * FROM products ORDER BY created_at DESC");
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            error_log('Products response: ' . print_r($products, true));
            $this->sendResponse($products);

        } catch (PDOException $e) {
            error_log('Database error in getProducts: ' . $e->getMessage());
            $this->sendResponse([
                'success' => false,
                'message' => 'Database error',
                'error' => $e->getMessage()
            ]);
        }
    }

    private function getProduct($id) {
        try {
            $query = "SELECT * FROM products WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($product) {
                $this->sendResponse([
                    'success' => true,
                    'product' => $product
                ]);
            } else {
                $this->sendResponse([
                    'success' => false,
                    'message' => 'Product not found'
                ]);
            }
        } catch (PDOException $e) {
            $this->sendResponse([
                'success' => false,
                'message' => 'Database error',
                'error' => $e->getMessage()
            ]);
        }
    }

    private function getOrders() {
        try {
            $query = "SELECT o.*, u.name as user_name 
                     FROM orders o 
                     JOIN users u ON o.user_id = u.id 
                     ORDER BY o.created_at DESC";
            $stmt = $this->db->query($query);
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->sendResponse($orders);
        } catch (PDOException $e) {
            $this->sendResponse([
                'success' => false,
                'message' => 'Database error',
                'error' => $e->getMessage()
            ]);
        }
    }

    private function getOrderDetails($orderId) {
        try {
            // Get order details
            $query = "SELECT oi.*, p.name as product_name, p.image_url 
                     FROM order_items oi
                     JOIN products p ON oi.product_id = p.id
                     WHERE oi.order_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$orderId]);
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $this->sendResponse([
                'success' => true,
                'items' => $items
            ]);
        } catch (PDOException $e) {
            $this->sendResponse([
                'success' => false,
                'message' => 'Database error',
                'error' => $e->getMessage()
            ]);
        }
    }

    private function getUsers() {
        try {
            $query = "SELECT id, name, email, money, role, created_at FROM users ORDER BY created_at DESC";
            $stmt = $this->db->query($query);
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->sendResponse($users);
        } catch (PDOException $e) {
            $this->sendResponse([
                'success' => false,
                'message' => 'Database error',
                'error' => $e->getMessage()
            ]);
        }
    }

    private function logout() {
        try {
            session_start();
            session_destroy();
            $this->sendResponse([
                'success' => true,
                'message' => 'Logged out successfully'
            ]);
        } catch (Exception $e) {
            $this->sendResponse([
                'success' => false,
                'message' => 'Error logging out: ' . $e->getMessage()
            ]);
        }
    }

    private function updateOrderStatus() {
        try {
            $orderId = $_POST['orderId'];
            $status = $_POST['status'];

            $query = "UPDATE orders SET status = ? WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $success = $stmt->execute([$status, $orderId]);

            if ($success) {
                $this->sendResponse([
                    'success' => true,
                    'message' => 'Order status updated successfully'
                ]);
            } else {
                throw new Exception('Failed to update order status');
            }
        } catch (Exception $e) {
            $this->sendResponse([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    private function uploadImage($file) {
        // Get the original file name
        $fileName = basename($file['name']);
        
        // Set target directory and path
        $targetDir = $_SERVER['DOCUMENT_ROOT'] . '/querykicks/assets/images/shoes/';
        $targetPath = $targetDir . $fileName;
        
        // Debug
        error_log('Moving file to: ' . $targetPath);
        
        // Move the file
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return 'assets/images/shoes/' . $fileName;
        } else {
            throw new Exception('Failed to move uploaded file');
        }
    }

    private function addProduct() {
        try {
            // Debug incoming data
            error_log('POST data: ' . print_r($_POST, true));
            error_log('FILES data: ' . print_r($_FILES, true));
    
            if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('No image file uploaded or upload error occurred.');
            }
    
            // Upload image and get the path
            $imageUrl = $this->uploadImage($_FILES['image']);
            error_log('Image URL for database: ' . $imageUrl);
    
            // Insert product data
            $query = "INSERT INTO products (name, description, price, stock, image_url) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($query);
            $success = $stmt->execute([
                $_POST['name'],
                $_POST['description'],
                $_POST['price'],
                $_POST['stock'],
                $imageUrl 
            ]);
    
            if ($success) {
                $this->sendResponse([
                    'success' => true, 
                    'message' => 'Product added successfully',
                    'imageUrl' => $imageUrl
                ]);
            } else {
                throw new Exception('Failed to add product to database.');
            }
        } catch (Exception $e) {
            error_log('Add product error: ' . $e->getMessage());
            $this->sendResponse([
                'success' => false, 
                'message' => $e->getMessage()
            ]);
        }
    }

    private function addUserMoney() {
        try {
            $userId = $_POST['userId'];
            $amount = floatval($_POST['amount']);

            if ($amount <= 0) {
                throw new Exception('Amount must be greater than 0');
            }

            $query = "UPDATE users SET money = money + ? WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $success = $stmt->execute([$amount, $userId]);

            if ($success) {
                $this->sendResponse([
                    'success' => true,
                    'message' => 'Money added successfully'
                ]);
            } else {
                throw new Exception('Failed to add money');
            }
        } catch (Exception $e) {
            $this->sendResponse([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    private function updateProduct() {
        try {
            $imageUrl = null;
            
            // Handle image upload if new image is provided
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $imageUrl = $this->uploadImage($_FILES['image']);
            }

            // Update query
            if ($imageUrl) {
                $query = "UPDATE products SET name = ?, description = ?, price = ?, stock = ?, image_url = ? WHERE id = ?";
                $params = [
                    $_POST['name'],
                    $_POST['description'],
                    $_POST['price'],
                    $_POST['stock'],
                    $imageUrl,
                    $_POST['id']
                ];
            } else {
                $query = "UPDATE products SET name = ?, description = ?, price = ?, stock = ? WHERE id = ?";
                $params = [
                    $_POST['name'],
                    $_POST['description'],
                    $_POST['price'],
                    $_POST['stock'],
                    $_POST['id']
                ];
            }

            $stmt = $this->db->prepare($query);
            $success = $stmt->execute($params);

            if ($success) {
                $this->sendResponse(['success' => true, 'message' => 'Product updated successfully']);
            } else {
                throw new Exception('Failed to update product in database.');
            }
        } catch (Exception $e) {
            $this->sendResponse(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    private function deleteProduct($id) {
        try {
            $query = "DELETE FROM products WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $success = $stmt->execute([$id]);
            
            if ($success) {
                $this->sendResponse(['success' => true, 'message' => 'Product deleted successfully']);
            } else {
                throw new Exception('Failed to delete product from database.');
            }
        } catch (Exception $e) {
            $this->sendResponse(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    private function sendResponse($data) {
        // Clear any previous output
        if (ob_get_length()) ob_clean();
        
        // Ensure no errors are output
        error_reporting(0);
        
        // Set headers
        header('Content-Type: application/json');
        header('Cache-Control: no-cache, must-revalidate');
        
        // Debug response
        error_log('Sending JSON response: ' . print_r($data, true));
        
        // Encode and send without escaping slashes
        echo json_encode($data, JSON_UNESCAPED_SLASHES);
        exit();
    }
}

// Handle request if accessed directly
if (basename($_SERVER['PHP_SELF']) == 'AdminController.php') {
    $controller = new AdminController();
    $controller->handleRequest();
}