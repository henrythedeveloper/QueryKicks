```php
// src/Controllers/BaseController.php
class BaseController {
    protected function json($data, int $status = 200): void {
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode($data);
    }

    protected function view(string $template, array $data = []): void {
        extract($data);
        require_once "views/$template.php";
    }
}

// src/Controllers/ProductController.php
class ProductController extends BaseController {
    private ProductService $productService;

    public function __construct() {
        $this->productService = new ProductService();
    }

    public function index(): void {
        try {
            $products = $this->productService->getProducts();
            $this->view('store/products', ['products' => $products]);
        } catch (Exception $e) {
            $this->view('error', ['message' => $e->getMessage()]);
        }
    }

    public function store(): void {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }

            $product = $this->productService->createProduct($_POST);
            $this->json(['message' => 'Product created', 'product' => $product]);
        } catch (ValidationException $e) {
            $this->json(['errors' => $e->getErrors()], 422);
        }
    }
}

// src/Controllers/CartController.php
class CartController extends BaseController {
    private CartService $cartService;

    public function __construct() {
        $this->cartService = new CartService();
    }

    public function add(): void {
        try {
            $this->cartService->addToCart(
                $_SESSION['user_id'],
                (int)$_POST['product_id'],
                (int)($_POST['quantity'] ?? 1)
            );
            $this->json(['message' => 'Product added to cart']);
        } catch (Exception $e) {
            $this->json(['error' => $e->getMessage()], 400);
        }
    }

    public function checkout(): void {
        try {
            $this->cartService->checkout($_SESSION['user_id']);
            $this->json(['message' => 'Order placed successfully']);
        } catch (Exception $e) {
            $this->json(['error' => $e->getMessage()], 400);
        }
    }
}
```

The key improvements here are:
1. Error handling
2. Input validation
3. Service integration
4. Response formatting
5. Session management

Would you like to see the implementation of another controller or move to setting up the routing system?