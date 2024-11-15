# Project Architecture Documentation

## Core System Design

### Base Controller (src/Core/Controller.php)
```php
/**
 * Abstract base controller providing common functionality for all controllers
 * 
 * Benefits:
 * - Reduces code duplication
 * - Standardizes response handling
 * - Centralizes common operations
 */
abstract class Controller {
    /**
     * Renders a view with provided data
     *
     * @param string $view  Path to the view file (without .php extension)
     * @param array  $data  Data to be passed to the view
     */
    protected function render(string $view, array $data = []): void {
        // extract() creates variables from array keys
        // Example: ['user' => 'John'] becomes $user = 'John'
        extract($data);
        
        require "views/{$view}.php";
    }

    /**
     * Safely redirects to another page
     *
     * @param string $path URL to redirect to
     */
    protected function redirect(string $path): void {
        header("Location: {$path}");
        exit; // Prevents further code execution after redirect
    }

    /**
     * Validates request data
     *
     * @param array $data Request data to validate
     * @param array $rules Validation rules
     * @throws ValidationException
     */
    protected function validate(array $data, array $rules): void {
        $errors = [];
        
        foreach ($rules as $field => $rule) {
            if (!isset($data[$field]) && strpos($rule, 'required') !== false) {
                $errors[$field] = "The {$field} field is required";
            }
        }

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
    }
}

/**
 * Example implementation showing how to use the base controller
 */
class ProductController extends Controller {
    private ProductModel $model;
    
    public function __construct() {
        $this->model = new ProductModel();
    }

    /**
     * Display product listing page
     * Shows how to use render() with data
     */
    public function index(): void {
        try {
            $products = $this->model->getProducts();
            $this->render('store/products', [
                'products' => $products,
                'title' => 'Our Products'
            ]);
        } catch (Exception $e) {
            // Error handling
            $this->render('error', ['message' => $e->getMessage()]);
        }
    }

    /**
     * Create new product
     * Shows validation and redirect usage
     */
    public function create(): void {
        try {
            $this->validate($_POST, [
                'name' => 'required',
                'price' => 'required|numeric'
            ]);

            $this->model->createProduct($_POST);
            $this->redirect('/products');
        } catch (ValidationException $e) {
            $this->render('store/create', ['errors' => $e->getErrors()]);
        }
    }
}
```

### Project Structure Explanation

```plaintext
project/
├── src/                    # Source code directory
│   ├── Core/              # Core framework classes
│   │   ├── Database.php   # Database connection handling
│   │   └── Controller.php # Base controller class
│   ├── Controllers/       # Application controllers
│   ├── Models/           # Data models
│   └── Services/         # Business logic services
├── config/               # Configuration files
├── public/              # Web root directory
└── composer.json        # Dependencies and autoloading
```

### Key Benefits
1. **Separation of Concerns**
   - Each class has a single responsibility
   - Code is more organized and maintainable
   - Easier to test and debug

2. **Code Reusability**
   - Base controller provides common functionality
   - Avoid duplicating code across controllers
   - Consistent handling of views and redirects

3. **Error Handling**
   - Centralized error handling
   - Better user experience
   - Easier debugging

4. **Security**
   - Input validation
   - Safe redirects
   - Protection against common vulnerabilities

### Usage Example
```php
// Example route handling
$controller = new ProductController();
$action = $_GET['action'] ?? 'index';

try {
    $controller->$action();
} catch (Exception $e) {
    // Handle errors
}
```

Would you like me to explain any specific part in more detail or move on to the next improvement?
