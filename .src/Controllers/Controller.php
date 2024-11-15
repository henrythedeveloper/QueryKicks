// src/Core/Controller.php
abstract class Controller {
    protected function render(string $view, array $data = []): void {
        extract($data);
        require "views/{$view}.php";
    }

    protected function redirect(string $path): void {
        header("Location: {$path}");
        exit;
    }
}

// Example usage in ProductController
class ProductController extends Controller {
    private ProductModel $model;

    public function __construct() {
        $this->model = new ProductModel();
    }

    public function index(): void {
        $products = $this->model->getProducts();
        $this->render('store/main', ['products' => $products]);
    }
}