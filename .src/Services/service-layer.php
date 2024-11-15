```php
// src/Services/BaseService.php
abstract class BaseService {
    protected function validateData(array $data, array $rules): void {
        $validator = new Validator($data, $rules);
        if (!$validator->passes()) {
            throw new ValidationException($validator->getErrors());
        }
    }
}

// src/Services/ProductService.php
class ProductService extends BaseService {
    private ProductModel $productModel;
    private ImageUploader $imageUploader;

    public function __construct() {
        $this->productModel = new ProductModel();
        $this->imageUploader = new ImageUploader();
    }

    public function createProduct(array $data): array {
        $this->validateData($data, [
            'name' => 'required|string',
            'price' => 'required|numeric',
            'image' => 'required|image'
        ]);

        if (isset($_FILES['image'])) {
            $data['image_url'] = $this->imageUploader->upload($_FILES['image']);
        }

        return $this->productModel->create($data);
    }

    public function getProducts(array $filters = []): array {
        return $this->productModel->all($filters);
    }
}

// src/Services/CartService.php
class CartService extends BaseService {
    private CartModel $cartModel;
    private ProductModel $productModel;

    public function addToCart(int $userId, int $productId, int $quantity): void {
        $product = $this->productModel->find($productId);
        if (!$product) {
            throw new NotFoundException('Product not found');
        }

        Database::beginTransaction();
        try {
            $this->cartModel->addItem($userId, $productId, $quantity);
            Database::commit();
        } catch (Exception $e) {
            Database::rollback();
            throw $e;
        }
    }

    public function checkout(int $userId): void {
        $cart = $this->cartModel->getUserCart($userId);
        if (empty($cart)) {
            throw new ValidationException('Cart is empty');
        }

        Database::beginTransaction();
        try {
            // Create order
            $orderId = $this->orderModel->create([
                'user_id' => $userId,
                'total' => $this->cartModel->getCartTotal($userId)
            ]);

            // Add order items
            foreach ($cart as $item) {
                $this->orderItemModel->create([
                    'order_id' => $orderId,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price']
                ]);
            }

            // Clear cart
            $this->cartModel->clearCart($userId);

            Database::commit();
        } catch (Exception $e) {
            Database::rollback();
            throw $e;
        }
    }
}
```

This service layer:
1. Handles business logic
2. Manages transactions
3. Validates input
4. Coordinates between models
5. Handles file uploads

Want me to show the UserService or continue with the next layer?